<?php

namespace Develodesign\Punchout\Model\ResourceModel;

    use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
    use Magento\Framework\Model\ResourceModel\Db\Context;

class ActivityEventLog extends AbstractDb
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('develodesign_logactivity', 'log_id');
    }
}
