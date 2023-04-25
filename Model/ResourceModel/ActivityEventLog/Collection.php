<?php

namespace Develodesign\Punchout\Model\ResourceModel\ActivityEventLog;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'log_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Develodesign\Punchout\Model\ActivityEventLog::class,
            \Develodesign\Punchout\Model\ResourceModel\ActivityEventLog::class
        );
    }
}
