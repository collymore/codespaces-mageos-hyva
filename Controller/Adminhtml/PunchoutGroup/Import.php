<?php

namespace Develodesign\Punchout\Controller\Adminhtml\PunchoutGroup;

use Develodesign\Punchout\Controller\Adminhtml\PunchoutGroup;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;

class Import extends PunchoutGroup implements HttpGetActionInterface
{
    
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Import Punchout Groups'));
        return $resultPage;
    }
}
