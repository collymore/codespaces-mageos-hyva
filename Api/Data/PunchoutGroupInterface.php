<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Develodesign\Punchout\Api\Data;

interface PunchoutGroupInterface
{
    const PUNCHOUTGROUP_ID = 'punchoutgroup_id';
    const GROUP_NAME = 'group_name';
    const GROUP_EMAIL = 'group_email';

    const STATUS = 'status';

    const IS_PARENT = 'is_parent';
    const PARENT_PUNCHOUT_GROUP = 'parent_punchout_group';

    const MAGENTO_CUSTOMER_GROUP = 'magento_customer_group';
    const SHARED_SECRET = 'shared_secret';
    const DUNS_IDENTITY = 'duns_identity';
    const ARIBA_NETWORK_ID = 'ariba_network_id';
    const BUSINESS_UNIT = 'business_unit';
    const OCI_PASSWORD = 'oci_password';
    const OCI_USERNAME = 'oci_username';

    const STREET = 'street';

    const CITY = 'city';

    const POSTCODE = 'postcode';

    const COUNTRY_ID = 'country_id';

    const REGION = 'region';

    const TELEPHONE = 'telephone';
    const UPDATED_AT = 'updated_at';
    
    const CREATED_AT = 'created_at';
    
    const CXML_NODE_XPATH_CONFIG_EMAIL = 'cxml_node_xpath_config_email';
    
    const CUSTOMER_ATTRIBUTES = 'customer_attributes';
    
    const CXML_NODE_TAX_PER_ITEM = 'cxml_node_tax_per_item';
    
    const CXML_NODE_TAX_MESSAGE_HEADER = 'cxml_node_tax_message_header';
    
  

    /**
     * Get punchoutgroup_id
     *
     * @return string|null
     */
    public function getPunchoutgroupId();

    /**
     * Set punchoutgroup_id
     *
     * @param string $punchoutgroupId
     *
     * @return $this
     */
    public function setPunchoutgroupId($punchoutgroupId);

    /**
     * Get group_name
     *
     * @return string|null
     */
    public function getGroupName();

    /**
     * Set group_name
     *
     * @param string $groupName
     *
     * @return $this
     */
    public function setGroupName($groupName);

    /**
     * Get status
     *
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get group_email
     *
     * @return string|null
     */
    public function getGroupEmail();

    /**
     * Set group_email
     *
     * @param string $groupEmail
     *
     * @return $this
     */
    public function setGroupEmail($groupEmail);

    /**
     * Get is_parent
     * @return string|null
     */
    public function getIsParent();

    /**
     * Set is_parent
     *
     * @param string $isParent
     *
     * @return $this
     */
    public function setIsParent($isParent);

    /**
     * Get shared_secret
     *
     * @return string|null
     */
    public function getSharedSecret();

    /**
     * Set shared_secret
     *
     * @param string $sharedSecret
     *
     * @return $this
     */
    public function setSharedSecret($sharedSecret);

    /**
     * Get duns_identity
     *
     * @return string|null
     */
    public function getDunsIdentity();

    /**
     * Set duns_identity
     *
     * @param string $dunsIdentity
     *
     * @return $this
     */
    public function setDunsIdentity($dunsIdentity);

    /**
     * Get ariba_network_id
     *
     * @return string|null
     */
    public function getAribaNetworkId();

    /**
     * Set ariba_network_id
     *
     * @param string $aribaNetworkId
     *
     * @return $this
     */
    public function setAribaNetworkId($aribaNetworkId);

    /**
     * Get business_unit
     *
     * @return string|null
     */
    public function getBusinessUnit();

    /**
     * Set business_unit
     *
     * @param string $businessUnit
     *
     * @return $this
     */
    public function setBusinessUnit($businessUnit);

    /**
     * Get oci_username
     *
     * @return string|null
     */
    public function getOciUsername();

    /**
     * Set oci_username
     *
     * @param string $ociUsername
     *
     * @return $this
     */
    public function setOciUsername($ociUsername);

    /**
     * Get oci_password
     *
     * @return string|null
     */
    public function getOciPassword();

    /**
     * Set oci_password
     *
     * @param string $ociPassword
     *
     * @return $this
     */
    public function setOciPassword($ociPassword);

    /**
     * Get parent_punchout_group
     *
     * @return string|null
     */
    public function getParentPunchoutGroup();

    /**
     * Set parent_punchout_group
     *
     * @param string $parentPunchoutGroup
     *
     * @return $this
     */
    public function setParentPunchoutGroup($parentPunchoutGroup);
    
    /**
     * Get magento_customer_group
     * @return string|null
     */
    public function getMagentoCustomerGroup();
    
    /**
     * Set magento_customer_group
     *
     * @param string $magentoCustomerGroup
     *
     * @return $this
     */
    public function setMagentoCustomerGroup($magentoCustomerGroup);

    /**
     * Get street
     *
     * @return string|null
     */
    public function getStreet();

    /**
     * Set street
     *
     * @param string $street
     *
     * @return $this
     */
    public function setStreet($street);

    /**
     * Get city
     *
     * @return string|null
     */
    public function getCity();

    /**
     * Set city
     *
     * @param string $city
     *
     * @return $this
     */
    public function setCity($city);

    /**
     * Get country_id
     *
     * @return string|null
     */
    public function getCountryId();

    /**
     * Set country_id
     *
     * @param string $countryId
     *
     * @return $this
     */
    public function setCountryId($countryId);

    /**
     * Get region
     *
     * @return string|null
     */
    public function getRegion();

    /**
     * Set region
     *
     * @param string $region
     *
     * @return $this
     */
    public function setRegion($region);

    /**
     * Get postcode
     *
     * @return string|null
     */
    public function getPostcode();

    /**
     * Set postcode
     *
     * @param string $postcode
     *
     * @return $this
     */
    public function setPostcode($postcode);

    /**
     * Get telephone
     *
     * @return string|null
     */
    public function getTelephone();

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return $this
     */
    public function setTelephone($telephone);

    /**
     * Get created_at
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     *
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated_at
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     *
     * @param string $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt);
    
    /**
     * @return string|null
     */
    public function getCxmlNodeXpathConfigEmail();
    
    /**
     * @param string $cxmlNodeXpathConfigEmail
     *
     * @return $this
     */
    public function setCxmlNodeXpathConfigEmail($cxmlNodeXpathConfigEmail);
    
    /**
     * @param string $customerAttributes
     *
     */
    public function setCustomerAttributes($customerAttributes);

    /**
     * @return string|null
     */
    public function getCustomerAttributes();
    
    /**
     * @return string|null
     */
    public function getCxmlNodeTaxPerItem();
    
    /**
     * @param string $cxmlNodeTaxPerItem
     *
     * @return $this
     */
    public function setCxmlNodeTaxPerItem($cxmlNodeTaxPerItem);
    
    /**
     * @return string|null
     */
    public function getCxmlNodeTaxMessageHeader();
    
    /**
     * @param string $cxmlNodeTaxMessageHeader
     *
     * @return $this
     */
    public function setCxmlNodeTaxMessageHeader($cxmlNodeTaxMessageHeader);
    
   
    
}
