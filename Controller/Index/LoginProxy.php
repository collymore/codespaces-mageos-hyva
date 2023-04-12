<?php
namespace Develodesign\Punchout\Controller\Index;

    use Develodesign\Punchout\Event\EventServiceProvider;
    use Develodesign\Punchout\Service\CustomerService;
    use Develodesign\Punchout\Service\ProxyRequestService;
    use Develodesign\Punchout\Service\SessionService;
    use Develodesign\Punchout\Service\SetupRequestService;
    use Magento\Framework\App\Action\Action;
    use Magento\Framework\App\Action\Context as ActionContext;
    use Magento\Framework\App\Action\HttpGetActionInterface;
    use Magento\Framework\Controller\Result\JsonFactory;
    use Magento\Framework\Exception\LocalizedException;
    use Magento\Framework\Exception\NoSuchEntityException;
    use Magento\Framework\Webapi\Exception;
    use Develodesign\Punchout\Response\JsonResponse;

    class LoginProxy extends Action implements HttpGetActionInterface
    {
        /**
         * @var JsonResponse
         */
        protected $jsonResponse;
        
        /**
         * @var ProxyRequestService
         */
        protected $proxyRequestService;
        
        /**
         * @var SetupRequestService
         */
        protected $setupRequestService;
    
        /**
         * @var CustomerService
         */
        protected $customerService;
    
        /**
         * @var SessionService
         */
        protected $customerSessionService;
    
        /**
         * @var EventServiceProvider
         */
        protected $eventServiceProvider;
    
        public function __construct(
            ActionContext $context,
            JsonResponse $jsonResponse,
            ProxyRequestService $proxyRequestService,
            SetupRequestService $setupRequestService,
            CustomerService $customerService,
            SessionService $sessionService,
            EventServiceProvider $eventServiceProvider
        ) {
            $this->jsonResponse = $jsonResponse;
            $this->proxyRequestService = $proxyRequestService;
            $this->setupRequestService = $setupRequestService;
            $this->customerService = $customerService;
            $this->customerSessionService = $sessionService;
            $this->eventServiceProvider = $eventServiceProvider;
            parent::__construct($context);
        }

        /**
         * @throws Exception
         */
        public function execute()
        {
            try {
                if (!array_key_exists('Bearer', $this->getRequest()->getParams())) {
                    throw new \Magento\Framework\Webapi\Exception(
                        __('Request Parameter: Bearer is required to perform this action'),
                        0,
                        \Magento\Framework\Webapi\Exception::HTTP_BAD_REQUEST
                    );
                }
                $params = $this->getRequest()->getParam('Bearer');
                $queryParam = $this->proxyRequestService->getRequestParam($params);
    
                if (!isset($queryParam->token, $queryParam->user_id)) {
                    return $this->jsonResponse->sendResponse(400,
                        'Access token and user id are required to perform this action');
                }
                $matchingCustomer = $this->setupRequestService->getProxyUser($queryParam->token, $queryParam->user_id);
                if (null === $matchingCustomer) {
                    return $this->jsonResponse->sendResponse(
                        404,
                        sprintf('No matching customer found with the provided id %d', $queryParam->user_id)
                    );
                }
        
                if (!$this->customerService->isActiveProxyUser($queryParam->user_id)) {
                    return $this->jsonResponse->sendResponse(
                        404,
                        sprintf('Customer account with the provided Id = %s has been disabled or suspended',
                            $queryParam->user_id)
                    );
                }
                if(!$this->proxyRequestService->isTokenExpired($matchingCustomer->getExpiryDate())){
                     $refreshedToken = $this->setupRequestService->getRefreshedToken($queryParam->user_id, $this->proxyRequestService->getCurrentTimeStamp());
                    if (!$refreshedToken) {
                        return $this->jsonResponse->sendResponse(
                            200,
                           'Token provided has expired'
                        );
                    }
                }
                if (!$this->customerSessionService->authoriseCustomer($queryParam->user_id)) {
                    return $this->jsonResponse->sendResponse(
                        401,
                        'Failure to authorise customer login'
                    );
                    
                }
    
                $customerSession = $this->customerSessionService->createCXMLSessionData($matchingCustomer);
                if($customerSession){
                    $this->customerSessionService->clearAuthUserCartSessionData();
                }
               
                
            } catch (NoSuchEntityException|LocalizedException $e) {
                $this->eventServiceProvider->dispatchExceptionPunchoutRequestEvent('CXML PunchOutSetupRequest Store Login',
                   'LoginProxy', sprintf('Message:%s File:%s', $e->getMessage(),
                        $e->getFile()));
                return $this->jsonResponse->sendResponse(
                    500,
                    $e->getMessage()
                );
            }
            $info = 'Successful CXML Store Login';
            $punchoutGroupId = $this->customerService->getPunchoutGroupId($queryParam->user_id);
            $this->eventServiceProvider->dispatchLoginProxyRequestEvent($queryParam->user_id,$punchoutGroupId,$info);
            return $this->_redirect('/');
        }
    }
