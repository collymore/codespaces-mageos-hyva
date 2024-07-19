<?php

namespace Develodesign\Punchout\Service;

use Develodesign\Punchout\Model\ResourceModel\PunchoutGroup\CollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Develodesign\Punchout\Model\PunchoutGroup;
use Magento\Quote\Model\Quote;

class PunchoutGroupService
{
    /**
     * @var CollectionFactory
     */
    private $punchoutGroupCollection;
    public function __construct(
        CollectionFactory $punchoutGroupCollection
    ) {
        $this->punchoutGroupCollection = $punchoutGroupCollection;
    }
    
    /**
     * Loads a punchout group by sharedsecret and Duns Id
     * @return PunchoutGroup
     */
    public function loadPunchOutGroupBySecretDuns($sharedSecret, $dunsIdentity): PunchoutGroup
    {
        return $this->punchoutGroupCollection->create()
            ->addFieldToFilter('shared_secret', ['eq' => $sharedSecret])
            ->addFieldToFilter('duns_identity', ['eq' => $dunsIdentity])
            ->getFirstItem();
    }

    /**
     * Loads a punchout group by sharedsecret and aribanetworkId
     * @return PunchoutGroup
     */
    public function loadPunchOutGroupByAribaNetworkSecret($sharedSecret, $aribaNetworkId): PunchoutGroup
    {
        return $this->punchoutGroupCollection->create()
            ->addFieldToFilter('ariba_network_id', ['eq' => $aribaNetworkId])
            ->addFieldToFilter('shared_secret', ['eq' => $sharedSecret])
            ->getFirstItem();
    }

    /**
     * Loads a punchout group by sharedsecret and duns or aribanetworkId
     * @throws NoSuchEntityException
     * @return PunchoutGroup
     */
    public function loadPunchOutGroupByCredentials($sharedSecret, $dunsIdentity, $aribaNetworkId): PunchoutGroup
    {
        $punchoutGroup = $this->loadPunchOutGroupBySecretDuns($sharedSecret, $dunsIdentity);

        // PunchOutGroup not set and Ariba Network ID is not empty, load it by Ariba Network ID
        if (!$punchoutGroup->getPunchoutgroupId() && $aribaNetworkId) {
            $punchoutGroup = $this->loadPunchOutGroupByAribaNetworkSecret($sharedSecret, $aribaNetworkId);
        }

        if (!$punchoutGroup->getPunchoutgroupId()) {
            throw new NoSuchEntityException(
                __(
                    'No PunchoutGroup Shared Secret: %fieldValue, Duns: %field2Value, Ariba: %field3Value',
                    [
                        'fieldName'   => 'sharedSecret',
                        'fieldValue'  => $sharedSecret,
                        'field2Name'  => 'dunsIdentity',
                        'field2Value' => $dunsIdentity,
                        'field3Name'  => 'aribaNetworkId',
                        'field3Value' => $aribaNetworkId,
                    ]
                )
            );
        }
        return $punchoutGroup;
    }
    
    /**
     * Loads a punchoutGroup by OCI login credentials
     * @return PunchoutGroup
     */
    public function loadPunchOutGroupByOciCredentials(string $username, string $password): ?PunchoutGroup
    {
        $punchoutGroup = $this->punchoutGroupCollection->create()
            ->addFieldToFilter('oci_username', ['eq' => $username])
            ->addFieldToFilter('oci_password', ['eq' => $password]);
        if (!($punchoutGroup->count() > 0)) {
            return null;
        }
        return $punchoutGroup->getFirstItem();
    }
    
    /**
     * Loads a punchout group by punchoutgroup_id
     * @return ?PunchoutGroup
     */
    public function loadPunchOutGroupById(int $punchoutGroupId) : ?PunchoutGroup
    {
        $punchoutGroup = $this->punchoutGroupCollection->create()
            ->addFieldToFilter('punchoutgroup_id', ['eq' => $punchoutGroupId]);
        if ($punchoutGroup->count() > 0) {
            return $punchoutGroup->getFirstItem();
        }
        return null;
    }
    
    /**
     * Get Punchout Group configuration
     * @param PunchoutGroup $punchoutGroup
     * @param Quote $quote
     * @return array
     */
    public function getPunchoutGroupConfig(PunchoutGroup $punchoutGroup, Quote $quote): array
    {
        $config = [
            'grand_total' => $quote->getGrandTotal(),
            'punchoutgroup_duns' => $punchoutGroup->getDunsIdentity()
        ];
        
        // Add tax per item to the config array
        if ((int)$punchoutGroup->getCxmlNodeTaxPerItem() === 1) {
            $config['cxml_node_tax_per_item'] = $punchoutGroup->getCxmlNodeTaxPerItem();
        }
        
        // Add tax information to the config array
        if ((int)$punchoutGroup->getCxmlNodeTaxMessageHeader() === 1) {
            $totals = $quote->getTotals();
            $tax = isset($totals['tax']) ? $totals['tax']->getValue() : 0;
            $config['cxml_node_tax_message_header'] = $tax;
        }
        
        // Add shipping cost to the config array
        if ((int)$punchoutGroup->getCxmlNodeShippingMessageHeader() === 1) {
            $shippingCost = $quote->getShippingAddress()->getShippingAmount();
            $shippingMethod = $quote->getShippingAddress()->getShippingMethod();
            $config['cxml_node_shipping_message_header'] = $shippingCost;
            $config['cxml_node_shipping_method'] = $shippingMethod;
        }
        
        return $config;
    }
}
