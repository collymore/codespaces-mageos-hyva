<?php

namespace Develodesign\Punchout\Service;

    use Develodesign\Punchout\Model\PunchoutGroup;
    use Magento\Customer\Api\Data\AddressInterface;
    use Magento\Customer\Api\Data\CustomerInterface;
    use Magento\Customer\Api\CustomerRepositoryInterface;
    use Magento\Customer\Model\AddressFactory;
    use Magento\Customer\Model\Customer;
    use Magento\Customer\Model\CustomerFactory;
    use Magento\Customer\Model\ResourceModel\Address as AddressResource;
    use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
    use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
    use Magento\Framework\DataObject;
    use Magento\Framework\Exception\AlreadyExistsException;
    use Magento\Framework\Exception\LocalizedException;
    use Magento\Framework\Exception\NoSuchEntityException;
    use Magento\Store\Api\Data\StoreInterface;
    use Magento\Store\Model\StoreManagerInterface;

class CustomerService
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var CustomerResource
     */
    private $customerResource;

    /**
     * @var AddressFactory
     */
    private $addressFactory;
    /**
     * @var AddressResource
     */
    private $addressResource;

    /**
     * @var CollectionFactory
     */
    private $customerCollection;
    
    private $customerRepository;

    public function __construct(
        StoreManagerInterface $storeManager,
        CustomerFactory $customerFactory,
        CustomerResource $customerResource,
        AddressFactory $addressFactory,
        AddressResource $addressResource,
        CollectionFactory $collectionFactory,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->customerResource = $customerResource;
        $this->addressFactory = $addressFactory;
        $this->addressResource = $addressResource;
        $this->customerCollection = $collectionFactory;
        $this->customerRepository = $customerRepository;
    }
    
    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getCustomerByEmail($customerEmail): CustomerInterface
    {
        return $this->customerRepository->get($customerEmail);
    }

    /**
     * @throws NoSuchEntityException
     * @throws AlreadyExistsException
     */
    public function createCustomer(DataObject $customerDTO): Customer
    {
        $customer = $this->customerFactory->create();
        $customer->isObjectNew(true);
        $customer->setData($customerDTO->toArray());
        $customer->setStoreId($this->storeManager->getStore()->getId())
            ->setIsActive(1)
            ->setForceConfirmed(true);
        $this->customerResource->save($customer);
        return $customer;
    }

    /**
     * @throws AlreadyExistsException
     */
    public function createCustomerAddress(Customer $customer, DataObject $punchoutGroup): void
    {
        $customerAddress = $this->addressFactory->create();
        $customerAddress->setCustomerId($customer->getId())
                ->setFirstname($customer->getFirstname())
                ->setLastname($customer->getLastname())
                ->setCountryId($punchoutGroup->getCountryId())
                ->setPostcode($punchoutGroup->getPostcode())
                ->setCity($punchoutGroup->getCity())
                ->setTelephone($punchoutGroup->getTelephone())
                ->setStreet($punchoutGroup->getStreet())
                ->setCompany($punchoutGroup->getGroupName())
                ->setIsDefaultBilling('1')
                ->setIsDefaultShipping('1');

        $this->addressResource->save($customerAddress);
    }

    public function prepareCustomerData(DataObject $matchingPunchoutGroup, $email, $nameData, array $attributes = []): DataObject
    {
        $customerProps = [
            'website_id' => $this->storeManager->getWebsite()->getId(),
            'firstname' => $nameData['first_name'],
            'lastname' => $nameData['last_name'],
            'email' => $email,
            'password' => $this->getRandomPassword(),
            'group_id' => $matchingPunchoutGroup->getMagentoCustomerGroup(),
            'punchout_group_id' => $matchingPunchoutGroup->getPunchoutgroupId()
            
        ] + $attributes;
        return new DataObject(
            $customerProps
        );
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function isActiveProxyUser(int $userId): bool
    {
        $proxyUser = $this->customerCollection->create()
            ->addFieldToFilter('entity_id', $userId)
            ->getFirstItem();

        return (int)$proxyUser->getIsActive() === 1;
    }

    /**
     * Generates a random password string
     */
    private function getRandomPassword() : string
    {
        return uniqid('M181#Ha73y' . rand(), false);
    }
    
    /**
     * Returns the customers groupID
     */
    public function getPunchoutGroupId(int $customerId) 
    {
        $customer = $this->customerCollection->create()
            ->addFieldToFilter('entity_id', $customerId)
            ->getFirstItem();
        return $customer->getPunchoutGroup();
    }
    
    /**
     * Returns the first customer available for that punchoutGroupID
     */
    public function getCustomerByPunchoutGroupId(int $punchoutGroupId)
    {
        $customer = $this->customerCollection->create()
            ->addFieldToFilter('punchout_group', $punchoutGroupId);

        if ($customer->count() > 0) {
            return $customer->getFirstItem();
        }
        
        return null;
    }
    
    /**
     * @throws NoSuchEntityException
     */
    public function getMainStore(): StoreInterface
    {
        return $this->storeManager->getStore();
    }
    
    /**
     * Gets the customers Address formatted for Cxml
     * @return array
     */
    public function getExistingCustomerAddress($data, CustomerInterface $customer) : array
    {
        /** @var AddressInterface $address */
        foreach ($customer->getAddresses() as $address) {
            $postCode = strtolower(trim($address->getPostcode()));
            $queryPostCode = strtolower(trim($data['postcode']));
            $street = strtolower(trim($address->getStreet()[0]));
            $queryStreet = strtolower(trim($data['street'][0]));
            $queryName  = strtolower(trim($data['company']));
            $addressName =  strtolower(trim($address->getFirstname()));
            $matches = false;
            
            if ($postCode === $queryPostCode) {
                $matches = true;
            }
            if ($postCode === $queryPostCode && $queryName === $addressName) {
                $matches = true;
            }
            if ($postCode === $queryPostCode && $queryStreet === $addressName) {
                $matches = true;
            }
            if ($postCode === $queryPostCode && $queryStreet === $street) {
                $matches = true;
            }
            
            if ($matches) {
                $firstName = $customer->getFirstname();
                $lastName = $customer->getLastname();
                $email = $customer->getEmail();
    
                return [
                    'ext_address_id' => $address->getId(),
                    'firstname' => $firstName,
                    'lastname' => $lastName,
                    'company' => $address->getCompany(),
                    'street' => $address->getStreet(),
                    'city' => $address->getCity(),
                    'postcode' => $address->getPostcode(),
                    'region' => ($address->getRegion()) ? $address->getRegion()->getRegion() : '',
                    'region_id' => $address->getRegionId(),
                    'country_id' => $address->getCountryId(),
                    'email' => $email,
                    'telephone' => $address->getTelephone()
                ];
            }
        }
        return [];
    }
    
    
    public function getDefaultCustomerAttributes(string $customerAttributes): array
    {
        $attributes = explode(',', str_replace('"', '', $customerAttributes));
        $customerAttributeArray = [];
        
        foreach ($attributes as $attribute) {
            // Split each attribute by colon
            $pair = explode(':', $attribute, 2);
            
            // Trim any extra whitespace from keys and values
            $key = trim($pair[0]);
            $value = trim($pair[1]);
            
            // Add the key-value pair to the array
            $customerAttributeArray[$key] = $value;
        }

        return $customerAttributeArray;
    }
}
