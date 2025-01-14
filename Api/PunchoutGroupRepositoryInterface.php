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
     * @param \Develodesign\Punchout\Api\Data\PunchoutGroupInterface $punchoutGroup
     * @return \Develodesign\Punchout\Api\Data\PunchoutGroupInterface
     * @throws LocalizedException
     */
    public function save(
        \Develodesign\Punchout\Api\Data\PunchoutGroupInterface $punchoutGroup
    );

    /**
     * Retrieve PunchoutGroup
     * @param string $punchoutgroupId
     * @return \Develodesign\Punchout\Api\Data\PunchoutGroupInterface
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
     * @param \Develodesign\Punchout\Api\Data\PunchoutGroupInterface $punchoutGroup
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(
        \Develodesign\Punchout\Api\Data\PunchoutGroupInterface $punchoutGroup
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
