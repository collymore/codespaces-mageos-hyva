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
             $this->eventManager->dispatch('cxml_setup_request_event',
                [
                    'event_type' => 'CXML PunchOutSetupRequest',
                    'action' => 'CxmlSetup',
                    'user_id' => $customerId,
                    'punchoutgroup_id' => $punchoutgroupId,
                    'info' => $info
                ]
            );
        }
        
        public function dispatchLoginProxyRequestEvent(int $customerId, int $punchoutgroupId, string $info): void
        {
            $this->eventManager->dispatch('login_proxy_request_event',
                [
                    'event_type' => 'CXML PunchOutSetupRequest Store Login',
                    'action' => 'LoginProxy',
                    'user_id' => $customerId,
                    'punchoutgroup_id' => $punchoutgroupId,
                    'info' => $info
                ]
            );
        }
    
    
        public function dispatchOciSetupRequestEvent(int $customerId, int $punchoutgroupId, string $info): void
        {
            $this->eventManager->dispatch('oci_setup_request_event',
                [
                    'event_type' => 'OCI PunchOutSetupRequest',
                    'action' => 'OCI Setup',
                    'user_id' => $customerId,
                    'punchoutgroup_id' => $punchoutgroupId,
                    'info' => $info
                ]
            );
        }
    
        public function dispatchCxmlOrderRequestEvent(int $customerId, int $punchoutgroupId, string $info): void
        {
            $this->eventManager->dispatch('cxml_order_request_event',
                [
                    'event_type' => 'CXML PunchOut Order Request',
                    'action' => 'CXML OrderRequest',
                    'user_id' => $customerId,
                    'punchoutgroup_id' => $punchoutgroupId,
                    'info' => $info
                ]
            );
        }
        
        public function dispatchExceptionPunchoutRequestEvent(string $eventType, string $action, string $info): void
        {
            $this->eventManager->dispatch('exception_punchout_request_event',
                [
                    'event_type' => $eventType,
                    'action' => $action,
                    'info' => $info
                ]
            );
        }
    
    }
