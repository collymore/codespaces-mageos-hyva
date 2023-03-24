<?php

namespace Develodesign\Punchout\Controller\Index;

    use Develodesign\Punchout\Model\ActivityEventLogFactory;
    use Develodesign\Punchout\Service\PunchoutGroupService;
    use Magento\Customer\Model\Session as CustomerSession;
    use Magento\Framework\App\Action\Action;
    use Magento\Framework\App\Action\Context;
    use Magento\Framework\App\ResponseInterface;
    use Magento\Framework\Controller\Result\Json;
    use Magento\Framework\Controller\Result\JsonFactory;
    use Magento\Framework\Controller\ResultInterface;

    class Ajaxlogger extends Action
    {

        /**
         * @var JsonFactory
         */
        protected $jsonFactory;

        /**
         * @var ActivityEventLogFactory
         */
        protected $activityEventLogFactory;

        /**
         * @var CustomerSession
         */
        protected $customerSession;

        /**
         * @var PunchoutGroupService
         */
        protected $punchoutGroupService;

        public function __construct(
            Context $context,
            JsonFactory $jsonFactory,
            \Develodesign\LogActivity\Model\ActivityEventLogFactory $activityEventLogFactory,
            CustomerSession $customerSession,
            PunchoutGroupService $punchoutGroupService
        ) {
            $this->jsonFactory = $jsonFactory;
            $this->activityEventLogFactory = $activityEventLogFactory;
            $this->customerSession = $customerSession;
            $this->punchoutGroupService = $punchoutGroupService;
            parent::__construct($context);
        }

        /**
         * @return ResponseInterface|Json|ResultInterface
         */
        public function execute()
        {
            $resultJson = $this->jsonFactory->create();
            $requestPostData = $this->getRequest()->getPost()->toArray();
            $response['error'] = true;
            /** @var \Develodesign\Punchout\Model\ActivityEventLog $activityEventLog */
            $activityEventLog = $this->activityEventLogFactory->create();

            try {
                $punchoutGroupId = '';
                $customer = $this->customerSession->getCustomer();
                if ($customer) {
                    $punchoutGroup = $this->punchoutGroupService->loadPunchOutGroupById((int)$customer->getPunchoutGroup());
                    if ($punchoutGroup) {
                        $punchoutGroupId = $punchoutGroup->getPunchoutgroupId();
                    }
                }

                $activityEventLog->setData([
                    'event_type' => (string)$requestPostData['event_type'],
                    'action' => (string)$requestPostData['action'],
                    'user_id' => $this->customerSession->getId(),
                    'punchoutgroup_id' => $punchoutGroupId,
                    'info' => (string)$requestPostData['info']
                ]);

                $activityEventLog->save();
                $response['error'] = false;
            } catch (\Exception $e) {
                $response['error'] = true;
            }
            return $resultJson->setData($response);
        }
    }
