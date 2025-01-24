<?php
    
    namespace Develodesign\Punchout\Controller\PurchaseOrder;

    use Develodesign\Punchout\Event\EventServiceProvider;
    use Develodesign\Punchout\Exceptions\CxmlDocumentLoadingException;
    use Develodesign\Punchout\Model\Order\Request;
    use Develodesign\Punchout\Response\CxmlResponse;
    use Magento\Framework\App\Action\Action;
    use Magento\Framework\App\Action\Context;
    use Magento\Framework\App\CsrfAwareActionInterface;
    use Magento\Framework\App\Request\InvalidRequestException;
    use Magento\Framework\App\RequestInterface;
    use Magento\Framework\Filesystem\DriverInterface;

class Index extends Action implements CsrfAwareActionInterface
{
    /**
     * @var Request
     */
    protected $orderRequest;
    
    /**
     * @var CxmlResponse
     */
    protected $cxmlResponse;
    
    /**
     * @var EventServiceProvider
     */
    protected $eventServiceProvider;

    /**
     * @var DriverInterface
     */
    protected $driver;
    
    public function __construct(
        Context $context,
        Request $orderRequest,
        CxmlResponse $cxmlResponse,
        EventServiceProvider $eventServiceProvider,
        DriverInterface $driver
    ) {
        $this->cxmlResponse = $cxmlResponse;
        $this->orderRequest = $orderRequest;
        $this->eventServiceProvider = $eventServiceProvider;
        $this->driver = $driver;
        parent::__construct($context);
    }
    
    /**
     */
    public function execute()
    {
        try {
              $orderRequest = $this->orderRequest;
              $document = stream_get_contents($this->driver->fileOpen('php://input', 'r'));
              $orderRequest->setDocument($document);
              $orderRequest->isValid();
              $result = $orderRequest->getCreateOrder();
        } catch (\Exception|CxmlDocumentLoadingException $exception) {
            $this->eventServiceProvider->dispatchExceptionPunchoutRequestEvent(
                'CXML PunchOut Order Request',
                'CXML OrderRequest',
                sprintf(
                    'Message:%s File:%s',
                    $exception->getMessage(),
                    $exception->getFile()
                )
            );
             return $this->cxmlResponse->respondWithData(200, $exception->getMessage());
        }
            return $this->cxmlResponse->respondWithData(200, $result['message']);
    }
    
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }
    
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
