<?php

namespace Develodesign\Punchout\Block\Adminhtml;

use Develodesign\Punchout\Model\ResourceModel\PunchoutGroup\CollectionFactory as PunchoutGroupCollectionFactory;
use Develodesign\Punchout\Api\PunchoutGroupRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Login
 * @package Develodesign\Punchout\Block
 */
class Login extends \Magento\Framework\View\Element\Template
{
    /**
     * @var CollectionFactory
     */
    protected $punchoutGroupCollection;

    protected $punchoutGroupRepository;

    /**
     * Index constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param Url $customerUrl
     * @param PunchoutGroupRepository $punchoutGroupRepository
     * @param PunchoutGroupCollectionFactory $CollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        Url $customerUrl,
        PunchoutGroupRepositoryInterface $punchoutGroupRepository,
        PunchoutGroupCollectionFactory $CollectionFactory,
        array $data = array()
    )
    {
        $this->punchoutGroupRepository = $punchoutGroupRepository;
        $this->punchoutGroupCollection = $CollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return PunchoutGroupCollectionFactory
     */
    public function getPunchoutGroups()
    {
        $collection = $this->punchoutGroupCollection->create();
        $collection->setOrder('group_name', 'ASC');
        return $collection;
    }

    /**
     * @return string
     */
    public function getCustomPostActionUrl()
    {
        $url = $this->getUrl('develo_punchout/index/login');
        return $url;
    }

    /**
     * @param $groupId
     * @return false|\Magento\Company\Api\Data\CompanyInterface
     */
    public function getPunchoutGroup($groupId)
    {
        try {
            return $this->punchoutGroupRepository->get($groupId);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param $groupId
     * @return bool
     */
    public function hasPunchout($groupId)
    {
        $punchout = false;
        if ($this->hasCxmlPunchout($groupId) || $this->hasOciPunchout($groupId)) {
            $punchout = true;
        }
        return $punchout;
    }

    /**
     * @param $groupId
     * @return array|false
     */
    public function hasCxmlPunchout($groupId)
    {
        if (!$group = $this->punchoutGroupRepository->get($groupId))
            return false;

        if (
            !$group->getData("status") ||
            !$group->getData("shared_secret") ||
            (!$group->getData("duns_identity") || !$group->getData("ariba_network_id"))
        ) {
            return false;
        }
        return array(
            "shared_secret" => $group->getData("shared_secret"),
            "duns_identity" => $group->getData("duns_identity")?:$group->getData("duns_identity")
        );
    }

    /**
     * @param $groupId
     * @return array|false
     */
    public function hasOciPunchout($groupId)
    {
        if (!$group = $this->getPunchoutGroup($groupId))
            return false;
        if (
            !$group->getData("is_punchout") ||
            !$group->getData("oci_username") ||
            !$group->getData("oci_password")
        ) {
            return false;
        }
        return array(
            "oci_username" => $group->getData("oci_username"),
            "oci_password" => $group->getData("oci_password")
        );
    }

    /**
     * @param $name
     * @return mixed|string
     */
    public function getShortName($name)
    {
        if (strlen($name) > 24)
            $name = substr($name, 0, 24) . ' ...';
        return $name;
    }

}