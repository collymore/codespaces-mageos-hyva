<?php

namespace Develodesign\Punchout\Observer;

    use Develodesign\Punchout\Service\SessionService;
    use Magento\Framework\Event\Observer;
    use Magento\Framework\Event\ObserverInterface;

    class RemoveOrderLink implements ObserverInterface
    {
        private $sessionService;
        public function __construct(
            SessionService $sessionService
        ) {
            $this->sessionService = $sessionService;
        }
        public function execute(Observer $observer)
        {
            $punchoutType = $this->sessionService->getPunchoutType();
            if (in_array($punchoutType, ['cxml', 'oci'], true)) {
                $layout = $observer->getLayout();
                $layout->getUpdate()->addHandle('punchout_customer_navigation_link');
            }
            
            return $this;
        }
    }
