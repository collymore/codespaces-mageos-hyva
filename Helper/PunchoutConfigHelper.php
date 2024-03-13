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
     * Returns the default Cxml Xpath selector for the incoming Email value
     */
    public function getDefaultCxmlNodeXpathConfigEmail()
    {
        return $this->getConfiguredValue('develodesign_punchout/defaults/cxml_node_xpath_config_email');
    }

    /**
     * Returns if Punchout should Auto create customers
     */
    public function getConfigAutoCreate()
    {
        return $this->getConfiguredFlag('develodesign_punchout/customer/auto_create_user');
    }


    public function getDefaultCustomerAttributes()
    {
        return $this->getConfiguredValue('develodesign_punchout/customer/default_attributes');
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
     * Retrieve the checkout route that should be caught in the router
     */
    public function getRedirectCheckoutPath()
    {
        return $this->getConfiguredValue('develodesign_punchout/display/redirect_checkout_path');
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
     * Returns the config for if Invoice Qty should be validated
     */
    public function getValidateInvoiceQty(): bool
    {
        return $this->getConfiguredFlag('develodesign_punchout/invoice/validate_invoice_qty');
    }

     /**
     * Returns the config for if Invoice Qty should be validated
     */
    public function getAddReloadCustomerSectionScript(): bool
    {
        return $this->getConfiguredValue('develodesign_punchout/customer/reload_customer_section_onlogin');
    }

    /**
    * Returns array of allowed iframes
    */
    public function getAllowedIframes()
    {
        $values = $this->getConfiguredValue('develodesign_punchout/defaults/iframe_allowed');
        $values = str_replace(' ', "\n", $values);
        $values = explode("\n", $values);
        return array_map('trim', $values);
    }

    /**
     * Returns the store level config value
     */
    public function getConfiguredValue($config_path, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
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
