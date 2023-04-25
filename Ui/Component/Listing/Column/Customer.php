<?php

namespace Develodesign\Punchout\Ui\Component\Listing\Column;

use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Psr\Log\LoggerInterface;

class Customer extends Column
{
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var LoggerInterface|MockObject
     */
    private $logger;
    
    /**
     * Customer constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CustomerRepository $customerRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CustomerRepository $customerRepository,
        LoggerInterface $logger,
        array $components = [],
        array $data = []
    ) {
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
        
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
    
    /**
     * @param array $dataSource
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $customerId = $item['user_id'];
                try {
                    $customer = $this->customerRepository->getById($customerId);
                    if ($customer && $customer->getId()) {
                        $customerName = $customer->getFirstname();
                        $customerEmail = $customer->getLastname();
                    } else {
                        $customerName = $item['customer_name'];
                        $customerEmail = $item['customer_email'];
                    }
                    $item['user_id'] = "$customerName <{$customerEmail}>";
                } catch (NoSuchEntityException $e) {
                    $this->logger->warning("Non existant Customer attempted to be loaded ID : {$customerId}");
                }
            }
        }
        
        return $dataSource;
    }
}
