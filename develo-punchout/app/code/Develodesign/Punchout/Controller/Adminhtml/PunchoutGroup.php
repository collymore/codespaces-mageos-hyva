<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Develodesign\Punchout\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Registry;

abstract class PunchoutGroup extends \Magento\Backend\App\Action
{
    
    /**
     * @var Registry
     */
    protected $_coreRegistry;
    const ADMIN_RESOURCE = 'Develodesign_Punchout::top_level';

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param Page $resultPage
     * @return Page
     */
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('Develodesign'), __('Develodesign'))
            ->addBreadcrumb(__('Punchoutgroup'), __('Punchoutgroup'));
        return $resultPage;
    }
}

