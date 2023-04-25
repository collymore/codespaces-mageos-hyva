<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Develodesign\Punchout\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class PunchoutGroup extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('develodesign_punchout_punchoutgroup', 'punchoutgroup_id');
    }
}
