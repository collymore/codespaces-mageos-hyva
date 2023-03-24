<?php

namespace Develodesign\Punchout\Block;

    use Magento\Checkout\Block\Onepage\Link;
    use Magento\Checkout\Helper\Data;
    use Magento\Checkout\Model\Session;
    use Magento\Framework\View\Element\Template\Context;

    class TransferCheckoutButton extends Link
    {
        public function __construct(
            Context $context,
            Session $checkoutSession,
            Data $checkoutHelper,
            array $data = []
        ) {
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
            switch ($this->_punchoutSessionCustomerManager->getPunchoutSessionDataType()) {
                case 'oci':
                    $this->getLayout()->createBlock('Develodesign\Punchout\Block\TransferCheckout\Oci')->setTemplate('Develodesign_Punchout::onepage/cxml.phtml')->toHtml();
                    break;
                case 'cxml':
                    //$this->setTemplate('Develodesign_Punchout::onepage/cxml.phtml');
                    $this->getLayout()->createBlock('Develodesign\Punchout\Block\TransferCheckout\Cxml')->setTemplate('Develodesign_Punchout::onepage/cxml.phtml')->toHtml();

                    break;

            }
            return parent::_prepareLayout();
        }
    }
