<?php

namespace Develodesign\Punchout\Service;

use Develodesign\Punchout\Exceptions\CxmlDocumentLoadingException;
use Develodesign\Punchout\Model\PunchoutGroup;
use Magento\Framework\Simplexml\Element;
use RuntimeException;
use SimpleXMLElement;
use \Magento\Directory\Model\Region;
use \Magento\Directory\Helper\Data as DirectoryHelper;

class CxmlService
{
    /**
     * @var Region
     */
    private $region;
    
    /**
     * @var DirectoryHelper
     */
    private $directoryHelper;
    
    /**
     * CxmlService constructor.
     * 
     * @param Region $region
     * @param DirectoryHelper $directoryHelper
     */
    public function __construct(
        \Magento\Directory\Model\Region $region,
        DirectoryHelper $directoryHelper
    ){
        $this->region = $region;
        $this->directoryHelper = $directoryHelper;
    }
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
    public function validateEmail($email): string
    {
        $userEmail = '';
        if (!empty($email)) {
            if ($email === 'none') {
                return $userEmail;
            }
            if(\Zend_Validate::is($email, 'EmailAddress')){
                $userEmail = $email;
            }
        }
        return $userEmail;
    }
    
    /**
     * Create usable email from punchout group domain and cxml data
     * @throws \Zend_Validate_Exception
     */
    public function createEmail($extrinsicData, $cxmlData, PunchoutGroup $punchoutGroup): string
    {
        // Get the punchout group email domain
        $punchoutGroupEmail = $punchoutGroup->getGroupEmail();
        $domain = substr($punchoutGroupEmail, strpos($punchoutGroupEmail, '@') + 1);
        
        // Fetch first and last name using the getFirstLastName method
        $nameData = $this->getFirstLastName($extrinsicData, $punchoutGroup, $cxmlData);
        $firstName = $nameData['first_name'];
        $lastName = $nameData['last_name'];
        
        // Check if the name is 'Punchout User', we don't want to use this for the email
        if ($firstName !== 'Punchout' || $lastName !== 'User') {
            $name = sprintf('%s_%s@', $firstName, $lastName);
            return $this->generateEmail($name, $domain);
        }
        
        // Fallback to UniqueName if 'Punchout User' is not valid
        if (isset($extrinsicData['UniqueName'])) {
            $name = $extrinsicData['UniqueName'];
            return $this->generateEmail($name, $domain);
        }
        
        // Fallback to Contact Name from cXML data
        if (isset($cxmlData->Request->PunchOutSetupRequest->Contact->Name) && !empty($cxmlData->Request->PunchOutSetupRequest->Contact->Name)) {
            $name = (string)$cxmlData->Request->PunchOutSetupRequest->Contact->Name;
            return $this->generateEmail($name, $domain);
        }
        
        // Final fallback to a unique ID
        $name = uniqid('', false);
        return $this->generateEmail($name, $domain);
    }
    
    private function generateEmail($name, $domain): string
    {
        $email = sprintf('%s%s', $name, $domain);
        
        // Validate the generated email address
        if (\Zend_Validate::is($email, 'EmailAddress')) {
            return $email;
        }
        
        // Return a fallback email format if validation fails
        return sprintf('%s_%s', $name, $domain);
    }
    
    /**
     * Get First and Last Names from posted extrinsic Data or fallback to default
     */
    public function getFirstLastName($extrinsicData,PunchoutGroup $punchoutGroup, SimpleXMLElement $parsedXMLData): array
    {
        // Fetch XPath configurations from PunchoutGroup
        $firstNameXPath = $punchoutGroup->getCxmlNodeXpathConfigUserFirstname();
        $lastNameXPath = $punchoutGroup->getCxmlNodeXpathConfigUserLastname();
        $nameFormat = $punchoutGroup->getCxmlNodeUserNameConfigFormat();
        
        // Extract the actual attribute names from the XPath
        $firstName = $firstNameXPath?$this->getValueByXPathConfig($firstNameXPath, $parsedXMLData):'';
        $lastName = $lastNameXPath?$this->getValueByXPathConfig($lastNameXPath, $parsedXMLData):'';
  
        if ($nameFormat === 'Single' && $firstName) {
            // Handle combined first and last name in the 'Single' format
            $userParts = explode(' ', $firstName);
            return [
                'first_name' => array_shift($userParts),
                'last_name' => implode(' ', $userParts),
            ];
        }
        
        if ($nameFormat === 'Both' && $firstName && $lastName) {
            // Handle separate first and last name in the 'Both' format
            return [
                'first_name' => $firstName,
                'last_name' => $lastName,
            ];
        }
        
        // 2. Fall back to the default 'FirstName' and 'LastName' in extrinsicData
        if (isset($extrinsicData['FirstName'], $extrinsicData['LastName'])) {
            if ($nameFormat === 'Single'){
                $userParts = explode(' ', $extrinsicData['FirstName']);
                $extrinsicData['FirstName'] = array_shift($userParts);
                $extrinsicData['LastName'] = implode(' ', $userParts);
            }
            return [
                'first_name' => $extrinsicData['FirstName'],
                'last_name' => $extrinsicData['LastName'],
            ];
        }
        
        // 3. Final fallback: Default values
        return [
            'first_name' => 'Punchout',
            'last_name' => 'User',
        ];
    }
    
