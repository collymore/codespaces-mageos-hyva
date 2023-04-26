<?php
    
    namespace Develodesign\Punchout\Service;
    
    use Develodesign\Punchout\Helper\PunchoutConfigHelper;
    use Magento\Catalog\Model\Product;
    use Magento\Catalog\Model\ProductRepository;
    use Magento\Framework\DataObject;
    use Magento\Framework\DB\TransactionFactory;
    use Magento\Framework\Exception\CouldNotSaveException;
    use Magento\Framework\Exception\LocalizedException;
    use Magento\Framework\Exception\NoSuchEntityException;
    use Magento\Quote\Api\CartManagementInterface;
    use Magento\Quote\Api\CartRepositoryInterface;
    use Magento\Quote\Api\Data\CartInterface;
    use Magento\Quote\Model\Quote\Item;
    use Magento\Quote\Model\Quote\ItemFactory;
    use Magento\Sales\Api\Data\OrderInterface;
    use Magento\Sales\Api\OrderRepositoryInterface;
    use Magento\Sales\Model\Order\Invoice as OrderInvoice;
    use Magento\Sales\Model\Service\InvoiceServiceFactory;
    use Magento\Store\Api\Data\StoreInterface;
    use Magento\Payment\Api\PaymentMethodListInterface;
    use Magento\Payment\Model\Method\InstanceFactory;

class CreateOrderService
{
    /**
     * @var CartManagementInterface
     */
    protected $quoteManager;
    
    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;
    
    /**
     * @var ProductRepository
     */
    protected $productRepository;
    
    /**
     * @var ItemFactory
     */
    protected $quoteItemFactory;
    
    /**
     * @var PunchoutConfigHelper
     */
    protected $configHelper;
    
    /**
     * @var PaymentMethodListInterface
     */
    protected $paymentMethodList;
    
    /**
     * @var InstanceFactory
     */
    protected $paymentMethodInstanceFactory;
    
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;
    
    /**
     * @var InvoiceServiceFactory
     */
    protected $invoiceServiceFactory;
    
    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;
    
    public function __construct(
        CartManagementInterface $quoteManager,
        CartRepositoryInterface $quoteRepository,
        ProductRepository $productRepository,
        ItemFactory $quoteItemFactory,
        PunchoutConfigHelper $configHelper,
        PaymentMethodListInterface $paymentMethodList,
        InstanceFactory $paymentMethodInstanceFactory,
        OrderRepositoryInterface $orderRepository,
        InvoiceServiceFactory $invoiceServiceFactory,
        TransactionFactory $transactionFactory
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->quoteManager = $quoteManager;
        $this->productRepository = $productRepository;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->configHelper = $configHelper;
        $this->paymentMethodList = $paymentMethodList;
        $this->paymentMethodInstanceFactory = $paymentMethodInstanceFactory;
        $this->orderRepository = $orderRepository;
        $this->invoiceServiceFactory = $invoiceServiceFactory;
        $this->transactionFactory = $transactionFactory;
    }
    
