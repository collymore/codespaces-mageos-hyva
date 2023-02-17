<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Develodesign\Punchout\Model\Customer\Attribute\Source;

use Develodesign\Punchout\Model\Provider\PunchGroupProvider;

class PunchoutGroup extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var PunchGroupProvider
     */
    private $punchoutGroupProvider;

    public function __construct(PunchGroupProvider $punchoutGroupProvider)
    {
        $this->punchoutGroupProvider = $punchoutGroupProvider;
    }

    /**
     * getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        $punchoutGroupCollection = $this->punchoutGroupProvider->getPunchoutGroupCollection();
        if ($this->_options === null) {
            foreach ($punchoutGroupCollection as $parentGroup) {
                $this->_options[] = [
                    'label' => $parentGroup->getGroupName(),
                    'value' => htmlentities($parentGroup->getPunchoutgroupId())
                ];
            }
            
        }
        
        return $this->_options;
    }
}