    private function extractNameFromXPath(string $xpath): string
    {
        // Regex to match the 'name' attribute in the XPath
        if (preg_match("/@name='([^']+)'/", $xpath, $matches)) {
            return $matches[1]; // Return the extracted name
        }
        
        return $xpath; // Return the original string if no match is found
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
        
        // Default values for first and last name
        $firstName = 'Punchout';
        $lastName = 'User';
        $company = '';
        
        // Check if DeliverTo elements exist and get the first one for name parsing
        if (isset($address->PostalAddress->DeliverTo) && count($address->PostalAddress->DeliverTo) > 0) {
            $deliverTo = (string)$address->PostalAddress->DeliverTo[0];
            
            // If DeliverTo is not empty, parse it for first and last name
            if (!empty(trim($deliverTo))) {
                // Split the name into first and last name parts
                $nameParts = explode(' ', trim($deliverTo));
                
                if (count($nameParts) >= 2) {
                    $firstName = $nameParts[0];
                    $lastName = implode(' ', array_slice($nameParts, 1));
                } else {
                    $firstName = $deliverTo;
                }
            }
            
            // If there's a second DeliverTo element, use it for the company name
            if (count($address->PostalAddress->DeliverTo) > 1) {
                $company = (string)$address->PostalAddress->DeliverTo[1];
            } 
        } 
        
        $countryId = (string)$address->PostalAddress->Country->attributes()->isoCountryCode;
        
        // Process region/state information
        $regionId = '';
        $regionCode = '';
        
        if (isset($address->PostalAddress->State)) {
            $stateValue = (string)$address->PostalAddress->State;
            if (!empty($stateValue)) {
                // Try looking up by code
                $regionData = $this->region->loadByCode($stateValue, $countryId);
                if ($regionData->getId()) {
                    $regionCode = $regionData->getCode();
                    $regionId = $regionData->getId();
                } else {
                    // If region is not found, just use the provided value
                    $regionId = $stateValue;
                }
            }
        }
        
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
            'firstname' => $firstName,
            'lastname' => $lastName,
            'company' => $company,
            'street' => $street,
            'city' => (string)$address->PostalAddress->City,
            'postcode' => (string)$address->PostalAddress->PostalCode,
            'region' => $regionId,
            'regionName' => $regionCode,
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
    public function getItemOutExtrinsic(array $extrinsic, bool $nonDefault = false) : array
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

    /**
     * Gets a value by Xpath from the XML doc
     */
    public function getValueByXPathConfig($cxmlNodeXpathConfig, SimpleXMLElement $parsedXMLData): string
    {
        if(!trim($cxmlNodeXpathConfig)){
            return '';
        }

        $xpathSelector = $parsedXMLData->xpath($cxmlNodeXpathConfig);
        if(!$xpathSelector){
            return 'none';
        }
        return (string)$xpathSelector[0];
    }
    
    public function getDunsIdentity(string $dunsIdentityConfig,SimpleXMLElement $cxml): string
    {
        $dunsIdentity = '';
        switch ($dunsIdentityConfig) {
            case 'sender':
                $dunsIdentity = (string)$cxml->Header->Sender->Credential->Identity;
                break;
            case 'from':
                $dunsIdentity = (string)$cxml->Header->From->Credential->Identity;
                break;
            case 'both':
                $dunsIdentity = (string)$cxml->Header->From->Credential->Identity;
                if (empty($dunsIdentity)) {
                    $dunsIdentity = (string)$cxml->Header->Sender->Credential->Identity;
                }
                break;
                
        }
        return $dunsIdentity;
    }
}
