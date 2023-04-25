<?php
    
    namespace Develodesign\Punchout\Api;

    use Magento\Framework\Api\SearchCriteriaInterface;

interface ActivityEventLogRepositoryInterface
{
    /**
     * Save ActivityEventLog
     * @param \Develodesign\Punchout\Api\Data\ActivityEventLogInterface $activityEventLog
     * @return \Develodesign\Punchout\Api\Data\ActivityEventLogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Develodesign\Punchout\Api\Data\ActivityEventLogInterface $activityEventLog
    );
    
    /**
     * Retrieve ActivityEventLog
     * @param string $logId
     * @return \Develodesign\Punchout\Api\Data\ActivityEventLogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($logId);
    
    /**
     * Retrieve ActivityEventLog matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Develodesign\Punchout\Api\Data\ActivityEventLogSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );
    
    /**
     * Delete ActivityEventLog
     * @param \Develodesign\Punchout\Api\Data\ActivityEventLogInterface $activityEventLog
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Develodesign\Punchout\Api\Data\ActivityEventLogInterface $activityEventLog
    );
    
    /**
     * Delete ActivityEventLog by ID
     * @param string $logId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($logId);
}
