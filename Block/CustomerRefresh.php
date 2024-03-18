<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Develodesign\Punchout\Block;
use Develodesign\Punchout\Helper\PunchoutConfigHelper;

class CustomerRefresh extends \Magento\Framework\View\Element\Template
{
    /**
     * @var PunchoutConfigHelper
     */
    protected $punchoutConfigHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        PunchoutConfigHelper $punchoutConfigHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->punchoutConfigHelper = $punchoutConfigHelper;
        parent::__construct($context, $data);
    }

    /**
     * Checks the config to see if reload script should be added. 
     * 
     * @return Bool
     */
    public function getAddReloadCustomerSectionScript()
    {
        return $this->punchoutConfigHelper->getAddReloadCustomerSectionScript();
    }
}

