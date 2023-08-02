<?php

namespace Develodesign\Punchout\Service;

use Develodesign\Punchout\Exceptions\CxmlDocumentLoadingException;
use Magento\Framework\Simplexml\Element;
use RuntimeException;
use SimpleXMLElement;

class CxmlService
{
    /**
     * Parse request body to Xml string
     */
    public function parseXmlResponse($rawRequestBody) : SimpleXMLElement
    {
        $libxml = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($rawRequestBody, Element::class);
        if ($xml === false) {
            libxml_use_internal_errors($libxml);
            throw new RuntimeException(sprintf(
                "Unable to parse cXML Setup Request %s",
                trim(libxml_get_last_error()->message)
            ));
        }
        return $xml;
    }

    /**
     * Validation of incoming CXML data
     */
    public function validateSetupRequest(Element $cxmlData): array
    {
        $errors = ['error' => false];
        if ($cxmlData->getAttribute('payloadID') === null) {
            $errors[]['payloadID'] = 'PayloadId element is required to perform this action';
            $errors['error'] = true;
        }
        if (!$cxmlData->Header->Sender->Credential->SharedSecret) {
            $errors[]['sharedSecret'] = 'SharedSecret element is required to perform this action';
            $errors['error'] = true;
        }
        if (!$cxmlData->Header->Sender->Credential->Identity) {
            $errors[]['identity'] = 'Sender Credential Identity element is required to perform this action';
            $errors['error'] = true;
        }
        if (!$cxmlData->Request->PunchOutSetupRequest->BuyerCookie) {
            $errors[]['buyerCookie'] = 'BuyerCookie is required to perform this action';
            $errors['error'] = true;
        }
        if (!$cxmlData->Request->PunchOutSetupRequest->getAttribute('operation')) {
            $errors[]['operation'] = 'PunchOutSetupRequest operation is required to perform this action';
            $errors['error'] = true;
        }
        if (!$cxmlData->Request->PunchOutSetupRequest->Extrinsic) {
            $errors[]['extrinsic'] = 'Extrinsic element is required to perform this action';
            $errors['error'] = true;
        }
        if (!$cxmlData->Request->PunchOutSetupRequest->BrowserFormPost->URL) {
            $errors[]['url'] = 'BrowserFormPost URL element is required to perform this action';
            $errors['error'] = true;
        }
        return $errors;
    }

    /**
     * Get Ariba Network ID from incoming Cxml Data
     */
    public function getAribaNetworkId($cxmlData): ?string
    {
        $aribaNetworkId = false;
        $domain = (string)$cxmlData->Header->Sender->Credential['domain'];
        if (isset($cxmlData->Header->Sender->Credential['domain'])
            && stripos(strtolower($domain), 'AribaNetwork') !== false) {
            $aribaNetworkId = (string)$cxmlData->Header->From->Credential->Identity;
        }
        if (isset($cxmlData->Header->To->Credential['domain'])) {
            $domain = strtolower((string)$cxmlData->Header->To->Credential['domain']);
            if (stripos($domain, 'transactionnetworkId') !== false) {
                $aribaNetworkId = (string)$cxmlData->Header->From->Credential->Identity;
            }
        }
        return $aribaNetworkId;
    }

    /**
     * Get ExtrinsicData array from Xml object
     */
    public function getExtrinsicData($extrinsic): array
    {
        $result = [];
        foreach ($extrinsic as $ext) {
            $key = (string)$ext->getAttribute('name');
            $value = (string)$ext;
            $result[$key] = $value;
        }
        return $result;
    }

    /**
     * Check if incoming cxml data request is create operation
     */
    public function isCreate($cxmlData): bool
    {
        return $cxmlData->Request->PunchOutSetupRequest->getAttribute('operation') === 'create';
    }

    /**
     * Validates email from Incoming cxml data
     * @throws \Zend_Validate_Exception
     */
    public function validateEmail($xpathSelector): string
    {
        $useEmail = '';
        if (!empty($xpathSelector)) {
    
            if ($xpathSelector === 'none') {
                return $useEmail;
            }
            // $xpathSelector will be an array of SimpleXMLElement objects
            // In this case, we expect only one result, so we access the first element
            $email = (string)$xpathSelector[0];
            if(\Zend_Validate::is($email, 'EmailAddress')){
                $useEmail = $email;
            }
        }
        return $useEmail;
       
    }
    
    /**
     * Create usable email from punchout group domain and cxml data
     * @throws \Zend_Validate_Exception
     */
    public function createEmail($extrinsicData, $cxmlData, $punchoutGroupEmail): string
    {
        $domain = substr($punchoutGroupEmail, strpos($punchoutGroupEmail, '@') + 1);
        if (isset($extrinsicData['FirstName'], $extrinsicData['LastName'])) {
            $name = sprintf('%s_%s@', $extrinsicData['FirstName'], $extrinsicData['LastName']);
        } elseif (isset($extrinsicData['UniqueName'])) {
            $name = $extrinsicData['UniqueName'];
        } elseif (isset($cxmlData->Request->PunchOutSetupRequest->Contact->Name)) {
            $name = (string)$cxmlData->Request->PunchOutSetupRequest->Contact->Name;
        } else {
            $name = uniqid('', false);
        }
        $email = sprintf('%s%s', $name, $domain);
        if (\Zend_Validate::is($email, 'EmailAddress')) {
            return $email;
        }
        return  sprintf('%s_%s', $name, $punchoutGroupEmail);
    }
    
