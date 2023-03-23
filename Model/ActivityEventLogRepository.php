<?php

namespace Develodesign\Punchout\Model;

    use Develodesign\Punchout\Api\ActivityEventLogRepositoryInterface;
    use Develodesign\Punchout\Api\Data\ActivityEventLogInterface;
    use Develodesign\Punchout\Api\Data\ActivityEventLogInterfaceFactory;
    use Develodesign\Punchout\Api\Data\ActivityEventLogSearchResultsInterfaceFactory;
    use Develodesign\Punchout\Model\ResourceModel\ActivityEventLog as ResourceActivityEventLog;
    use Develodesign\Punchout\Model\ResourceModel\ActivityEventLog\CollectionFactory as ActivityEventLogCollectionFactory;
    use Magento\Framework\Api\DataObjectHelper;
    use Magento\Framework\Api\ExtensibleDataObjectConverter;
    use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
    use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
    use Magento\Framework\Api\SearchCriteriaInterface;
    use Magento\Framework\Exception\CouldNotDeleteException;
    use Magento\Framework\Exception\CouldNotSaveException;
    use Magento\Framework\Exception\NoSuchEntityException;
    use Magento\Framework\Reflection\DataObjectProcessor;
    use Magento\Store\Model\StoreManagerInterface;

    class ActivityEventLogRepository implements ActivityEventLogRepositoryInterface
    {
        /**
         * @var ResourceActivityEventLog
         */
        protected $resource;

        /**
         * @var ExtensibleDataObjectConverter
         */
        protected $extensibleDataObjectConverter;

        /**
         * @var ActivityEventLogSearchResultsInterfaceFactory
         */
        protected $searchResultsFactory;

        /**
         * @var ActivityEventLogFactory
         */
        protected $activityEventLogFactory;

        /**
         * @var ActivityEventLogCollectionFactory
         */
        protected $activityEventLogCollectionFactory;

        /**
         * @var ActivityEventLogInterfaceFactory
         */
        protected $dataActivityEventLogFactory;

        private $storeManager;

        /**
         * @var DataObjectHelper
         */
        protected $dataObjectHelper;

        /**
         * @var DataObjectProcessor
         */
        protected $dataObjectProcessor;

        /**
         * @var JoinProcessorInterface
         */
        protected $extensionAttributesJoinProcessor;

        /**
         * @var CollectionProcessorInterface
         */
        private $collectionProcessor;

        /**
         * @param ResourceActivityEventLog $resource
         * @param ActivityEventLogFactory $activityEventLogFactory
         * @param ActivityEventLogInterfaceFactory $dataActivityEventLogFactory
         * @param ActivityEventLogCollectionFactory $activityEventLogCollectionFactory
         * @param ActivityEventLogSearchResultsInterfaceFactory $searchResultsFactory
         * @param DataObjectHelper $dataObjectHelper
         * @param DataObjectProcessor $dataObjectProcessor
         * @param StoreManagerInterface $storeManager
         * @param CollectionProcessorInterface $collectionProcessor
         * @param JoinProcessorInterface $extensionAttributesJoinProcessor
         * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
         */
        public function __construct(
            ResourceActivityEventLog                      $resource,
            ActivityEventLogFactory                       $activityEventLogFactory,
            ActivityEventLogInterfaceFactory              $dataActivityEventLogFactory,
            ActivityEventLogCollectionFactory             $activityEventLogCollectionFactory,
            ActivityEventLogSearchResultsInterfaceFactory $searchResultsFactory,
            DataObjectHelper                              $dataObjectHelper,
            DataObjectProcessor                           $dataObjectProcessor,
            StoreManagerInterface                         $storeManager,
            CollectionProcessorInterface                  $collectionProcessor,
            JoinProcessorInterface                        $extensionAttributesJoinProcessor,
            ExtensibleDataObjectConverter                 $extensibleDataObjectConverter
        ) {
            $this->resource = $resource;
            $this->activityEventLogFactory = $activityEventLogFactory;
            $this->activityEventLogCollectionFactory = $activityEventLogCollectionFactory;
            $this->searchResultsFactory = $searchResultsFactory;
            $this->dataObjectHelper = $dataObjectHelper;
            $this->dataActivityEventLogFactory = $dataActivityEventLogFactory;
            $this->dataObjectProcessor = $dataObjectProcessor;
            $this->storeManager = $storeManager;
            $this->collectionProcessor = $collectionProcessor;
            $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
            $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        }

        /**
         * @param ActivityEventLogInterface $activityEventLog
         * @return ActivityEventLogInterface
         * @throws CouldNotSaveException
         */
        public function save(ActivityEventLogInterface $activityEventLog)
        {
            $activityEventLogData = $this->extensibleDataObjectConverter->toNestedArray(
                $activityEventLog,
                [],
                ActivityEventLogInterface::class
            );

            $activityEventLogModel = $this->activityEventLogFactory->create()->setData($activityEventLogData);

            try {
                $this->resource->save($activityEventLogModel);
            } catch (\Exception $exception) {
                throw new CouldNotSaveException(__(
                    'Could not save the activityEventLog: %1',
                    $exception->getMessage()
                ));
            }
            return $activityEventLogModel->getDataModel();
        }

        /**
         * @param string $logId
         * @return ActivityEventLogInterface
         * @throws NoSuchEntityException
         */
        public function get($logId)
        {
            $activityEventLog = $this->activityEventLogFactory->create();
            $this->resource->load($activityEventLog, $logId);
            if (!$activityEventLog->getId()) {
                throw new NoSuchEntityException(__('ActivityEventLog with id "%1" does not exist.', $logId));
            }
            return $activityEventLog->getDataModel();
        }

        /**
         * @param SearchCriteriaInterface $searchCriteria
         * @return \Develodesign\Punchout\Api\Data\ActivityEventLogSearchResultsInterface
         */
        public function getList(SearchCriteriaInterface $searchCriteria)
        {
            $collection = $this->activityEventLogCollectionFactory->create();

            $this->extensionAttributesJoinProcessor->process(
                $collection,
                ActivityEventLogInterface::class
            );

            $this->collectionProcessor->process($searchCriteria, $collection);

            $searchResults = $this->searchResultsFactory->create();
            $searchResults->setSearchCriteria($searchCriteria);

            $items = [];
            foreach ($collection as $model) {
                $items[] = $model->getDataModel();
            }

            $searchResults->setItems($items);
            $searchResults->setTotalCount($collection->getSize());
            return $searchResults;
        }

        /**
         * @param ActivityEventLogInterface $activityEventLog
         * @return bool
         * @throws CouldNotDeleteException
         */
        public function delete(ActivityEventLogInterface $activityEventLog)
        {
            try {
                $activityEventLogModel = $this->activityEventLogFactory->create();
                $this->resource->load($activityEventLogModel, $activityEventLog->getLogId());
                $this->resource->delete($activityEventLogModel);
            } catch (\Exception $exception) {
                throw new CouldNotDeleteException(__(
                    'Could not delete the ActivityEventLog: %1',
                    $exception->getMessage()
                ));
            }
            return true;
        }

        /**
         * @param string $logId
         * @return bool
         * @throws CouldNotDeleteException
         * @throws \Magento\Framework\Exception\LocalizedException
         */
        public function deleteById($logId)
        {
            return $this->delete($this->get($logId));
        }
    }
