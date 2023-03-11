<?php

namespace Develodesign\Punchout\Service;

    use Develodesign\Punchout\Model\PunchoutGroup;
    use Magento\Customer\Api\CustomerRepositoryInterface;
    use Magento\Customer\Api\Data\CustomerInterface;
    use Magento\Customer\Model\AddressFactory;
    use Magento\Customer\Model\Customer;
    use Magento\Customer\Model\CustomerFactory;
    use Magento\Customer\Model\ResourceModel\Address as AddressResource;
    use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
    use Magento\Framework\Exception\AlreadyExistsException;
    use Magento\Framework\Exception\LocalizedException;
    use Magento\Framework\Exception\NoSuchEntityException;
    use Magento\Store\Model\StoreManagerInterface;

    class CustomerService
    {
        /**
         * @var CustomerRepositoryInterface
         */
        private $customerRepository;

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

        public function __construct(
            CustomerRepositoryInterface $customerRepository,
            StoreManagerInterface $storeManager,
            CustomerFactory $customerFactory,
            CustomerResource $customerResource,
            AddressFactory $addressFactory,
            AddressResource $addressResource
        ) {
            $this->customerRepository = $customerRepository;
            $this->storeManager = $storeManager;
            $this->customerFactory = $customerFactory;
            $this->customerResource = $customerResource;
            $this->addressFactory = $addressFactory;
            $this->addressResource = $addressResource;
        }

        /**
         * @throws NoSuchEntityException
         * @throws LocalizedException
         */
        public function fetchCustomer($customerEmail): CustomerInterface
        {
            return $this->customerRepository->get($customerEmail, $this->storeManager->getStore()->getWebsiteId());
        }

        /**
         * @throws NoSuchEntityException
         * @throws AlreadyExistsException
         */
        public function createCustomer(\Magento\Framework\DataObject $customerDTO): Customer
        {
            $customer = $this->customerFactory->create();
            $customer->isObjectNew(true);
            $customer->setWebsiteId($customerDTO->getWebsiteId())
                ->setStoreId($this->storeManager->getStore()->getId())
                ->setFirstname($customerDTO->getFirstName())
                ->setLastname($customerDTO->getLastName())
                ->setEmail($customerDTO->getEmail())
                ->setPassword($customerDTO->getPassword())
                ->setGroupId($customerDTO->getGroupId())
                ->setPunchoutGroupId($customerDTO->getPunchoutGroupId())
                ->setIsActive(1)
                ->setForceConfirmed(true);
            $this->customerResource->save($customer);
            return $customer;
        }

        /**
         * @throws AlreadyExistsException
         */
        public function createCustomerAddress(Customer $customer, PunchoutGroup $punchoutGroup): void
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

        public function prepareCustomerData(PunchoutGroup $matchingPunchoutGroup, $email, $nameData)
        {
            return new \Magento\Framework\DataObject(
                [
                    'websiteId' => $this->storeManager->getWebsite()->getId(),
                    'firstName' => $nameData['first_name'],
                    'lastName' => $nameData['last_name'],
                    'email' => $email,
                    'password' => $this->getRandomPassword(),
                    'groupId' => $matchingPunchoutGroup->getMagentoCustomerGroup(),
                    'punchoutGroupId' => $matchingPunchoutGroup->getPunchoutgroupId()
                ]
            );
        }

        private function getRandomPassword()
        {
            return uniqid('M181#Ha73y' . rand(), false);
        }
    }
