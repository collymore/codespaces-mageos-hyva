<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Develodesign\Punchout\Model;

use Develodesign\Punchout\Api\Data\PunchoutGroupInterface;
use Magento\Framework\Model\AbstractModel;

class PunchoutGroup extends AbstractModel implements PunchoutGroupInterface
{
    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Develodesign\Punchout\Model\ResourceModel\PunchoutGroup::class);
    }

    /**
     * @inheritDoc
     */
    public function getPunchoutgroupId()
    {
        return $this->getData(self::PUNCHOUTGROUP_ID);
    }

    /**
     * @inheritDoc
     */
    public function setPunchoutgroupId($punchoutgroupId)
    {
        return $this->setData(self::PUNCHOUTGROUP_ID, $punchoutgroupId);
    }

    /**
     * @inheritDoc
     */
    public function getGroupName()
    {
        return $this->getData(self::GROUP_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setGroupName($groupName)
    {
        return $this->setData(self::GROUP_NAME, $groupName);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function getGroupEmail()
    {
        return $this->getData(self::GROUP_EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setGroupEmail($groupEmail)
    {
        return $this->setData(self::GROUP_EMAIL, $groupEmail);
    }

    /**
     * @inheritDoc
     */
    public function getSharedSecret()
    {
        return $this->getData(self::SHARED_SECRET);
    }

    /**
     * @inheritDoc
     */
    public function setSharedSecret($sharedSecret)
    {
        return $this->setData(self::SHARED_SECRET, $sharedSecret);
    }

    /**
     * @inheritDoc
     */
    public function getDunsIdentity()
    {
        return $this->getData(self::DUNS_IDENTITY);
    }

    /**
     * @inheritDoc
     */
    public function setDunsIdentity($dunsIdentity)
    {
        return $this->setData(self::DUNS_IDENTITY, $dunsIdentity);
    }

    /**
     * @inheritDoc
     */
    public function getAribaNetworkId()
    {
        return $this->getData(self::ARIBA_NETWORK_ID);
    }

    /**
     * @inheritDoc
     */
    public function setAribaNetworkId($aribaNetworkId)
    {
        return $this->setData(self::ARIBA_NETWORK_ID, $aribaNetworkId);
    }

    /**
     * @inheritDoc
     */
    public function getBusinessUnit()
    {
        return $this->getData(self::BUSINESS_UNIT);
    }

    /**
     * @inheritDoc
     */
    public function setBusinessUnit($businessUnit)
    {
        return $this->setData(self::BUSINESS_UNIT, $businessUnit);
    }

    /**
     * @inheritDoc
     */
    public function getOciUsername()
    {
        return $this->getData(self::OCI_USERNAME);
    }

    /**
     * @inheritDoc
     */
    public function setOciUsername($ociUsername)
    {
        return $this->setData(self::OCI_USERNAME, $ociUsername);
    }

    /**
     * @inheritDoc
     */
    public function getOciPassword()
    {
        return $this->getData(self::OCI_PASSWORD);
    }

    /**
     * @inheritDoc
     */
    public function setOciPassword($ociPassword)
    {
        return $this->setData(self::OCI_PASSWORD, $ociPassword);
    }

    /**
     * @inheritDoc
     */
    public function getParentPunchoutGroup()
    {
        return $this->getData(self::PARENT_PUNCHOUT_GROUP);
    }

    /**
     * @inheritDoc
     */
    public function setParentPunchoutGroup($parentPunchoutGroup)
    {
        return $this->setData(self::PARENT_PUNCHOUT_GROUP, $parentPunchoutGroup);
    }

    /**
     * @inheritDoc
     */
    public function getMagentoCustomerGroup()
    {
        return $this->getData(self::MAGENTO_CUSTOMER_GROUP);
    }

    /**
     * @inheritDoc
     */
    public function setMagentoCustomerGroup($magentoCustomerGroup)
    {
        return $this->setData(self::MAGENTO_CUSTOMER_GROUP, $magentoCustomerGroup);
    }

    /**
     * @inheritDoc
     */
    public function getStreet()
    {
        return $this->getData(self::STREET);
    }

    /**
     * @inheritDoc
     */
    public function setStreet($street)
    {
        return $this->setData(self::STREET, $street);
    }

    /**
     * @inheritDoc
     */
    public function getCity()
    {
        return $this->getData(self::CITY);
    }

    /**
     * @inheritDoc
     */
    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * @inheritDoc
     */
    public function getCountryId()
    {
        return $this->getData(self::COUNTRY_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCountryId($countryId)
    {
        return $this->setData(self::COUNTRY_ID, $countryId);
    }

    /**
     * @inheritDoc
     */
    public function getRegion()
    {
        return $this->getData(self::REGION);
    }

    /**
     * @inheritDoc
     */
    public function setRegion($region)
    {
        return $this->setData(self::REGION, $region);
    }

    /**
     * @inheritDoc
     */
    public function getPostcode()
    {
        return $this->getData(self::POSTCODE);
    }

    /**
     * @inheritDoc
     */
    public function setPostcode($postcode)
    {
        return $this->setData(self::POSTCODE, $postcode);
    }

    /**
     * @inheritDoc
     */
    public function getTelephone()
    {
        return $this->getData(self::TELEPHONE);
    }

    /**
     * @inheritDoc
     */
    public function setTelephone($telephone)
    {
        return $this->setData(self::TELEPHONE, $telephone);
    }

    /**
     * @inheritDoc
     */
    public function getIsParent()
    {
        return $this->getData(self::IS_PARENT);
    }

    /**
     * @inheritDoc
     */
    public function setIsParent($isParent)
    {
        return $this->setData(self::IS_PARENT, $isParent);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
    
    public function getCxmlNodeXpathConfigEmail()
    {
        return $this->getData(self::CXML_NODE_XPATH_CONFIG_EMAIL);
    }
    
    public function setCxmlNodeXpathConfigEmail($cxmlNodeXpathConfigEmail)
    {
        return $this->setData(self::CXML_NODE_XPATH_CONFIG_EMAIL, $cxmlNodeXpathConfigEmail);
    }
    
    public function getCustomerAttributes()
    {
        return $this->getData(self::CUSTOMER_ATTRIBUTES);
    }
    
    public function setCustomerAttributes($customerAttributes)
    {
        return $this->setData(self::CUSTOMER_ATTRIBUTES, $customerAttributes);
    }
    
    public function getCxmlNodeTaxPerItem()
    {
        return $this->getData(self::CXML_NODE_TAX_PER_ITEM);
    }
    
    public function setCxmlNodeTaxPerItem($cxmlNodeTaxPerItem)
    {
        return $this->setData(self::CXML_NODE_TAX_PER_ITEM, $cxmlNodeTaxPerItem);
    }
    
    public function getCxmlNodeTaxMessageHeader()
    {
        return $this->getData(self::CXML_NODE_TAX_MESSAGE_HEADER);
    }
    
    public function setCxmlNodeTaxMessageHeader($cxmlNodeTaxMessageHeader)
    {
        return $this->setData(self::CXML_NODE_TAX_MESSAGE_HEADER, $cxmlNodeTaxMessageHeader);
    }
    
    public function getCxmlNodeShippingMessageHeader()
    {
        return $this->getData(self::CXML_NODE_SHIPPING_MESSAGE_HEADER);
    }
    
    public function setCxmlNodeShippingMessageHeader($cxmlNodeShippingMessageHeader)
    {
        return $this->setData(self::CXML_NODE_SHIPPING_MESSAGE_HEADER, $cxmlNodeShippingMessageHeader);
    }
    
}
