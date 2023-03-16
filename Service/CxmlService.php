<?php

namespace Develodesign\Punchout\Service;

use Magento\Framework\Simplexml\Element;
use RuntimeException;

class CxmlService
{
    public function parseXmlResponse($rawRequestBody): Element|\SimpleXMLElement
    {
        $libxml = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($rawRequestBody, Element::class);
        if ($xml === false) {
            libxml_use_internal_errors($libxml);
            throw new RuntimeException(sprintf(
                "Unable to partse cXML Request %s",
                trim(libxml_get_last_error()->message)
            ));
        }
        return $xml;
    }

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

    public function getAribaNetworkId(): false|string
    {
        $aribaNetworkId = false;
        if (isset($cxmlData->Header->Sender->Credential['domain']) && stripos(strtolower((string)$cxmlData->Header->Sender->Credential['domain']), 'AribaNetwork') !== false) {
            $aribaNetworkId = (string)$cxmlData->Header->From->Credential->Identity;
        }

        if (isset($cxmlData->Header->To->Credential['domain']) && stripos(strtolower((string)$cxmlData->Header->To->Credential['domain']), 'transactionnetworkId') !== false) {
            $aribaNetworkId = (string)$cxmlData->Header->From->Credential->Identity;
        }
        return $aribaNetworkId;
    }

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

    public function isCreate($cxmlData): bool
    {
        return $cxmlData->Request->PunchOutSetupRequest->getAttribute('operation') === 'create';
    }

    /**
     * @throws \Zend_Validate_Exception
     */
    public function fetchEmail($extrinsicData, $cxmlData): string
    {
        $useEmail = '';
        if (isset($extrinsicData['UserEmail']) && !empty($extrinsicData['UserEmail']) && \Zend_Validate::is(value:$extrinsicData['UserEmail'],classBaseName:'EmailAddress'
        )) {
            $useEmail = $extrinsicData['UserEmail'];
        } elseif (isset($cxmlData->Request->PunchOutSetupRequest->Contact->Email) && !empty($cxmlData->Request->PunchOutSetupRequest->Contact->Email)
            && \Zend_Validate::is(
                $cxmlData->Request->PunchOutSetupRequest->Contact->Email,
                classBaseName:'EmailAddress'
            )) {
            $useEmail = (string)$cxmlData->Request->PunchOutSetupRequest->Contact->Email;
        }
        return $useEmail;
    }
    
    /**
     * @throws \Zend_Validate_Exception
     */
    public function createEmail($extrinsicData, $cxmlData, $punchoutGroupEmail): string
    {
        $domain = substr($punchoutGroupEmail, strpos($punchoutGroupEmail, '@') + 1);
        if(isset($extrinsicData['FirstName'],$extrinsicData['LastName'])){
            $name = sprintf('%s_%s@',$extrinsicData['FirstName'],$extrinsicData['LastName']);
        }elseif (isset($extrinsicData['UniqueName'])){
            $name = $extrinsicData['UniqueName'];
        } else if(isset($cxmlData->Request->PunchOutSetupRequest->Contact->Name)){
            $name = (string)$cxmlData->Request->PunchOutSetupRequest->Contact->Name;
        }else {
            $name = uniqid('',false);
        }
        $email = sprintf('%s%s',$name,$domain);
        if(\Zend_Validate::is($email, classBaseName:'EmailAddress')) {
            return $email;
        }
        return  sprintf('%s_%s',$name,$punchoutGroupEmail);
    }
    
    public function getFirstLastName($extrinsicData)
    {
        $data = [];
        if(isset($extrinsicData['FirstName'],$extrinsicData['LastName'])){
            $data['first_name'] = $extrinsicData['FirstName'];
            $data['last_name'] = $extrinsicData['LastName'];
        }else{
            $data['first_name'] = 'Punchout';
            $data['last_name'] =  'User';
        }
        return $data;
    }
    
    
}
