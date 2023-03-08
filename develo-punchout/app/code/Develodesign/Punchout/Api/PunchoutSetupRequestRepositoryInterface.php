<?php

namespace Develodesign\Punchout\Api;

interface PunchoutSetupRequestRepositoryInterface
{
    /**
     * Save PunchoutSetupRequest
     *
     * @param \Develodesign\Punchout\Api\Data\PunchoutSetupRequestInterface $punchoutSetupRequest
     *
     * @return \Develodesign\Punchout\Api\Data\PunchoutSetupRequestInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Develodesign\Punchout\Api\Data\PunchoutSetupRequestInterface $punchoutSetupRequest
    );

    /**
     * Retrieve PunchoutSetupRequest
     *
     * @param string $setupId
     *
     * @return \Develodesign\Punchout\Api\Data\PunchoutSetupRequestInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($setupId);

    /**
     * Retrieve PunchoutSetupRequest matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Develodesign\Punchout\Api\Data\PunchoutSetupRequestSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete PunchoutSetupRequest
     *
     * @param \Develodesign\Punchout\Api\Data\PunchoutSetupRequestInterface $punchoutSetupRequest
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Develodesign\Punchout\Api\Data\PunchoutSetupRequestInterface $punchoutSetupRequest
    );

    /**
     * Delete PunchoutSetupRequest by ID
     * @param string $setupId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($setupId);
}