    /**
     * Get First and Last Names from posted extrinsic Data or fallback to default
     */
    public function getFirstLastName($extrinsicData)
    {
        $data = [];
        if (isset($extrinsicData['FirstName'], $extrinsicData['LastName'])) {
            $data['first_name'] = $extrinsicData['FirstName'];
            $data['last_name'] = $extrinsicData['LastName'];
        } else {
            $data['first_name'] = 'Punchout';
            $data['last_name'] =  'User';
        }
        return $data;
    }
    
    /**
     * ParseOrderRequest from string to SimpleXMLElement
     * @throws CxmlDocumentLoadingException
     */
    public function parseOrderRequest(string $orderXMLRequest):\SimpleXMLElement
    {
        $prev = \libxml_use_internal_errors(true);
        \libxml_clear_errors();
        
        $dom = new \DOMDocument;
        $dom->loadXML($orderXMLRequest);
    
        $errors = \libxml_get_errors();
        \libxml_use_internal_errors($prev);
    
        if (\count($errors) !== 0) {
            throw new CxmlDocumentLoadingException($errors);
    
        }
        $libxml = libxml_use_internal_errors(true);
        $isValid = $dom->validate();
        if (!$isValid) {
            $errors = \libxml_get_errors();
            \libxml_use_internal_errors($libxml);
            throw new RuntimeException(sprintf(
                "validation Error, cXML Order Request: %s",
                $errors[0]->message
            ));
        }
    
        return simplexml_import_dom($dom);
    }
    
    /**
     * Parse Address from object to data array
     */
    public function parseAddress($address) : array
    {
        $extAddressId = (string)$address->attributes()->addressID;
        $deliverName = [];
        $argsName = (string)$address->Name;
        if (isset($address->PostalAddress->DeliverTo)) {
            foreach ($address->PostalAddress->DeliverTo as $deliverTo) {
                if (trim((string)$deliverTo) !== '') {
                    $deliverName[] = (string)$deliverTo;
                }
            }
            if ($deliverName) {
                $argsName = implode(',', $deliverName);
            }
        }
        $nameData = $this->getDefaultFirstLastName($argsName);
        
        $countryId = (string)$address->PostalAddress->Country->attributes()->isoCountryCode;
        $region = '';
       
        $street = [];
        foreach ($address->PostalAddress->Street as $line) {
            if (trim((string)$line) !== '') {
                $street[] = (string)$line;
            }
        }
        
        $tel = isset(
            $address->Phone->TelephoneNumber->CountryCode,
            $address->Phone->TelephoneNumber->AreaOrCityCode,
            $address->Phone->TelephoneNumber->Number
        ) ? $address->Phone->TelephoneNumber->CountryCode .
            $address->Phone->TelephoneNumber->AreaOrCityCode .
            $address->Phone->TelephoneNumber->Number : '0';
        
        return  [
            'ext_address_id' => $extAddressId,
            'firstname' => $nameData[0],
            'lastname' => $nameData[1],
            'company' => (string)$address->Name,
            'street' => $street,
            'city' => (string)$address->PostalAddress->City,
            'postcode' => (string)$address->PostalAddress->PostalCode,
            'region' => $region,
            'country_id' => $countryId,
            'email' => (string)$address->Email,
            'telephone' => $tel
        ];
    }
    
    /**
     * Return a default name if non sent in request
     */
    private function getDefaultFirstLastName(string $name): array
    {
        $nameArray = [];
        preg_match('/^(.+) ([^ ]+)$/', $name, $s);
        if (count($s) > 2) {
            $nameArray[] = $s[1];
            $nameArray[] = $s[2];
        } else {
            $nameArray[] = $name;
            $nameArray[] = 'Punchout User';
        }
        return $nameArray;
    }
    
    /**
     *  Get Item out data from Extrinsic xml
     */
    public function getItemOutExtrinsic(array $extrinsic, bool $nonDefault = false): array
    {
        $result = [];
        foreach ($extrinsic as $ext) {
            if(isset($ext['name'])){
                $key = strtolower(trim((string)$ext['name']));
            }else{
                $key = strtolower(trim((string)$ext));
            }

            if(is_array($ext)){
                $ext = array_shift($ext);
            }
            $value = (string)$ext;
            if ($nonDefault) {
                $result[$key] = "{$key}: " .$value;
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }
    
    public function getEmailByXPathConfig($cxmlNodeXpathConfig, SimpleXMLElement $parsedXMLData)
    {
        match($cxmlNodeXpathConfig) {
            '/cXML//Request/PunchOutSetupRequest/Contact/Email' => $xpathSelector = $parsedXMLData->xpath('/cXML/Request/PunchOutSetupRequest/Contact/Email'),
            '/cXML//Request/PunchOutSetupRequest/Extrinsic[@name="UserEmail"]' => $xpathSelector = $parsedXMLData->xpath('/cXML/Request/PunchOutSetupRequest/Extrinsic[@name="UserEmail"]'),
            '/cXML//Request/PunchOutSetupRequest/Extrinsic[@name="User"]' => $xpathSelector = $parsedXMLData->xpath('/cXML/Request/PunchOutSetupRequest/Extrinsic[@name="User"]'),
            default => $xpathSelector = 'none'
        };
        return $xpathSelector;
    
    }
   
}
