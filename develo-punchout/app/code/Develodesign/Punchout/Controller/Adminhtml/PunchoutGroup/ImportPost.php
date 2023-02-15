<?php

namespace Develodesign\Punchout\Controller\Adminhtml\PunchoutGroup;

use Develodesign\Punchout\Controller\Adminhtml\PunchoutGroup;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Develodesign\Punchout\Model\File\ImportFileHandler;

class ImportPost extends PunchoutGroup
{
    /**
     * @var ImportFileHandler
     */
    protected $importFileHandler;
    public function __construct(
        Context $context,
        Registry  $coreRegistry,
        ImportFileHandler $importFileHandler
    ) {
        $this->importFileHandler = $importFileHandler;
        parent::__construct($context, $coreRegistry);
    }
    public function execute()
    {
        $importedFile = $this->getRequest()->getFiles('import_punchout_group_file');
        if ($this->getRequest()->isPost() && isset($importedFile['tmp_name'])) {
            try {
                
                $this->importFileHandler->importFromCsvFile($importedFile);
                $this->messageManager->addSuccess(__('The punchout group file has been imported.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Invalid file upload attempt'));
            }
        } else {
            $this->messageManager->addError(__('Invalid file upload attempt'));
        }
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRedirectUrl());
        return $resultRedirect;
    }
}
