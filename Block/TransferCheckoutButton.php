<?php

namespace Develodesign\Punchout\Block;

    use Develodesign\Punchout\Service\SessionService;
    use Magento\Checkout\Block\Onepage\Link;
    use Magento\Checkout\Helper\Data;
    use Magento\Checkout\Model\Session;
    use Magento\Framework\View\Element\Template\Context;

    class TransferCheckoutButton extends Link
    {
        protected $sessionService;
        public function __construct(
            Context $context,
            Session $checkoutSession,
            Data $checkoutHelper,
            SessionService $sessionService,
            array $data = []
            
        ) {
            $this->sessionService = $sessionService;
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
            
            $defaultConfigGroupArry = $this->_helperData->getDefaultGroupValues();
            $customerConfigGroupArry = $this->_helperData->getCustomerGroupValues();
            $quote = $this->_cart->getQuote();
            $company = $this->_companyHelperData->getCompanyByCustomerId((int)$this->_punchoutSessionCustomerManager->getCustomerId());
            $this->company = $this->_companyHelperData->getCompanyByCustomerId((int)$this->_punchoutSessionCustomerManager->getCustomerId());
        
            $punchoutOrderArry = [
                'grand_total'   => $quote->getGrandTotal(),
                'company_duns'   => $company->getDunsIdentity()
            ];
        
            if($this->company->getId() == '137' || $this->company->getId() == '288'|| $this->company->getId() == '543'){
                $punchoutOrderArry['tfl_version'] = '1.2.055';
            }
            $xml = $this->_setUpRequestHelper->getCXMLPunchOutOrderMessage($cxmlSessionDataArry, $defaultConfigGroupArry, $customerConfigGroupArry, $punchoutOrderArry);
            $xml .= $this->getCXMLItems($quote->getAllItems());
            $xml .= '</PunchOutOrderMessage>
                            </Message>
                        </cXML>';
            $form = sprintf("<form id=\"punchout_cxml_form\"  action=\"%s\" method=\"post\" enctype=\"application/x-www-form-urlencoded\" >
                        <input name=\"cXML-urlencoded\" id=\"urlencoded_bottom\" type=\"hidden\" value= '%s'>
                    </form>", $cxmlSessionDataArry['return_url'], $xml);
        
            return $form;
        }
    }
