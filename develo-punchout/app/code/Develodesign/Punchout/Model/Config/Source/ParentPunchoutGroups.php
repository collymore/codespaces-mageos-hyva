<?php

namespace Develodesign\Punchout\Model\Config\Source;

use Develodesign\Punchout\Model\ResourceModel\PunchoutGroup\Collection;
use Develodesign\Punchout\Model\ResourceModel\PunchoutGroup\CollectionFactory as PunchoutGroupCollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class ParentPunchoutGroups implements OptionSourceInterface
{
    /**
     * @var PunchoutGroupCollectionFactory
     */
    private $punchoutGroupCollectionFactory;

    /**
     * @var array
     */
    private $options;
    public function __construct(PunchoutGroupCollectionFactory $collectionFactory)
    {
        $this->punchoutGroupCollectionFactory = $collectionFactory;
        $this->options = [];
    }

    /**
     * @return Collection
     */
    private function getParentPunchoutGroupCollection(): Collection
    {
        return $this->punchoutGroupCollectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('is_parent', ['eq' => '1'])
            ->setOrder('group_name', 'ASC');
    }
    public function toOptionArray(): array
    {
        $parentGroupCollection  = $this->getParentPunchoutGroupCollection();
        $this->options[] = ['label' => 'Select Parent Group', 'value' => 0];
        foreach ($parentGroupCollection as $parentGroup) {
            $this->options[] = ['label' => $parentGroup->getGroupName(), 'value' => htmlentities($parentGroup->getPunchoutgroupId())];
        }
        return $this->options;
    }
}
