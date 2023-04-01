<?php
    
    namespace Develodesign\Punchout\Event;

    use Magento\Framework\Event\ManagerInterface as EventManager;

    class EventServiceProvider
    {
    
        /**
         * @var EventManager
         */
        protected $eventManager;
    
        /*
         * @param EventManager $eventManager
         */
        public function __construct(EventManager $eventManager)
        {
            $this->eventManager = $eventManager;
        }
        
    
        public function dispatchCxmlSetupRequestEvent(int $customerId, int $punchoutgroupId, string $info): void
        {
             $this->eventManager->dispatch('cxml_setup_request_start_url',
                [
                    'event_type' => 'CXML PunchOutSetupRequest',
                    'action' => 'CxmlSetup',
                    'user_id' => $customerId,
                    'punchoutgroup_id' => $punchoutgroupId,
                    'info' => $info
                ]
            );
        }
    
    }
