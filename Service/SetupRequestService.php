<?php

namespace Develodesign\Punchout\Service;

    use Develodesign\Punchout\Model\PunchoutSetupRequestFactory;
    use Develodesign\Punchout\Model\PunchoutSetupRequest;
    use Develodesign\Punchout\Model\ResourceModel\PunchoutSetupRequest as PunchoutSetupRequestResource;
    use Magento\Framework\DataObject;
    use Magento\Framework\Exception\AlreadyExistsException;
    use Magento\Framework\Simplexml\Element;

    class SetupRequestService
    {
        private $punchoutSetupRequestFactory;
        private $setupRequestResource;

        public function __construct(
            PunchoutSetupRequestFactory $punchoutSetupRequestFactory,
            PunchoutSetupRequestResource $setupRequestResource
        ) {
            $this->punchoutSetupRequestFactory = $punchoutSetupRequestFactory;
            $this->setupRequestResource = $setupRequestResource;
        }

        /**
         * @throws AlreadyExistsException
         */
        public function createPunchoutSetupRequest(DataObject $setupData)
        {
            $punchoutSetupModel = $this->punchoutSetupRequestFactory->create();
            $punchoutSetupModel->setCustomerId($setupData->getCustomerId())
                ->setPayloadId($setupData->getPayloadId())
                ->setSenderIdentity($setupData->getSenderIdentity())
                ->setReturnUrl($setupData->getReturnUrl())
                ->setBuyerCookie($setupData->getBuyerCookie())
                ->setAccessToken($setupData->getAccessToken())
                ->setExpiryDate($setupData->getExpiryDate());
            $this->setupRequestResource->save($punchoutSetupModel);

            return $punchoutSetupModel;
        }

        /**
         * @param int     $customerId
         * @param Element $cxmlData
         *
         * @return DataObject
         * @throws \Exception
         */
        public function prepareSetupData(int $customerId, Element $cxmlData): DataObject
        {
            return new DataObject(
                [
                    'customer_id'     => $customerId,
                    'payload_id'       => $cxmlData->getAttribute('payloadID'),
                    'sender_identity' => $cxmlData->Header->Sender->Credential->Identity,
                    'return_url'      => $cxmlData->Request->PunchOutSetupRequest->BrowserFormPost->URL,
                    'buyer_cookie'    => $cxmlData->Request->PunchOutSetupRequest->BuyerCookie,
                    'access_token'     => $this->generateUUID(),
                    'expiry_date'     => $this->getOneWeekInterval()
                ]
            );
        }

        private function generateUUID(): string
        {
            // ripemd128 is 128-bit hex
            $hash = hash('ripemd128', uniqid(mt_rand(), true));
            $uuid = '';
            // UUID format is XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX for readability
            $uuid .= substr($hash, 0, 8) .
                '-' .
                substr($hash, 8, 4) .
                '-' .
                substr($hash, 12, 4) .
                '-' .
                substr($hash, 16, 4) .
                '-' .
                substr($hash, 20, 12);

            return $uuid;
        }

        /**
         * Returns +1 week of current time stamp
         * @return string
         * @throws \Exception
         */
        private function getOneWeekInterval(): string
        {
            $now = new \DateTime('now', new \DateTimeZone('UTC'));
            return $now->modify('+1 week')->format('Y-m-d H:i:s');
        }

        public function getProxyResponse(PunchoutSetupRequest $punchoutSetupRequestModel): array
        {
            return [
                'token'   => $punchoutSetupRequestModel->getAccessToken(),
                'user_id' => $punchoutSetupRequestModel->getCustomerId(),
                'payloadId' => $punchoutSetupRequestModel->getPayloadId()
            ];
        }
    }
