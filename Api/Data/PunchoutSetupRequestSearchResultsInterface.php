<?php

namespace Develodesign\Punchout\Api\Data;

interface PunchoutSetupRequestSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get PunchoutSetupRequest list.
     * @return \Develo\Punchout\Api\Data\PunchoutSetupRequestInterface[]
     */
    public function getItems();

    /**
     * Set setup_id list.
     * @param \Develo\Punchout\Api\Data\PunchoutSetupRequestInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
