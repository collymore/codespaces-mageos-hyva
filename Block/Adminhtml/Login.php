<?php

namespace Develodesign\Punchout\Block\Adminhtml;

use Develodesign\Punchout\Api\Data\PunchoutGroupInterface;
use Develodesign\Punchout\Model\ResourceModel\PunchoutGroup\CollectionFactory as PunchoutGroupCollectionFactory;
use Develodesign\Punchout\Api\PunchoutGroupRepositoryInterface;
use Develodesign\Punchout\Service\CustomerService;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Login
 * @package Develodesign\Punchout\Block
 */
class Login extends \Magento\Framework\View\Element\Template
{
    /**
     * @var PunchoutGroupCollectionFactory
     */
    protected $punchoutGroupCollection;

    protected $punchoutGroupRepository;
    
    /**
     * @var CustomerService
     */
    protected $customerService;

    /**
     * Index constructor.
     * @param Context $context
     * @param PunchoutGroupRepository $punchoutGroupRepository
     * @param PunchoutGroupCollectionFactory $CollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        PunchoutGroupRepositoryInterface $punchoutGroupRepository,
        PunchoutGroupCollectionFactory $CollectionFactory,
        CustomerService $customerService,
        array $data = []
    ) {
        $this->punchoutGroupRepository = $punchoutGroupRepository;
        $this->punchoutGroupCollection = $CollectionFactory;
        $this->customerService = $customerService;
        parent::__construct($context, $data);
    }

    /**
     * @return \Develodesign\Punchout\Model\ResourceModel\PunchoutGroup\Collection
     */
    public function getPunchoutGroups()
    {
        $collection = $this->punchoutGroupCollection->create();
        $collection->addFieldToFilter('status', ['eq' => '1'])
            ->setOrder('group_name', 'ASC');
    
        return $collection;
    }

    /**
     * @return string
     */
    public function getCXMLSetupUrl(): string
    {
        return  sprintf('%s%s', $this->getBaseUrl(), 'develo_punchout/index/cxmlsetup');
    }
    
    public function getOCISetupUrl(): string
    {
        return sprintf('%s%s', $this->getBaseUrl(), 'develo_punchout/index/ocisetup');
    }
    
    /**
     * @param $groupId
     *
     * @return PunchoutGroupInterface|false
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
     *
     * @return bool
     * @throws LocalizedException
     */
    public function hasPunchout($groupId): bool
    {
        $punchout = false;
        if ($this->getCxmlPunchout($groupId) || $this->getOciPunchout($groupId)) {
            $punchout = true;
        }
        return $punchout;
    }
    
    /**
     * @param $groupId
     *
     * @return array|false
     * @throws LocalizedException
     */
    public function getCxmlPunchout($groupId): array
    {
        if (!$group = $this->punchoutGroupRepository->get($groupId)) {
            return [];
        }
    
        if (!$group->getSharedSecret() &&
            (!$group->getDunsIdentity() || !$group->getAribaNetworkId())
        ) {
            return [];
        }
        $customer = $this->customerService->getCustomerByPunchoutGroupId($groupId);
        
        return [
            "shared_secret"    => $group->getSharedSecret(),
            "duns_identity"    => $group->getDunsIdentity(),
            "ariba_network_id" => $group->getAribaNetworkId(),
            'contactName'  => sprintf('%s %s', $customer->getFirstname(), $customer->getLastname()) ?? 'Test User',
            'contactEmail' => $customer->getEmail() ?? $group->getGroupEmail()
        ];
    }
    /**
     * @param $groupId
     * @return array|false
     */
    public function getOciPunchout($groupId): array
    {
        if (!$group = $this->getPunchoutGroup($groupId)) {
            return [];
        }
        if (!$group->getOciUsername() &&
            !$group->getOciPassword()
        ) {
            return [];
        }
        return [
            "oci_username" => $group->getOciUsername(),
            "oci_password" => $group->getOciPassword()
        ];
    }

    /**
     * @param $name
     * @return string
     */
    public function getShortName($name): string
    {
        if (strlen($name) > 24) {
            $name = substr($name, 0, 24) . ' ...';
        }
        return $name;
    }

    /**
     * Gets a random string for cxml value
     */
    public function getRandomValue($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
