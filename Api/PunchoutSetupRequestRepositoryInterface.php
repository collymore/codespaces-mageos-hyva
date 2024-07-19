<?php

namespace Develodesign\Punchout\Api;

use Develodesign\Punchout\Api\Data\PunchoutSetupRequestInterface;
use Develodesign\Punchout\Api\Data\PunchoutSetupRequestSearchResultsInterface;
use Magento\Framework\Exception\LocalizedException;

interface PunchoutSetupRequestRepositoryInterface
{
    /**
     * Save PunchoutSetupRequest
     *
     * @param PunchoutSetupRequestInterface $punchoutSetupRequest
     *
     * @return PunchoutSetupRequestInterface
     * @throws LocalizedException
     */
    public function save(
        PunchoutSetupRequestInterface $punchoutSetupRequest
    );

    /**
     * Retrieve PunchoutSetupRequest
     *
     * @param string $setupId
     *
     * @return PunchoutSetupRequestInterface
     * @throws LocalizedException
     */
    public function get($setupId);

    /**
     * Retrieve PunchoutSetupRequest matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return PunchoutSetupRequestSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete PunchoutSetupRequest
     *
     * @param PunchoutSetupRequestInterface $punchoutSetupRequest
     *
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(
        PunchoutSetupRequestInterface $punchoutSetupRequest
    );

    /**
     * Delete PunchoutSetupRequest by ID
     * @param string $setupId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($setupId);
}
