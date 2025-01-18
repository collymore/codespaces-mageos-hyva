<?php
    
    namespace Develodesign\Punchout\Observer\PunchoutExceptions;
    
    use Develodesign\Punchout\Model\ActivityEventLogFactory;
    use Develodesign\Punchout\Observer\BasePunchoutRequestEvent;
    use Develodesign\Punchout\Service\CustomerService;
    use Develodesign\Punchout\Service\SessionService;
    use Magento\Framework\Event\Observer;
    use Magento\Framework\Event\ObserverInterface;
    use \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;

class ExceptionRequestEvent extends BasePunchoutRequestEvent implements ObserverInterface
{
    /**
     * @var SessionService
     */
    protected $sessionService;
        
    /**
     * @var CustomerService
     */
    protected $customerService;

    /**
     * @var RemoteAddress
     */
    protected $remote;
    
    public function __construct(
        ActivityEventLogFactory $activityEventLogFactory,
        SessionService $sessionService,
        CustomerService $customerService,
        RemoteAddress $remote
    ) {
        $this->sessionService = $sessionService;
        $this->customerService = $customerService;
        $this->remote = $remote;
        parent::__construct($activityEventLogFactory);
    }
    
    public function execute(Observer $observer): void
    {
        $activityEvent = $this->activityEventLogFactory->create();
        $customerId = $this->sessionService->getCustomerSession()->getCustomerId()??0;
        $punchoutGroupId = 0;
        if($customerId){
            $punchoutGroupId = $this->customerService->getPunchoutGroupId($customerId);
        }
        $activityEvent->setData([
                'event_type'       => $observer->getEvent()->getEventType(),
                'action'           => $observer->getEvent()->getAction(),
                'user_id'          => $customerId,
                'punchoutgroup_id' => $punchoutGroupId,
                'info'             => $observer->getEvent()->getInfo(),
                'ip'          => $this->remote->getRemoteAddress()
            ])->save();
    }
}
