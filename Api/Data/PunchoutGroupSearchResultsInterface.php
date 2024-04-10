<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Develodesign\Punchout\Api\Data;

use Develodesign\Punchout\Api\Data\PunchoutGroupInterface;

interface PunchoutGroupSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get PunchoutGroup list.
     *
     * @return Develodesign\Punchout\Api\Data\PunchoutGroupInterface[]
     */
    public function getItems();

    /**
     * Set group_name list.
     *
     * @param Develodesign\Punchout\Api\Data\PunchoutGroupInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items);
}
