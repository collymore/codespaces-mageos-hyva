<?php

namespace Develodesign\Punchout\Block;

    use Develodesign\Punchout\Helper\PunchoutConfigHelper;
    use Develodesign\Punchout\Response\CxmlResponse;
    use Develodesign\Punchout\Service\CustomerService;
    use Develodesign\Punchout\Service\CxmlService;
    use Develodesign\Punchout\Service\PunchoutGroupService;
    use Develodesign\Punchout\Service\SessionService;
    use Magento\Checkout\Block\Onepage\Link;
    use Magento\Checkout\Helper\Data;
    use Magento\Checkout\Model\Cart;
    use Magento\Checkout\Model\Session;
    use Magento\Framework\View\Element\Template\Context;

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
    
        /**
         * @var CxmlResponse
         */
        protected $cxmlResponse;
        protected $cxmlService;
        protected $customerService;
    
        public function __construct(
            Context $context,
            Session $checkoutSession,
            Data $checkoutHelper,
            SessionService $sessionService,
            PunchoutConfigHelper $punchoutConfigHelper,
            Cart $cart,
            PunchoutGroupService $punchoutGroupService,
            CxmlResponse $cxmlResponse,
            CxmlService $cxmlService,
            CustomerService $customerService,
            array $data = []
            
        ) {
            $this->sessionService = $sessionService;
            $this->punchoutConfigHelper = $punchoutConfigHelper;
            $this->cart = $cart;
            $this->punchoutGroupService = $punchoutGroupService;
            $this->cxmlResponse = $cxmlResponse;
            $this->cxmlService = $cxmlService;
            $this->customerService = $customerService;
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
                    $this->setTemplate('Develodesign_Punchout::onepage/oci_link.phtml');
                    break;
                case 'cxml':
                    $this->setTemplate('Develodesign_Punchout::onepage/cxml.phtml');
                    break;

            }
            return parent::_prepareLayout();
        }
    
        public function generateCXMLCheckoutForm()
        {
            $cxmlSessionData  = $this->sessionService->getCXMLSessionData();
            $uom = $this->punchoutConfigHelper->getConfigUOM();
            $quote = $this->cart->getQuote();
            $customer = $this->sessionService->getCustomerSession();
            $punchoutGroupId = $this->customerService->getPunchoutGroupId($customer->getCustomerId());
            $punchoutGroup = $this->punchoutGroupService->loadPunchOutGroupById($punchoutGroupId);
            $punchoutOrder = [
                'grand_total'   => $quote->getGrandTotal(),
                'punchoutgroup_duns'   => $punchoutGroup->getDunsIdentity()
            ];
            $xml = $this->cxmlResponse->getPunchoutOrderMessage($cxmlSessionData, $punchoutOrder);
            $xml .= $this->cxmlService->getCXMLItems($quote->getAllItems(),$uom);
            $xml .= '</PunchOutOrderMessage>
                            </Message>
                        </cXML>';
    
            return sprintf("<form id=\"punchout_cxml_form\"  action=\"%s\" method=\"post\" enctype=\"application/x-www-form-urlencoded\" >
                        <input name=\"cXML-urlencoded\" id=\"urlencoded_bottom\" type=\"hidden\" value= '%s'>
                    </form>", $cxmlSessionData['return_url'], $xml);
            
        }
    
        public function generateCXMLSubmitButton()
        {
            $form = '#punchout_cxml_form';
            $modal = '#punchout-modal';
            $label = 'Transfer Basket Items With Punchout';
        
            $html = '<button class=" button btn-proceed-checkout btn-checkout" id="punchout-button-submit" type="button">
                                <span>
                                    <span>' . $label . '</span>
                                </span>
                    </button>
                    <script>
                    require([\'jquery\', \'jquery/ui\'], function($){
                      $( "#punchout-button-submit" ).click(function() {
                            $("' . $modal . '").show();
                            $("' . $form . '").submit();
                        });
                    });
                    </script>';
            return $html;
        }
    }
