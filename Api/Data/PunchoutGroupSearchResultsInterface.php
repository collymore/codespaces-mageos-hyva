<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Develodesign\Punchout\Api\Data;

interface PunchoutGroupSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get PunchoutGroup list.
     *
     * @return PunchoutGroupInterface[]
     */
    public function getItems();

    /**
     * Set group_name list.
     *
     * @param PunchoutGroupInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items);
}
