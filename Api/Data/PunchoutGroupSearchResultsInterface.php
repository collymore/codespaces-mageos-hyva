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
     * @return \Develodesign\Punchout\Api\Data\PunchoutGroupInterface[]
     */
    public function getItems();

    /**
     * Set group_name list.
     *
     * @param \Develodesign\Punchout\Api\Data\PunchoutGroupInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items);
}
