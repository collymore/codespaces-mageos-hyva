<?php

namespace Develodesign\Punchout\Service;

use Magento\Framework\Simplexml\Element;
use RuntimeException;

class CxmlService
{
    public function parseXmlResponse($rawRequestBody)
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

    public function validateSetupRequest(Element $cxmlData)
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

    public function getAribaNetworkId()
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

    public function getExtrinsicData($extrinsic)
    {
        $result = [];
        foreach ($extrinsic as $ext) {
            $key = (string)$ext->getAttribute('name');
            $value = (string)$ext;
            $result[$key] = $value;
        }
        return $result;
    }

   

    public function isCreate($cxmlData)
    {
        return $cxmlData->Request->PunchOutSetupRequest->getAttribute('operation') === 'create';
    }

    public function fetchEmail($extrinsicData, $cxmlData)
    {
        $useEmail = '';
        if (isset($extrinsicData['UserEmail']) && !empty($extrinsicData['UserEmail'])) {
            $useEmail = $extrinsicData['UserEmail'];
        } elseif (isset($cxmlData->Request->PunchOutSetupRequest->Contact->Email) && !empty($cxmlData->Request->PunchOutSetupRequest->Contact->Email)) {
            $useEmail = (string)$cxmlData->Request->PunchOutSetupRequest->Contact->Email;
        }
        return $useEmail;
    }
    
    private function createEmail()
    {
    
    }
}
