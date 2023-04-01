<?php
    
    namespace Develodesign\Punchout\Observer\Cxml;

    use Develodesign\Punchout\Api\ActivityEventLogRepositoryInterface;
    use Develodesign\Punchout\Model\ActivityEventLogFactory;
    use Magento\Framework\Event\Observer;
    use Magento\Framework\Event\ObserverInterface;
    use Magento\Framework\Exception\LocalizedException;

    class CxmlSetupRequestEvent implements ObserverInterface
    {
        /**
         * @var ActivityEventLogFactory
         */
        protected $activityEventLogFactory;
    
        /**
         * @var ActivityEventLogRepositoryInterface
         */
        protected $activityEventLogRepository;
        public function __construct(
            ActivityEventLogFactory $activityEventLogFactory,
            ActivityEventLogRepositoryInterface $activityEventLogRepository
        )
        {
            $this->activityEventLogFactory = $activityEventLogFactory;
            $this->activityEventLogRepository = $activityEventLogRepository;
        }
    
        /**
         * @throws LocalizedException
         */
        public function execute(Observer $observer)
        {
            $activityEvent = $this->activityEventLogFactory->create();
        
            return $activityEvent->setData([
                'event_type'       => $observer->getEvent()->getEventType(),
                'action'           => $observer->getEvent()->getAction(),
                'user_id'          => $observer->getEvent()->getUserId(),
                'punchoutgroup_id' => $observer->getEvent()->getPunchoutgroupId(),
                'info'             => $observer->getEvent()->getInfo()
            ])->save();
        
        }
    
    }
