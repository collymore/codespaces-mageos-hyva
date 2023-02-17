<?php
namespace Develodesign\Punchout\Model\Provider;

use Develodesign\Punchout\Model\ResourceModel\PunchoutGroup\Collection;
use Develodesign\Punchout\Model\ResourceModel\PunchoutGroup\CollectionFactory as PunchoutGroupCollectionFactory;

class PunchGroupProvider
{
    /**
     * @var PunchoutGroupCollectionFactory
     */
    private $punchoutGroupCollectionFactory;

    public function __construct(PunchoutGroupCollectionFactory $collectionFactory)
    {
        $this->punchoutGroupCollectionFactory = $collectionFactory;
    }

    /**
     * @return Collection
     */
    public function getParentPunchoutGroupCollection(): Collection
    {
        return $this->punchoutGroupCollectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('is_parent', ['eq' => '1'])
            ->setOrder('group_name', 'ASC');
    }

    /**
     * @return Collection
     */
    public function getPunchoutGroupCollection(): Collection
    {
        return $this->punchoutGroupCollectionFactory->create()
            ->addFieldToSelect('*')
            ->setOrder('group_name', 'ASC');
    }
}
