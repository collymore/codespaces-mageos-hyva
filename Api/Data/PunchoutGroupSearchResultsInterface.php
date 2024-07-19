<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Develodesign\Punchout\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface PunchoutGroupSearchResultsInterface extends SearchResultsInterface
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
