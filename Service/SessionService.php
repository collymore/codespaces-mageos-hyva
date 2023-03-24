<?php

namespace Develodesign\Punchout\Service;

    use Magento\Checkout\Model\Session as CheckoutSession;
    use Magento\Customer\Model\Session as CustomerSession;
    use Magento\Framework\DataObject;

    class SessionService
    {
        /** @var CustomerSession */
        protected $customerSession;

        /** @var CheckoutSession */
        protected $checkoutSession;

        public function __construct(
            CustomerSession $customerSession,
            CheckoutSession $checkoutSession
        ) {
            $this->customerSession = $customerSession;
            $this->checkoutSession = $checkoutSession;
        }

        /**
         * @param $id
         *
         * @return bool
         */
        public function authoriseCustomer(int $customerId): bool
        {
            $this->customerSession->setCustomerId($customerId);
            return $this->customerSession->loginById($customerId);
        }

        public function createCXMLSessionData(DataObject $punchoutSetup)
        {
            $this->customerSession->setPunchoutType('cxml')
                    ->setPayloadId($punchoutSetup->getPayloadId())
                    ->setSenderIdentity($punchoutSetup->getSenderIdentity())
                    ->setReturnUrl($punchoutSetup->getReturnUrl())
                    ->setBuyerCookie($punchoutSetup->getBuyerCookie());

            return $this->customerSession;
        }

        public function createOCISessionData(array $params)
        {
            $this->customerSession->setPunchoutType('oci')
                ->setHookUrl($params['hook_url'])
                ->setTarget($params['~target'])
                ->setOkCode($params['~okcode'])
                ->setCaller($params['~caller'])
                ->setOciVersion($params['oci_version']);

            return $this->getCustomerSession();
        }

        public function getPunchoutType(): string
        {
            return $this->customerSession->getPunchoutType();
        }

        public function getCustomerSession()
        {
            return $this->customerSession;
        }
        
        public function getCXMLSessionData()
        {
            $customerSession = $this->getCustomerSession();
            return [
                'payloadId' => $customerSession->getPayloadId(),
                'sender_identity' => $customerSession->getSenderIdentity(),
                'return_url' => $customerSession->getReturnUrl(),
                'buyer_cookie' => $customerSession->getBuyerCookie()
            ];
         
        }


        /**
         * Clears current user cart session data
         * @return bool
         * @throws \Magento\Framework\Exception\LocalizedException
         * @throws \Magento\Framework\Exception\NoSuchEntityException
         */
        public function clearAuthUserCartSessionData(): bool
        {
            if ($this->customerSession->isSessionExists() === true && $this->checkoutSession->isSessionExists() === true) {
                if ($this->checkoutSession->hasQuote() === true) {
                    $cart = $this->checkoutSession->getQuote();
                    foreach ($this->checkoutSession->getQuote()->getAllVisibleItems() as $item) {
                        $cart->removeItem($item->getItemId())->save();
                    }
                    $cart->setTotalsCollectedFlag(false);
                    $cart->collectTotals();
                    $cart->save();
                    return true;
                }
            }
            return false;
        }
    }
