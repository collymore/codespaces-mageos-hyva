<?php

namespace Develodesign\Punchout\Model;

use Develodesign\Punchout\Api\Data\PunchoutSetupRequestInterface;
use Magento\Framework\Model\AbstractModel;

class PunchoutSetupRequest extends AbstractModel implements PunchoutSetupRequestInterface
{
    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Develodesign\Punchout\Model\ResourceModel\PunchoutSetupRequest::class);
    }
    
    public function getSetupId()
    {
        return $this->getData(self::SETUP_ID);
    }
    
    public function setSetupId($setupId)
    {
        return $this->setData(self::SETUP_ID, $setupId);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @inheritDoc
     */
    public function getPayloadId()
    {
        return $this->getData(self::PAYLOAD_ID);
    }

    /**
     * @inheritDoc
     */
    public function setPayloadId($payloadId)
    {
        return $this->setData(self::PAYLOAD_ID, $payloadId);
    }

    /**
     * @inheritDoc
     */
    public function getSenderIdentity()
    {
        return $this->getData(self::SENDER_IDENTITY);
    }

    /**
     * @inheritDoc
     */
    public function setSenderIdentity($senderIdentity)
    {
        return $this->setData(self::SENDER_IDENTITY, $senderIdentity);
    }

    /**
     * @inheritDoc
     */
    public function getReturnUrl()
    {
        return $this->getData(self::RETURN_URL);
    }

    /**
     * @inheritDoc
     */
    public function setReturnUrl($returnUrl)
    {
        return $this->setData(self::RETURN_URL, $returnUrl);
    }

    /**
     * @inheritDoc
     */
    public function getBuyerCookie()
    {
        return $this->getData(self::BUYER_COOKIE);
    }

    /**
     * @inheritDoc
     */
    public function setBuyerCookie($buyerCookie)
    {
        return $this->setData(self::BUYER_COOKIE, $buyerCookie);
    }

    /**
     * @inheritDoc
     */
    public function getAccessToken()
    {
        return $this->getData(self::ACCESS_TOKEN);
    }

    /**
     * @inheritDoc
     */
    public function setAccessToken($accessToken)
    {
        return $this->setData(self::ACCESS_TOKEN, $accessToken);
    }

    /**
     * @inheritDoc
     */
    public function getExpiryDate()
    {
        return $this->getData(self::EXPIRY_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setExpiryDate($expiryDate)
    {
        return $this->setData(self::EXPIRY_DATE, $expiryDate);
    }

    /**
     * @inheritDoc
     */
    public function getPoNumber()
    {
        return $this->getData(self::PO_NUMBER);
    }

    /**
     * @inheritDoc
     */
    public function setPoNumber($poNumber)
    {
        return $this->setData(self::PO_NUMBER, $poNumber);
    }

    /**
     * @inheritDoc
     */
    public function getOrderStatus()
    {
        return $this->getData(self::ORDER_STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setOrderStatus($orderStatus)
    {
        return $this->setData(self::ORDER_STATUS, $orderStatus);
    }

    
}