    /**
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function getCart(): CartInterface
    {
        $quoteId = $this->quoteManager->createEmptyCart();
        return $this->quoteRepository->get($quoteId);
    }
    
    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getQuoteItem($outItems, CartInterface $quote, StoreInterface $store, $loadNonCatalog = false): array
    {
        $result = [];
        /** @var DataObject $item */
        foreach ($outItems as $item) {
            $sku = $item->getProductSku();
            $productId = $item->getInternalReferenceId();
            $qty = (int)filter_var($item->getQuantity(), FILTER_SANITIZE_NUMBER_INT);
            $product = $this->getProductByIdOrSku($productId, $sku, $store->getId());
            $price = $item->getUnitPrice() * $qty;
            
            if ($product && $product->getId()) {
                $quoteItem = $this->setUpQuoteItem($product, $qty, $item);
                $result[$sku] = $quoteItem;
            }
            if ($loadNonCatalog === true) {
                $this->productRepository->get('saved-quote-product', false, $store->getId(), true);
            }
            if ($this->configHelper->getConfigLoadNonCatalogue() === true) {
                $nonCatalogSKU = $this->configHelper->getConfigNonCatalogueSKU();
                if (isset($nonCatalogSKU) && trim($nonCatalogSKU) !== '') {
                    $product = $this->productRepository->get($nonCatalogSKU, false, $store->getId(), true);
                    $productOptions = $this->getCustomisableProductOptions($product, $item);
                    $params = [
                        'product' => $product->getId(),
                        'qty' => 1,
                        'options' => $productOptions,
                    ];
                    $product->setIsSuperMode(true);
                    $product->setPrice($price);
                    $product->setCustomPrice($price);
                    $product->setOriginalCustomPrice($price);
                    $requestParams = new DataObject($params);
                    $quote->addProduct($product, $requestParams);
                }
            }
                
        }
        return $result;
    }
    
    /**
     * @throws NoSuchEntityException
     */
    private function getProductByIdOrSku($productId, $sku, $storeId): Product
    {
        $product = null;
        if ((int)$productId !== 0) {
            $matchingProduct = $this->productRepository->getById($productId, false, $storeId, true);
            if ($matchingProduct && (int)$matchingProduct->getId() === (int)$productId) {
                $product = $matchingProduct;
            }
        }
        
        if ($product === null) {
            $matchingSKUProduct = $this->productRepository->get($sku, false, $storeId, true);
            if ($matchingSKUProduct) {
                $product = $matchingSKUProduct;
            }
                
        }
        return $product;
    }
    
    /**
     * @param $product
     * @param int $qty
     * @param $item
     *
     * @return Item
     */
    private function setUpQuoteItem(Product $product, int $qty, $item): Item
    {
        $quoteItem = $this->quoteItemFactory->create();
        $quoteItem->setProduct($product);
        $quoteItem->setCustomPrice($item->getUnitPrice());
        $quoteItem->setOriginalCustomPrice($item->getUnitPrice());
        $quoteItem->setQty($qty);
        $quoteItem->setSku($product->getSku());
        return $quoteItem;
    }
        
    private function getCustomisableProductOptions(Product $product, $item): array
    {
        $optionValues = [];
        $skuTitle = trim($this->configHelper->getConfigSKUTitle());
        $nameTitle = trim($this->configHelper->getConfigNameTitle());
        $qtyTitle = trim($this->configHelper->getConfigQtyTitle());
        if (isset($skuTitle, $nameTitle, $qtyTitle) && ($skuTitle && $nameTitle && $qtyTitle)) {
            foreach ($product->getOptions() as $o) {
                if ($o->getTitle() === $skuTitle) {
                    $optionValues[$o->getId()] = $item->getProductSku() ?: substr($item->getDescription(), 0, 60);
                }
                if ($o->getTitle() === $nameTitle) {
                    $optionValues[$o->getId()] = $item->getDescription();
                }
                if ($o->getTitle() === $qtyTitle) {
                    $optionValues[$o->getId()] = $item->getQuantity();
                }
            }
        }
            
        return $optionValues;
    }
    
    /**
     * @throws LocalizedException
     */
    public function isPaymentAvailable(int $storeId, $quote): bool
    {
        $isAvailable = false;
        foreach ($this->paymentMethodList->getActiveList($storeId) as $method) {
            $methodInstance = $this->paymentMethodInstanceFactory->create($method);
            if (!$methodInstance->isAvailable($quote)) {
                $isAvailable = true;
            }
        }
        if (!$isAvailable) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('No payment is not available for the given quote.')
            );
        }
    
        return true;
    }
    
    /**
     * @throws CouldNotSaveException
     */
    public function makeOrderPlacement(int $quoteId): int
    {
        return $this->quoteManager->placeOrder($quoteId);
    }
    
    /**
     * @param int $orderId
     *
     * @return OrderInterface
     */
    public function getCreatedOrder(int $orderId): OrderInterface
    {
        $order = $this->orderRepository->get($orderId);
        $order->setEmailSent(0);
        return $order;
    }
    
    /**
     * @throws LocalizedException
     */
    public function invoiceOrder($order): void
    {
        $invoice = $this->invoiceServiceFactory->create()
            ->prepareInvoice($order);
        if (!$invoice) {
            throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t save the invoice right now.'));
        }
        if (!$invoice->getTotalQty()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('You can\'t create an invoice without products.')
            );
        }
        $invoice->setRequestedCaptureCase(OrderInvoice::CAPTURE_OFFLINE);
        $invoice->register();
        $invoice->getOrder()->setCustomerNoteNotify(false);
        $invoice->getOrder()->setIsInProcess(true);
        $transaction = $this->transactionFactory->create()->addObject($invoice)->addObject($invoice->getOrder());
        $transaction->save();
    }
}
