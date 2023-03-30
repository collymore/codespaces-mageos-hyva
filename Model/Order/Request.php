<?php
    
    namespace Develodesign\Punchout\Model\Order;
    
    use Develodesign\Punchout\Exceptions\CxmlDocumentLoadingException;
    use Develodesign\Punchout\Model\Order\Request\AbstractRequest;
    use Develodesign\Punchout\Model\Order\Request\Cxml;
    use Develodesign\Punchout\Service\CreateOrderService;
    use Develodesign\Punchout\Service\CustomerService;
    use Develodesign\Punchout\Service\CxmlService;
    use Develodesign\Punchout\Service\PunchoutGroupService;
    use Develodesign\Punchout\Service\SessionService;
    use Develodesign\Punchout\Service\SetupRequestService;
    use http\Exception\RuntimeException;
    use Magento\Customer\Api\Data\CustomerInterface;
    use Magento\Customer\Model\Customer;
    use Magento\Framework\DataObject;
    use Magento\Framework\Exception\CouldNotSaveException;
    use Magento\Framework\Exception\LocalizedException;
    use Magento\Framework\Exception\NoSuchEntityException;

    class Request extends \Magento\Framework\DataObject
    {
        /** @var AbstractRequest */
        protected $document;
        
        protected $cxmlService;
    
        /**
         * @var CustomerInterface
         */
        protected $customer;
    
        protected $company;
    
        
        protected $punchoutGroup;
    
        /**
         * @var CustomerService
         */
        protected $customerService;
    
        /**
         * @var PunchoutGroupService
         */
        protected $punchoutGroupService;
    
        /**
         * @var SetupRequestService
         */
        protected $setupRequestService;
    
        /**
         * @var \Magento\Sales\Model\Order
         */
        protected $createOrder;
    
        /**
         * @var CreateOrderService
         */
        protected $createOrderService;
    
        /**
         * @var SessionService
         */
        protected $sessionService;
    
        public function __construct(
            CxmlService $cxmlService,
            CustomerService $customerService,
            PunchoutGroupService $punchoutGroupService,
            SetupRequestService $setupRequestService,
            CreateOrderService $createOrderService,
            SessionService $sessionService
        )
        {
           $this->cxmlService = $cxmlService;
           $this->customerService = $customerService;
           $this->punchoutGroupService = $punchoutGroupService;
           $this->setupRequestService = $setupRequestService;
           $this->createOrder = null;
           $this->createOrderService = $createOrderService;
           $this->sessionService = $sessionService;
        }
    
        public function setDocument($document)
        {
            if ($document instanceof AbstractRequest) {
                $this->document = $document;
                return $this;
            }
        
            if (is_string($document)) {
                $this->document = new Cxml(
                    $this->cxmlService,
                    $this->customerService,
                    $this->punchoutGroupService,
                    $this->setupRequestService
                );
            
                $this->document->setDocument($document);
            }
            return $this;
        }
    
        /**
         * @return AbstractRequest
         */
        public function getDocument(): AbstractRequest
        {
            return $this->document;
        }
    
        /**
         * @return CustomerInterface|null
         */
        public function getCustomer(): ?CustomerInterface
        {
            if ($this->customer === null) {
                $this->customer = $this->getDocument()->getCustomer();
            }
            return $this->customer;
        }
    
        /*public function getCompany(): ?\Magento\Company\Api\Data\CompanyInterface
        {
            if ($this->company === null) {
                $companyId = $this->getDocument()->getCompany();
                $this->company = $this->companyRepository->get((int)$companyId);
            }
            return $this->company;
        }*/
        
    
        /**
         * @return mixed
         */
        public function getPunchoutGroup()
        {
            if($this->punchoutGroup === null){
                $this->punchoutGroup = $this->getDocument()->getPunchoutGroup();
            }
            return $this->punchoutGroup;
        
        }
    
        /**
         * @return bool
         * @throws CxmlDocumentLoadingException
         */
        public function isValid(): bool
        {
            /** @var Cxml $document */
            $document = $this->document;
            $sourceXML = $document->getCxml();
            $poNumber = (string)$sourceXML->Request->OrderRequest->OrderRequestHeader['orderID'];
            $dunsIdentity = (string)$sourceXML->Header->Sender->Credential->Identity;
            
            $isAlreadyExists = $this->setupRequestService->orderAlreadyExists(poNumber: $poNumber,
                customerId: (int)$this->getCustomer()->getId(), dunsNetwork: $dunsIdentity);
            return $this->getCustomer() && $this->getPunchoutGroup() !== null && !$isAlreadyExists;
            
        }
    
    
        /**
         * @throws NoSuchEntityException
         * @throws CouldNotSaveException
         * @throws LocalizedException
         * @throws \Exception
         */
        public function getCreateOrder(): array
        {
            $result = [];
            if ($this->createOrder === null) {
                $customer = $this->getCustomer();
                $store = $this->customerService->getMainStore();
                $shippingAddressData = $this->getDocument()->getShipToAddress();
                $billingAddressData = $this->getDocument()->getBillToAddress();
                $result['shippingAddressData'] = $shippingAddressData;
                $result['billingAddressData'] = $billingAddressData;
    
                if (!isset($billingAddressData['firstname']) || !$billingAddressData['firstname']) {
                    $billingAddressData['firstname'] = $customer->getFirstname();
                }
                $purchaseOrderNo = $this->getDocument()->getPoNumber();
                $shippingMethodCode = $this->getDocument()->getShippingCode();
                $shippingPrice = $this->getDocument()->getShippingPrice();
                $grandTotal = $this->getDocument()->getGrandTotal();
                $tax = $this->getDocument()->getTax();
                $quote = $this->createOrderService->getCart();
                $quote->assignCustomer($customer);
                $quote->setStoreId($store->getId());
                $quote->setCurrency();
                $quote->setCustomerIsGuest(false);
                $outItems = $this->getDocument()->getItems()->getData();
                $quoteItems =  $this->createOrderService->getQuoteItem(outItems: $outItems,quote: $quote,store: $store);
                $this->sessionService->getCustomerSession()->setId($customer->getId());
                foreach ($quoteItems as $quoteItem) {
                    $quote->addItem($quoteItem);
                }
                if ($quote->getAllVisibleItems()) {
                    $quote->getBillingAddress()->addData($billingAddressData);
                    $quote->getBillingAddress()->setCustomerAddressId('');
                    $existAddress = $this->customerService->getExistingCustomerAddress(data: $shippingAddressData,
                        customer: $customer);
                    if ($existAddress) {
                        $quote->getShippingAddress()->addData($existAddress);
                    } else {
                        $quote->getShippingAddress()->addData($shippingAddressData);
                        $quote->getShippingAddress()->setCustomerAddressId('');
                    }
                    $shippingAddress = $quote->getShippingAddress();
                    $shippingAddress->setCollectShippingRates(true)
                        ->collectShippingRates()
                        ->setShippingMethod($shippingMethodCode);
                    
                    if ($this->createOrderService->isPaymentAvailable(storeId: $store->getId(),
                            quote: $quote) === true) {
                        $quote->setInventoryProcessed(false);
                        $quote->save();
            
                        $quote->getPayment()->setMethod($this->getDocument()->getPaymentMethod())
                            ->setPoNumber($purchaseOrderNo);
                        $quote->collectTotals()->save();
            
                        $orderId = $this->createOrderService->makeOrderPlacement($quote->getId());
                        $order = $this->createOrderService->getCreatedOrder($orderId);
    
                        if ($order->getEntityId()) {
                            $this->createOrder = $order;
                            $sourceXml = $this->document->getCxml();
                            $orderSetupRequestData = $this->setupRequestService->getOrderSetupRequestData(sourceXml: $sourceXml,
                                customerId: $customer->getId(), order: $order);
                            $orderSetupRequestData['purchase_order_number'] = $purchaseOrderNo;
                            $result['payloadId'] = $orderSetupRequestData['payloadId'];
                            $setupId = $this->setupRequestService->createOrderSetUpRequest($orderSetupRequestData);
                            if ($setupId) {
                                $result['setup_id'] = $setupId;
                            }
                            $this->createOrder->addStatusHistoryComment("PunchOut Order received (PO Number {$order->getPayment()->getPoNumber()})");
                            $this->createOrder->setState(\Magento\Sales\Model\Order::STATE_PROCESSING)->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
                            $this->createOrderService->invoiceOrder($this->createOrder);
                            $this->createOrder->save();
                            $result['order_id'] = $order->getRealOrderId();
                            $result['message'] = 'Order created successfully - web reference: ' . $order->getIncrementId();
                            $result['punchoutGroupId'] = $this->getPunchoutGroup()->getPunchoutgroupId();
                        }
                        var_dump($result);
                        die();
                    }
                }
               
            }
            return $result;
        }
    
    }
