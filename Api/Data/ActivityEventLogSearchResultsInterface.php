<?php

namespace Develodesign\Punchout\Api\Data;

interface ActivityEventLogSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get ActivityEventLog list.
     * @return \Develodesign\Punchout\Api\Data\ActivityEventLogInterface[]
     */
    public function getItems();

    /**
     * Set event_type list.
     * @param \Develodesign\Punchout\Api\Data\ActivityEventLogInterface[] $items
     * @return \Develodesign\Punchout\Api\Data\ActivityEventLogSearchResultsInterface
     */
    public function setItems(array $items);
}
