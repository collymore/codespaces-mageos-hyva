<?php

namespace Develodesign\Punchout\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class PunchoutSetupRequest extends AbstractDb
{

        /**
         * @inheritDoc
         */
    protected function _construct()
    {
        $this->_init('develodesign_punchout_punchoutsetuprequest', 'setup_id');
    }
}
