<?php
    
    namespace Develodesign\Punchout\Observer\Oci;
    
    use Develodesign\Punchout\Observer\BasePunchoutRequestEvent;
    use Magento\Framework\Event\Observer;
    use Magento\Framework\Event\ObserverInterface;

    class OciSetupRequestEvent extends BasePunchoutRequestEvent implements ObserverInterface
    {
        /**
         * @throws \Exception
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
