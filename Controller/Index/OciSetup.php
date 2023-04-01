<?php

namespace Develodesign\Punchout\Controller\Index;

    use Develodesign\Punchout\Event\EventServiceProvider;
    use Develodesign\Punchout\Response\JsonResponse;
    use Develodesign\Punchout\Service\CustomerService;
    use Develodesign\Punchout\Service\OciService;
    use Develodesign\Punchout\Service\PunchoutGroupService;
    use Develodesign\Punchout\Service\SessionService;
    use Magento\Framework\App\Action\Action;
    use Magento\Framework\App\Action\Context as ActionContext;
    use Magento\Framework\App\CsrfAwareActionInterface;
    use Magento\Framework\App\Request\InvalidRequestException;
    use Magento\Framework\App\RequestInterface;
    use Magento\Framework\Exception\AlreadyExistsException;
    use Magento\Framework\Exception\LocalizedException;
    use Magento\Framework\Exception\NoSuchEntityException;

    class OciSetup extends Action implements CsrfAwareActionInterface
    {
        protected $ociService;

        protected $jsonResponse;
        
        protected $punchoutGroupService;
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
            OciService $ociService,
            PunchoutGroupService $punchoutGroupService,
            JsonResponse $jsonResponse,
            CustomerService $customerService,
            SessionService $sessionService,
            EventServiceProvider $eventServiceProvider
        ) {
            $this->ociService = $ociService;
            $this->punchoutGroupService = $punchoutGroupService;
            $this->jsonResponse = $jsonResponse;
            $this->customerService = $customerService;
            $this->customerSessionService = $sessionService;
            $this->eventServiceProvider = $eventServiceProvider;
            parent::__construct($context);
        }
    
        /**
         * @throws \Zend_Validate_Exception
         * @throws NoSuchEntityException
         * @throws AlreadyExistsException
         * @throws LocalizedException
         */
        public function execute()
        {
            try {
                $params = $this->getRequest()->getParams();
                $requestBody = $this->ociService->prepareOCIRequestBody($params);
                $validatedPostBody = $this->ociService->validateRequest($requestBody);
                if (!is_array($validatedPostBody)) {
                    return $this->jsonResponse->sendResponse(
                        400,
                        'Request Parameter(s) Username, Password and Hook URL are required to perform this action'
                    );
                }
                if (!isset($validatedPostBody['~target'])) {
                    $validatedPostBody['~target'] = '_BLANK';
                }
                $configParam = $this->ociService->validateSetupConfiguredParam($validatedPostBody);
    
                if (!is_array($configParam)) {
                    return $this->jsonResponse->sendResponse(
                        422,
                        $configParam
                    );
                }
                if  (!$this->ociService->isValidEmail($validatedPostBody['username'])) {
                    return $this->jsonResponse->sendResponse(
                        422,
                        sprintf('Valid email is required to perform this action, %s provided', $validatedPostBody['username'])
                    );
        
                }
    
                $matchingPunchoutGroup = $this->punchoutGroupService->loadPunchOutGroupByOciCredentials(
                    username: $validatedPostBody['username'],
                    password: $validatedPostBody['password']
                );
                if (null === $matchingPunchoutGroup) {
                    return $this->jsonResponse->sendResponse(
                        404,
                        sprintf('No such PunchoutGroup entity with the provided credentials %s',$validatedPostBody['username'])
                    );
        
                }
                $matchingCustomer = $this->customerService->fetchCustomer($validatedPostBody['username']);
                if(!$matchingCustomer->getId()){
                    $nameData = $this->ociService->getFirstLastName(name:$matchingPunchoutGroup->getGroupName());
                    $customerDTO = $this->customerService->prepareCustomerData(matchingPunchoutGroup:$matchingPunchoutGroup,email:$validatedPostBody['username'],nameData:$nameData);
                    $matchingCustomer = $this->customerService->createCustomer($customerDTO);
                    $this->customerService->createCustomerAddress(customer:$matchingCustomer,punchoutGroup:$matchingPunchoutGroup);
        
                }
                if (!$this->customerSessionService->authoriseCustomer($matchingCustomer->getId())) {
                    return $this->jsonResponse->sendResponse(
                        401,
                        'Failure to authorise customer login'
                    );
        
                }
                $customerSession = $this->customerSessionService->createOCISessionData($validatedPostBody);
                if($customerSession){
                    $this->customerSessionService->clearAuthUserCartSessionData();
                }
                $info = 'Successful OCI PunchOutSetupResponse and Store Login';
                $this->eventServiceProvider->dispatchCxmlSetupRequestEvent($matchingCustomer->getId(),$matchingPunchoutGroup->getPunchoutgroupId(),$info);
                return $this->_redirect('/');
                
            } catch (\Exception $exception) {
                return $this->jsonResponse->sendResponse(
                    500,
                    $exception->getMessage()
                );
            }
            
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
