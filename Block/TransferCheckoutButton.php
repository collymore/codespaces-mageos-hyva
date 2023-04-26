<?php

namespace Develodesign\Punchout\Block;

    use Develodesign\Punchout\Block\TransferCheckout\Cxml as CxmlBlock;
    use Develodesign\Punchout\Helper\PunchoutConfigHelper;
    use Develodesign\Punchout\Service\CustomerService;
    use Develodesign\Punchout\Service\PunchoutGroupService;
    use Develodesign\Punchout\Service\SessionService;
    use Magento\Checkout\Block\Onepage\Link;
    use Magento\Checkout\Helper\Data;
    use Magento\Checkout\Model\Cart;
    use Magento\Checkout\Model\Session;
    use Magento\Framework\Exception\NoSuchEntityException;
    use Magento\Framework\View\Element\Template\Context;
    use Develodesign\Punchout\Block\TransferCheckout\Oci as OciBlock;

class TransferCheckoutButton extends Link
{
    /**
     * @var SessionService
     */
    protected $sessionService;
    
    /**
     * @var PunchoutConfigHelper
     */
    protected $punchoutConfigHelper;
    
    /**
     * @var Cart
     */
    protected $cart;
    
    /**
     * @var PunchoutGroupService
     */
    protected $punchoutGroupService;
        
    protected $customerService;
        
    protected $cxmlBlock;
        
    protected $ociBlock;
    
    public function __construct(
        Context $context,
        Session $checkoutSession,
        Data $checkoutHelper,
        SessionService $sessionService,
        PunchoutConfigHelper $punchoutConfigHelper,
        Cart $cart,
        PunchoutGroupService $punchoutGroupService,
        CustomerService $customerService,
        CxmlBlock $cxmlBlock,
        OciBlock $ociBlock,
        array $data = []
    ) {
        $this->sessionService = $sessionService;
        $this->punchoutConfigHelper = $punchoutConfigHelper;
        $this->cart = $cart;
        $this->punchoutGroupService = $punchoutGroupService;
        $this->customerService = $customerService;
        $this->cxmlBlock = $cxmlBlock;
        $this->ociBlock = $ociBlock;
        parent::__construct($context, $checkoutSession, $checkoutHelper, $data);
    }

    /**
     * Sets checkout layout and template
     * @return Link
     */
    protected function _prepareLayout()
    {
           
        /**
         *  Render template based on punchout session type i.e oci, cxml || default
         */
        switch ($this->sessionService->getPunchoutType()) {
            case 'oci':
                $this->setTemplate('Develodesign_Punchout::onepage/oci.phtml');
                break;
            case 'cxml':
                $this->setTemplate('Develodesign_Punchout::onepage/cxml.phtml');
                break;

        }
        return parent::_prepareLayout();
    }
    
    /**
     * @throws NoSuchEntityException
     */
    public function generateCXMLCheckoutForm()
    {
        try {
            $cxmlSessionData  = $this->sessionService->getPunchoutSessionData('cxml');
            $uom = $this->punchoutConfigHelper->getConfigUOM();
            $quote = $this->cart->getQuote();
            $customer = $this->sessionService->getCustomerSession();
            $punchoutGroupId = $this->customerService->getPunchoutGroupId($customer->getCustomerId());
            $punchoutGroup = $this->punchoutGroupService->loadPunchOutGroupById($punchoutGroupId);
            $punchoutOrder = [
                'grand_total'   => $quote->getGrandTotal(),
                'punchoutgroup_duns'   => $punchoutGroup->getDunsIdentity()
            ];
            return $this->cxmlBlock->getCxmlForm($cxmlSessionData, $punchoutOrder, $quote, $uom);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    
    /**
     *
     */
    public function generateCXMLSubmitButton(): string
    {
        return $this->cxmlBlock->getSubmitButton($this->punchoutConfigHelper->getConfigTransferButtonLabel());
    }
        
    public function generateOciCheckoutForm()
    {
        $ociSessionData  = $this->sessionService->getPunchoutSessionData('oci');
        $customer = $this->sessionService->getCustomerSession();
        $punchoutGroupId = $this->customerService->getPunchoutGroupId($customer->getCustomerId());
        return $this->ociBlock->getOCIForm($ociSessionData, $this->cart->getItems(), $punchoutGroupId);
    }
        
    public function generateOCISubmitButton(): string
    {
        return $this->ociBlock->getOciButton($this->punchoutConfigHelper->getConfigTransferButtonLabel());
    }
}
