<?php

namespace Develodesign\Punchout\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Develodesign\Punchout\Model\Provider\PunchGroupProvider;

class ParentPunchoutGroups implements OptionSourceInterface
{
    /**
     * @var PunchGroupProvider
     */
    private $punchoutGroupProvider;

    /**
     * @var array
     */
    private $options;
    public function __construct(PunchGroupProvider $punchoutGroupProvider)
    {
        $this->punchoutGroupProvider = $punchoutGroupProvider;
        $this->options = [];
    }

    
    public function toOptionArray(): array
    {
        $parentGroupCollection  = $this->punchoutGroupProvider->getParentPunchoutGroupCollection();
        $this->options[] = ['label' => 'Select Parent Group', 'value' => null];
        foreach ($parentGroupCollection as $parentGroup) {
            $this->options[] = ['label' => $parentGroup->getGroupName(), 'value' => htmlentities($parentGroup->getPunchoutgroupId())];
        }
        return $this->options;
    }
}
