<?php

namespace Develodesign\Punchout\Api\Data;

interface ActivityEventLogInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const LOG_ID = 'log_id';
    const EVENT_TYPE = 'event_type';
    const ACTION = 'action';
    const USER_ID = 'user_id';
    const IP = 'ip';
    const PUNCHOUTGROUP_ID = 'punchoutgroup_id';

    const INFO = 'info';
    const ERROR_MESSAGE = 'error_message';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Get Activity LogId
     * @return string|null
     */
    public function getLogId();

    /**
     * Sets Activity LogId
     * @param string $logId
     * @return \Develodesign\Punchout\Api\Data\ActivityEventLogInterface
     */
    public function setLogId($logId);

    /**
     * Get event_type
     * @return string|null
     */
    public function getEventType();

    /**
     * Set event_type
     * @param string $eventType
     * @return \Develodesign\Punchout\Api\Data\ActivityEventLogInterface
     */
    public function setEventType($eventType);

    /**
     * Get action
     * @return string|null
     */
    public function getAction();

    /**
     * Set action
     * @param string $action
     * @return \Develodesign\Punchout\Api\Data\ActivityEventLogInterface
     */
    public function setAction($action);

    /**
     * Get user_id
     * @return string|null
     */
    public function getUserId();

    /**
     * Set user_id
     * @param string $userId
     * @return \Develodesign\Punchout\Api\Data\ActivityEventLogInterface
     */
    public function setUserId($userId);

    /**
     * Get ip
     * @return string|null
     */
    public function getIp();

    /**
     * Set ip
     * @param string $ip
     * @return \Develodesign\Punchout\Api\Data\ActivityEventLogInterface
     */
    public function setIp($ip);

    /**
     * Get punchoutgroup_id
     * @return string|null
     */
    public function getPunchoutgroupId();

    /**
     * Set punchoutgroup_id
     * @param string $punchoutgroupId
     * @return \Develodesign\Punchout\Api\Data\ActivityEventLogInterface
     */
    public function setPunchoutgroupId($punchoutgroupId);

    /**
     * Get info
     * @return string|null
     */
    public function getInfo();

    /**
     * Set info
     * @param string $info
     * @return \Develodesign\Punchout\Api\Data\ActivityEventLogInterface
     */
    public function setInfo($info);

    /**
     * Get error_message
     * @return string|null
     */
    public function getErrorMessage();

    /**
     * Set error_message
     * @param string $errorMessage
     * @return \Develodesign\Punchout\Api\Data\ActivityEventLogInterface
     */
    public function setErrorMessage($errorMessage);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Develodesign\Punchout\Api\Data\ActivityEventLogInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     * @param string $updatedAt
     * @return \Develodesign\Punchout\Api\Data\ActivityEventLogInterface
     */
    public function setUpdatedAt($updatedAt);
}
