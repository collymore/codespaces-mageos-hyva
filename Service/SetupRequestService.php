<?php

namespace Develodesign\Punchout\Service;

    use Develodesign\Punchout\Model\PunchoutSetupRequest;
    use Develodesign\Punchout\Model\PunchoutSetupRequestFactory;
    use Develodesign\Punchout\Model\ResourceModel\PunchoutSetupRequest as PunchoutSetupRequestResource;
    use Develodesign\Punchout\Model\ResourceModel\PunchoutSetupRequest\Collection as PunchoutSetupCollection;
    use Magento\Framework\DataObject;
    use Magento\Framework\Exception\AlreadyExistsException;
    use Magento\Framework\Simplexml\Element;
    use Magento\Framework\UrlInterface;

    class SetupRequestService
    {
        /**
         * @var PunchoutSetupRequestFactory
         */
        private $punchoutSetupRequestFactory;

        /**
         * @var PunchoutSetupRequestResource
         */
        private $setupRequestResource;

        /**
         * @var UrlInterface
         */
        private $url;

        /**
         * @var PunchoutSetupCollection
         */
        private $collection;

        public function __construct(
            PunchoutSetupRequestFactory $punchoutSetupRequestFactory,
            PunchoutSetupRequestResource $setupRequestResource,
            UrlInterface $urlBuilder,
            PunchoutSetupCollection $collection
        ) {
            $this->punchoutSetupRequestFactory = $punchoutSetupRequestFactory;
            $this->setupRequestResource = $setupRequestResource;
            $this->url = $urlBuilder;
            $this->collection = $collection;
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

        /**
         * @param PunchoutSetupRequest $punchoutSetupRequestModel
         *
         * @return string
         */
        public function getStartUpUrlResponse(PunchoutSetupRequest $punchoutSetupRequestModel): string
        {
            $postBody = [
                'token'   => $punchoutSetupRequestModel->getAccessToken(),
                'user_id' => $punchoutSetupRequestModel->getCustomerId(),
                'payloadId' => $punchoutSetupRequestModel->getPayloadId()
            ];
            return sprintf('%s%s?Bearer=%s', $this->url->getUrl(), $this->getPunchoutSetUpLoginUrl(), base64_encode(json_encode($postBody)));
        }

        private function getPunchoutSetUpLoginUrl(): string
        {
            return 'develo_punchout/index/loginproxy';
        }

        public function getProxyUser(string $token, int $userId): ?DataObject
        {
            $punchoutSetup = $this->collection->addFieldToFilter('access_token', $token)
                ->addFieldToFilter('customer_id', $userId);
            if (!($punchoutSetup->count() > 0)) {
                return null;
            }
            return $punchoutSetup->getFirstItem();
        }

        public function getRefreshedToken($userId, $now)
        {
            $timeStamp = $now->format('Y-m-d H:i:s');
            $punchoutSetupCollection = $this->collection->addFieldToFilter('customer_id', $userId)
                ->addFieldToFilter('expiry_date', ['gteq' => $timeStamp]);
            if ($punchoutSetupCollection->count() > 0) {
                return $punchoutSetupCollection->getFirstItem()->getAccessToken();
            }
            return false;
        }
        
        public function orderAlreadyExists(string $poNumber,int $customerId, $dunsNetwork): bool
        {
            $result = $this->collection->addFieldToFilter('po_number', $poNumber)
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('sender_identity', $dunsNetwork);
        
            if ($result->count() > 0) {
                throw new \RuntimeException(sprintf('An Order already created for the customer with purchase order number %s',$poNumber));
            }
            return false;
        }
    
        public function getOrderSetupRequestData($sourceXml, $customerId,$order): array
        {
            return [
                'customer_id' => $customerId,
                'payloadId' => (string)$sourceXml['payloadID'],
                'sender_identity' => (string)$sourceXml->Header->Sender->Credential->Identity,
                'order_status' => sprintf('OrderId: %s  Status: %s', $order->getIncrementId(), $order->getRealOrderId())
            ];
        }
    
        /**
         * @throws AlreadyExistsException
         */
        public function createOrderSetUpRequest($arryData): int
        {
            $punchoutSetupModel = $this->punchoutSetupRequestFactory->create();
            $punchoutSetupModel->setCustomerId($arryData['customer_id'])
                ->setPayloadId($arryData['payloadId'])
                ->setSenderIdentity($arryData['sender_identity'])
                ->setRequestType(1)
                ->setPurchaseOrderNumber($arryData['purchase_order_number'])
                ->setOrderStatus($arryData['order_status']);
            $this->setupRequestResource->save($punchoutSetupModel);
            return (int)$punchoutSetupModel->getSetupId();
        }

    }
