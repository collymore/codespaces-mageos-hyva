<?php
    
    namespace Develodesign\Punchout\Model\Order\Request;

    use Develodesign\Punchout\Service\CustomerService;
    use Develodesign\Punchout\Service\PunchoutGroupService;
    use Develodesign\Punchout\Service\SetupRequestService;

    abstract class AbstractRequest extends \Magento\Framework\Model\AbstractModel
    {
        protected $document;
    
        protected $customer;
    
        protected $companyId;
        
        protected $punchoutGroup;
        protected $shippingCode;
        protected $shippingPrice;
        
        protected $tax;
        protected $poNumber;
        
        protected $paymentMethod;
        protected $grandTotal;
        protected $shipTo;
        
        protected $cxmlService;
        
        protected $customerService;
        
        protected $punchoutGroupService;
        
        protected $setupRequestService;
    
        public function __construct(
            \Develodesign\Punchout\Service\CxmlService $cxmlService,
            CustomerService $customerService,
            PunchoutGroupService $punchoutGroupService,
            SetupRequestService $setupRequestService
        )
        {
         $this->cxmlService = $cxmlService;
         $this->customerService = $customerService;
         $this->punchoutGroupService = $punchoutGroupService;
         $this->setupRequestService = $setupRequestService;
        }
    
        public function setDocument($document): void
        {
            $this->document = $document;
        }
    
        public function getDocument()
        {
            return $this->document;
        }
    
        abstract public function isValid();
    
        abstract public function getShipToAddress();
    
        abstract public function getBillToAddress();
    
        abstract public function getShippingCode();
    
        abstract public function getShippingPrice();
    
        abstract public function getTax();
    
        public function setCustomer($customer): void
        {
            $this->customer = $customer;
        }
    
        public function getCustomer()
        {
            return $this->customer;
        }
        
        public function getPunchoutGroup()
        {
            return $this->punchoutGroup;
        }
    
        public function getCompanyId()
        {
            return $this->companyId;
        }
    
        public function setCompanyId($companyId): void
        {
            $this->companyId = $companyId;
        }
        
    
        public function setPoNumber($poNumber): void
        {
            $this->poNumber = $poNumber;
        }
    
        public function getPoNumber()
        {
            return $this->poNumber;
        }
        
    
        public function getPaymentMethod()
        {
            return $this->paymentMethod;
        }
        
    
        public function getGrandTotal()
        {
            return $this->grandTotal;
        }
    
        public function setShipTo($shipToAddress)
        {
            $this->shipTo = $shipToAddress;
        }
    
        public function getShipTo()
        {
            return $this->shipTo;
        }
    }
