<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Develodesign\Punchout\Model;

use Develodesign\Punchout\Api\Data\PunchoutGroupInterface;
use Develodesign\Punchout\Api\Data\PunchoutGroupInterfaceFactory;
use Develodesign\Punchout\Api\Data\PunchoutGroupSearchResultsInterfaceFactory;
use Develodesign\Punchout\Api\PunchoutGroupRepositoryInterface;
use Develodesign\Punchout\Model\ResourceModel\PunchoutGroup as ResourcePunchoutGroup;
use Develodesign\Punchout\Model\ResourceModel\PunchoutGroup\CollectionFactory as PunchoutGroupCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class PunchoutGroupRepository implements PunchoutGroupRepositoryInterface
{
    protected ResourcePunchoutGroup $resource;

    protected PunchoutGroupInterfaceFactory $punchoutGroupFactory;

    protected CollectionProcessorInterface $collectionProcessor;

    protected PunchoutGroupCollectionFactory $punchoutGroupCollectionFactory;
    
    protected PunchoutGroupSearchResultsInterfaceFactory|PunchoutGroup $searchResultsFactory;

    /**
     * @param ResourcePunchoutGroup $resource
     * @param PunchoutGroupInterfaceFactory $punchoutGroupFactory
     * @param PunchoutGroupCollectionFactory $punchoutGroupCollectionFactory
     * @param PunchoutGroupSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourcePunchoutGroup $resource,
        PunchoutGroupInterfaceFactory $punchoutGroupFactory,
        PunchoutGroupCollectionFactory $punchoutGroupCollectionFactory,
        PunchoutGroupSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->punchoutGroupFactory = $punchoutGroupFactory;
        $this->punchoutGroupCollectionFactory = $punchoutGroupCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(PunchoutGroupInterface $punchoutGroup)
    {
        try {
            $this->resource->save($punchoutGroup);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the PunchoutGroup: %1 record',
                $exception->getMessage()
            ));
        }
        return $punchoutGroup;
    }

    /**
     * @inheritDoc
     */
    public function get($punchoutGroupId)
    {
        $punchoutGroup = $this->punchoutGroupFactory->create();
        $this->resource->load($punchoutGroup, $punchoutGroupId);
        if (!$punchoutGroup->getId()) {
            throw new NoSuchEntityException(__('PunchoutGroup record with id "%1" does not exist.', $punchoutGroupId));
        }
        return $punchoutGroup;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->punchoutGroupCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(PunchoutGroupInterface $punchoutGroup)
    {
        try {
            $punchoutGroupModel = $this->punchoutGroupFactory->create();
            $this->resource->load($punchoutGroupModel, $punchoutGroup->getPunchoutgroupId());
            $this->resource->delete($punchoutGroupModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the PunchoutGroup: %1 record',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($punchoutGroupId)
    {
        return $this->delete($this->get($punchoutGroupId));
    }
}
