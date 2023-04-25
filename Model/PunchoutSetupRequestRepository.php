<?php
    /**
     * Copyright ©  All rights reserved.
     * See COPYING.txt for license details.
     */
    declare(strict_types=1);

namespace Develodesign\Punchout\Model;

use Develodesign\Punchout\Api\Data\PunchoutSetupRequestInterface;
use Develodesign\Punchout\Api\Data\PunchoutSetupRequestInterfaceFactory;
use Develodesign\Punchout\Api\Data\PunchoutSetupRequestSearchResultsInterfaceFactory;
use Develodesign\Punchout\Api\PunchoutSetupRequestRepositoryInterface;
use Develodesign\Punchout\Model\ResourceModel\PunchoutSetupRequest as ResourcePunchoutSetupRequest;
use Develodesign\Punchout\Model\ResourceModel\PunchoutSetupRequest\CollectionFactory as PunchoutSetupRequestCollectionFactory;//phpcs:ignore
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class PunchoutSetupRequestRepository implements PunchoutSetupRequestRepositoryInterface
{

    /**
     * @var PunchoutSetupRequestCollectionFactory
     */
    protected $punchoutSetupRequestCollectionFactory;

    /**
     * @var PunchoutSetupRequestInterface
     */
    protected $punchoutSetupRequestFactory;

    /**
     * @var ResourcePunchoutSetupRequest
     */
    protected $resource;

    /**
     * @var PunchoutSetupRequest
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;
    
    /**
     * @param ResourcePunchoutSetupRequest $resource
     * @param PunchoutSetupRequestInterfaceFactory $punchoutSetupRequestFactory
     * @param PunchoutSetupRequestCollectionFactory $punchoutSetupRequestCollectionFactory
     * @param PunchoutSetupRequestSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourcePunchoutSetupRequest $resource,
        PunchoutSetupRequestInterfaceFactory $punchoutSetupRequestFactory,
        PunchoutSetupRequestCollectionFactory $punchoutSetupRequestCollectionFactory,
        PunchoutSetupRequestSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->punchoutSetupRequestFactory = $punchoutSetupRequestFactory;
        $this->punchoutSetupRequestCollectionFactory = $punchoutSetupRequestCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(
        PunchoutSetupRequestInterface $punchoutSetupRequest
    ) {
        try {
            $this->resource->save($punchoutSetupRequest);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the punchoutSetupRequest: %1',
                $exception->getMessage()
            ));
        }
        return $punchoutSetupRequest;
    }

    /**
     * @inheritDoc
     */
    public function get($setupId)
    {
        $punchoutSetupRequest = $this->punchoutSetupRequestFactory->create();
        $this->resource->load($punchoutSetupRequest, $setupId);
        if (!$punchoutSetupRequest->getId()) {
            throw new NoSuchEntityException(__('PunchoutSetupRequest with id "%1" does not exist.', $setupId));
        }
        return $punchoutSetupRequest;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->punchoutSetupRequestCollectionFactory->create();

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
    public function delete(
        PunchoutSetupRequestInterface $punchoutSetupRequest
    ) {
        try {
            $punchoutSetupRequestModel = $this->punchoutSetupRequestFactory->create();
            $this->resource->load($punchoutSetupRequestModel, $punchoutSetupRequest->getPunchoutsetuprequestId());
            $this->resource->delete($punchoutSetupRequestModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the PunchoutSetupRequest: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($setupId)
    {
        return $this->delete($this->get($setupId));
    }
}
