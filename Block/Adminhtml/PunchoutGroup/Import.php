<?php

namespace Develodesign\Punchout\Block\Adminhtml\PunchoutGroup;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget;

class Import extends Widget
{
    /**
     * @var string
     */
    protected $_template = 'Develodesign_Punchout::importPunchoutGroup.phtml';

    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(Context $context, array $data = [])
    {
        parent::__construct($context, $data);
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }

    public function getFormAction()
    {
        return $this->getUrl('develodesign_punchout/punchoutgroup/importPost');
    }
    
    public function getDownloadSampleUrl()
    {
        return $this->getUrl('develodesign_punchout/punchoutgroup/downloadFile');
    }
}
