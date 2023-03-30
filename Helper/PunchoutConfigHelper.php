<?php

namespace Develodesign\Punchout\Helper;

    use Magento\Framework\App\Config\ScopeConfigInterface;
    use Magento\Framework\App\Helper\AbstractHelper;

    class PunchoutConfigHelper extends AbstractHelper
    {
        public function __construct(
            \Magento\Framework\App\Helper\Context $context
        ) {
            parent::__construct($context);
        }
    
        public function getConfigUOM()
        {
          return $this->getConfiguredValue('develodesign_punchout/defaults/uom');
        }
        
        public function getConfigAutoCreate()
        {
          return $this->getConfiguredFlag('develodesign_punchout/customer/auto_create_user');
        }
    
        public function getConfigTransferButtonLabel()
        {
            return $this->getConfiguredValue('develodesign_punchout/display/transfer_button_label');
        }
        
        public function getConfigLoadNonCatalogue()
        {
            return $this->getConfiguredFlag('develodesign_punchout/product/load_non_catalogue');
        }
        
        public function getConfigNonCatalogueSKU()
        {
            return $this->getConfiguredValue('develodesign_punchout/product/non_catalogue_sku');
        }
        
        public function getConfigSKUTitle()
        {
            return $this->getConfiguredValue('develodesign_punchout/product/customizable_option_sku');
        }
    
        public function getConfigNameTitle()
        {
            return $this->getConfiguredValue('develodesign_punchout/product/customizable_option_name');
        }
    
        public function getConfigQtyTitle()
        {
            return $this->getConfiguredValue('develodesign_punchout/product/customizable_option_qty');
        }
    
    
        public function getConfiguredValue($config_path, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
        {
            return $this->scopeConfig->getValue($config_path, $scope);
        }
    
        public function getConfiguredFlag($config_path, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT): bool
        {
            return $this->scopeConfig->isSetFlag($config_path, $scope);
        }
    }
