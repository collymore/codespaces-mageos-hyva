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
