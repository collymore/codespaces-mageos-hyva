<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Develodesign\Punchout\Api;

use Develodesign\Punchout\Api\Data\PunchoutGroupInterface;
use Develodesign\Punchout\Api\Data\PunchoutGroupSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

interface PunchoutGroupRepositoryInterface
{

    /**
     * Save PunchoutGroup
     * @param PunchoutGroupInterface $punchoutGroup
     * @return PunchoutGroupInterface
     * @throws LocalizedException
     */
    public function save(
        PunchoutGroupInterface $punchoutGroup
    );

    /**
     * Retrieve PunchoutGroup
     * @param string $punchoutgroupId
     * @return PunchoutGroupInterface
     * @throws LocalizedException
     */
    public function get($punchoutgroupId);

    /**
     * Retrieve PunchoutGroup matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return PunchoutGroupSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete PunchoutGroup
     * @param PunchoutGroupInterface $punchoutGroup
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(
        PunchoutGroupInterface $punchoutGroup
    );

    /**
     * Delete PunchoutGroup by ID
     * @param string $punchoutgroupId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($punchoutgroupId);
}
