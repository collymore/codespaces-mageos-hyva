<?php

namespace Develodesign\Punchout\Service;

    use Develodesign\Punchout\Model\PunchoutGroup;
    use Magento\Customer\Model\AddressFactory;
    use Magento\Customer\Model\Customer;
    use Magento\Customer\Model\CustomerFactory;
    use Magento\Customer\Model\ResourceModel\Address as AddressResource;
    use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
    use Magento\Framework\DataObject;
    use Magento\Framework\Exception\AlreadyExistsException;
    use Magento\Framework\Exception\LocalizedException;
    use Magento\Framework\Exception\NoSuchEntityException;
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

        public function __construct(
            StoreManagerInterface $storeManager,
            CustomerFactory $customerFactory,
            CustomerResource $customerResource,
            AddressFactory $addressFactory,
            AddressResource $addressResource
        ) {
            $this->storeManager = $storeManager;
            $this->customerFactory = $customerFactory;
            $this->customerResource = $customerResource;
            $this->addressFactory = $addressFactory;
            $this->addressResource = $addressResource;
        }

        /**
         * @param $customerEmail
         *
         * @return Customer
         * @throws LocalizedException
         * @throws NoSuchEntityException
         */
        public function fetchCustomer($customerEmail): Customer
        {
            return $this->customerFactory->create()->setWebsiteId($this->storeManager->getStore()->getWebsiteId())->loadByEmail($customerEmail);
        }

        /**
         * @throws NoSuchEntityException
         * @throws AlreadyExistsException
         */
        public function createCustomer(DataObject $customerDTO): Customer
        {
            $customer = $this->customerFactory->create();
            $customer->isObjectNew(true);
            $customer->setStoreId($this->storeManager->getStore()->getId())
                ->setFirstname($customerDTO->getFirstName())
                ->setLastname($customerDTO->getLastName())
                ->setEmail($customerDTO->getEmail())
                ->setPassword($customerDTO->getPassword())
                ->setGroupId($customerDTO->getGroupId())
                ->setPunchoutGroup($customerDTO->getPunchoutGroupId())
                ->setWebsiteId($customerDTO->getWebsiteId())
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

        public function prepareCustomerData(PunchoutGroup $matchingPunchoutGroup, $email, $nameData): DataObject
        {
            return new DataObject(
                [
                    'website_id' => $this->storeManager->getWebsite()->getId(),
                    'first_name' => $nameData['first_name'],
                    'last_name' => $nameData['last_name'],
                    'email' => $email,
                    'password' => $this->getRandomPassword(),
                    'group_id' => $matchingPunchoutGroup->getMagentoCustomerGroup(),
                    'punchout_group_id' => $matchingPunchoutGroup->getPunchoutgroupId()
                ]
            );
        }

        private function getRandomPassword()
        {
            return uniqid('M181#Ha73y' . rand(), false);
        }
    }
