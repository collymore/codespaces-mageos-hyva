<?php
    
    namespace Develodesign\Punchout\Observer\PunchoutExceptions;
    
    use Develodesign\Punchout\Model\ActivityEventLogFactory;
    use Develodesign\Punchout\Observer\BasePunchoutRequestEvent;
    use Develodesign\Punchout\Service\CustomerService;
    use Develodesign\Punchout\Service\SessionService;
    use Magento\Framework\Event\Observer;
    use Magento\Framework\Event\ObserverInterface;

    class ExceptionRequestEvent extends BasePunchoutRequestEvent implements ObserverInterface
    {
        protected $sessionService;
        
        protected $customerService;
    
        public function __construct(
            ActivityEventLogFactory $activityEventLogFactory,
            SessionService $sessionService,
            CustomerService $customerService
        )
        {
            $this->sessionService = $sessionService;
            $this->customerService = $customerService;
            parent::__construct($activityEventLogFactory);
        }
    
        public function execute(Observer $observer)
        {
            $activityEvent = $this->activityEventLogFactory->create();
            $customerId = $this->sessionService->getCustomerSession()->getCustomerId();
            $punchoutGroupId = $this->customerService->getPunchoutGroupId($customerId);
            return $activityEvent->setData([
                'event_type'       => $observer->getEvent()->getEventType(),
                'action'           => $observer->getEvent()->getAction(),
                'user_id'          => $customerId,
                'punchoutgroup_id' => $punchoutGroupId,
                'info'             => $observer->getEvent()->getInfo()
            ])->save();
        }
    }
