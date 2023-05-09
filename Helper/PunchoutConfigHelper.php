<?php

namespace Develodesign\Punchout\Helper;

    use Magento\Framework\App\Config\ScopeConfigInterface;
    use Magento\Framework\App\Helper\AbstractHelper;
    use Magento\Store\Model\StoreManagerInterface;

class PunchoutConfigHelper extends AbstractHelper
{
    /**
     * StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }
    
    /**
     * Returns gloabl UOM value for default product attribute value
     */
    public function getConfigUOM()
    {
        return $this->getConfiguredValue('develodesign_punchout/defaults/uom');
    }

    /**
     * Returns the default shipping method value
     */
    public function getDefaultShippingMethod()
    {
        return $this->getConfiguredValue('develodesign_punchout/defaults/default_shipping');
    }
        
    /**
     * Returns if Punchout should Auto create customers
     */
    public function getConfigAutoCreate()
    {
        return $this->getConfiguredFlag('develodesign_punchout/customer/auto_create_user');
    }
        
    /**
     * Returns the Punchout Cart Button Label
     */
    public function getConfigTransferButtonLabel()
    {
        return $this->getConfiguredValue('develodesign_punchout/display/transfer_button_label');
    }

    /**
     * Returns the Modal content shown after returning Cxml cart
     */
    public function getTransferModalContent()
    {
        return $this->getConfiguredValue('develodesign_punchout/display/transfer_modal_content');
    }

    /**
     * Returns if the non-catalog products feature should be used
     */
    public function getConfigLoadNonCatalogue()
    {
        return $this->getConfiguredFlag('develodesign_punchout/product/load_non_catalogue');
    }
        
     /**
      * Returns the non-catalog Product Sku
      */
    public function getConfigNonCatalogueSKU()
    {
        return $this->getConfiguredValue('develodesign_punchout/product/non_catalogue_sku');
    }
        
     /**
      * Returns the non-catalog product option for the Sku field
      */
    public function getConfigSKUTitle()
    {
        return $this->getConfiguredValue('develodesign_punchout/product/customizable_option_sku');
    }
    
    /**
     * Returns the non-catalog product option for the Name field
     */
    public function getConfigNameTitle()
    {
        return $this->getConfiguredValue('develodesign_punchout/product/customizable_option_name');
    }
    
    /**
     * Returns the non-catalog product option for the Qty field
     */
    public function getConfigQtyTitle()
    {
        return $this->getConfiguredValue('develodesign_punchout/product/customizable_option_qty');
    }
    
    /**
     * Returns the store level config value
     */
    public function getConfiguredValue($config_path, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT):string
    {
        return $this->scopeConfig->getValue($config_path, $scope);
    }
    
    /**
     * Returns the store level flag value
     */
    public function getConfiguredFlag($config_path, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT): bool
    {
        return $this->scopeConfig->isSetFlag($config_path, $scope);
    }
}
