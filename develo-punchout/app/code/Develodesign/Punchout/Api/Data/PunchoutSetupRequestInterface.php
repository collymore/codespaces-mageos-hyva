<?php

namespace Develodesign\Punchout\Api\Data;

interface PunchoutSetupRequestInterface
{
    const SETUP_ID = 'setup_id';

    const CUSTOMER_ID = 'customer_id';
    const PAYLOAD_ID = 'payload_id';
    const SENDER_IDENTITY = 'sender_identity';
    const RETURN_URL = 'return_url';

    const BUYER_COOKIE = 'buyer_cookie';
    const ACCESS_TOKEN = 'access_token';
    const EXPIRY_DATE = 'expiry_date';
    const PO_NUMBER = 'po_number';

    const ORDER_STATUS = 'order_status';

    /**
     * Get setup_id
     * @return string|null
     */
    public function getSetupId();

    /**
     * Set setup_id
     * @param string $setupId
     * @return \Develodesign\Punchout\PunchoutSetupRequest\Api\Data\PunchoutSetupRequestInterface
     */
    public function setSetupId($setupId);

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Develodesign\Punchout\PunchoutSetupRequest\Api\Data\PunchoutSetupRequestInterface
     */
    public function setCustomerId($customerId);

    /**
     * Get payload_id
     * @return string|null
     */
    public function getPayloadId();

    /**
     * Set payload_id
     * @param string $payloadId
     * @return \Develodesign\Punchout\PunchoutSetupRequest\Api\Data\PunchoutSetupRequestInterface
     */
    public function setPayloadId($payloadId);

    /**
     * Get sender_identity
     * @return string|null
     */
    public function getSenderIdentity();

    /**
     * Set sender_identity
     * @param string $senderIdentity
     * @return \Develodesign\Punchout\PunchoutSetupRequest\Api\Data\PunchoutSetupRequestInterface
     */
    public function setSenderIdentity($senderIdentity);

    /**
     * Get return_url
     * @return string|null
     */
    public function getReturnUrl();

    /**
     * Set return_url
     * @param string $returnUrl
     * @return \Develodesign\Punchout\PunchoutSetupRequest\Api\Data\PunchoutSetupRequestInterface
     */
    public function setReturnUrl($returnUrl);

    /**
     * Get buyer_cookie
     * @return string|null
     */
    public function getBuyerCookie();

    /**
     * Set buyer_cookie
     * @param string $buyerCookie
     * @return \Develodesign\Punchout\PunchoutSetupRequest\Api\Data\PunchoutSetupRequestInterface
     */
    public function setBuyerCookie($buyerCookie);

    /**
     * Get access_token
     * @return string|null
     */
    public function getAccessToken();

    /**
     * Set access_token
     * @param string $accessToken
     * @return \Develodesign\Punchout\PunchoutSetupRequest\Api\Data\PunchoutSetupRequestInterface
     */
    public function setAccessToken($accessToken);

    /**
     * Get expiry_date
     * @return string|null
     */
    public function getExpiryDate();

    /**
     * Set expiry_date
     * @param string $expiryDate
     * @return \Develodesign\Punchout\PunchoutSetupRequest\Api\Data\PunchoutSetupRequestInterface
     */
    public function setExpiryDate($expiryDate);

    /**
     * Get po_number
     * @return string|null
     */
    public function getPoNumber();

    /**
     * Set po_number
     * @param string $poNumber
     * @return \Develodesign\Punchout\PunchoutSetupRequest\Api\Data\PunchoutSetupRequestInterface
     */
    public function setPoNumber($poNumber);

    /**
     * Get order_status
     * @return string|null
     */
    public function getOrderStatus();

    /**
     * Set order_status
     * @param string $orderStatus
     * @return \Develodesign\Punchout\PunchoutSetupRequest\Api\Data\PunchoutSetupRequestInterface
     */
    public function setOrderStatus($orderStatus);
}
