<?php
    
    namespace Develodesign\Punchout\Model\Data;
    
    use Develodesign\Punchout\Api\Data\ActivityEventLogInterface;

class ActivityEventLog extends \Magento\Framework\Api\AbstractExtensibleObject implements ActivityEventLogInterface
{
    /**
     * @return string|null
     */
    public function getLogId()
    {
        return $this->_get(self::LOG_ID);
    }
    
    /**
     * @param string $logId
     *
     * @return ActivityEventLogInterface|ActivityEventLog
     */
    public function setLogId($logId)
    {
        return $this->setData(self::LOG_ID, $logId);
    }
    
    /**
     * @return string|null
     */
    public function getEventType()
    {
        return $this->_get(self::EVENT_TYPE);
    }
    
    /**
     * @param string $eventType
     * @return ActivityEventLogInterface|ActivityEventLog
     */
    public function setEventType($eventType)
    {
        return $this->setData(self::EVENT_TYPE, $eventType);
    }
    
    /**
     * @return string|null
     */
    public function getAction()
    {
        return $this->_get(self::ACTION);
    }
    
    /**
     * @param string $action
     * @return ActivityEventLogInterface|ActivityEventLog
     */
    public function setAction($action)
    {
        return $this->setData(self::ACTION, $action);
    }
    
    /**
     * @return string|null
     */
    public function getUserId()
    {
        return $this->_get(self::USER_ID);
    }
    
    /**
     * @param string $userId
     * @return ActivityEventLogInterface|ActivityEventLog
     */
    public function setUserId($userId)
    {
        return $this->setData(self::USER_ID, $userId);
    }

    /**
     * @return string|null
     */
    public function getIp()
    {
        return $this->_get(self::IP);
    }
    
    /**
     * @param string $ip
     * @return ActivityEventLogInterface|ActivityEventLog
     */
    public function setIp($ip)
    {
        return $this->setData(self::IP, $ip);
    }
    
    /**
     * @return string|null
     */
    public function getPunchoutgroupId()
    {
        return $this->_get(self::PUNCHOUTGROUP_ID);
    }
    
    /**
     * @param string $punchoutgroupId
     * @return ActivityEventLogInterface|ActivityEventLog
     */
    public function setPunchoutgroupId($punchoutgroupId)
    {
        return $this->setData(self::PUNCHOUTGROUP_ID, $punchoutgroupId);
    }
    
    /**
     * @return string|null
     */
    public function getInfo()
    {
        return $this->_get(self::INFO);
    }
    
    /**
     * @param string $info
     * @return ActivityEventLogInterface|ActivityEventLog
     */
    public function setInfo($info)
    {
        return $this->setData(self::INFO, $info);
    }
    
    /**
     * @return string|null
     */
    public function getErrorMessage()
    {
        return $this->_get(self::ERROR_MESSAGE);
    }
    
    /**
     * @param string $errorMessage
     * @return ActivityEventLog|\Develodesign\Punchout\Api\Data\ActivityEventLogInterface
     */
    public function setErrorMessage($errorMessage)
    {
        return $this->setData(self::ERROR_MESSAGE, $errorMessage);
    }
    
    /**
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }
    
    /**
     * @param string $createdAt
     * @return ActivityEventLogInterface|ActivityEventLog
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
    
    /**
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->_get(self::UPDATED_AT);
    }
    
    /**
     * @param string $updatedAt
     * @return ActivityEventLogInterface|ActivityEventLog
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
    
    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Develodesign\Punchout\Api\Data\ActivityEventLogExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }
    
    /**
     * Set an extension attributes object.
     *
     * @param \Develodesign\Punchout\Api\Data\ActivityEventLogExtensionInterface $extensionAttributes
     *
     * @return ActivityEventLog
     */
    public function setExtensionAttributes(
        \Develodesign\Punchout\Api\Data\ActivityEventLogExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
