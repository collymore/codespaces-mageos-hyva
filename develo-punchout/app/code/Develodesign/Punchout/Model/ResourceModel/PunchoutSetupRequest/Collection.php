<?php

namespace Develodesign\Punchout\Model\ResourceModel\PunchoutSetupRequest;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'setup_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Develodesign\Punchout\Model\PunchoutSetupRequest::class,
            \Develodesign\Punchout\Model\ResourceModel\PunchoutSetupRequest::class
        );
    }
}
