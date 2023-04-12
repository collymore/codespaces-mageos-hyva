<?php
    
    namespace Develodesign\Punchout\Controller\Index;
    
    use Develodesign\Punchout\Event\EventServiceProvider;
    use Develodesign\Punchout\Response\CxmlResponse;
    use Develodesign\Punchout\Service\CustomerService;
    use Develodesign\Punchout\Service\CxmlService;
    use Develodesign\Punchout\Service\PunchoutGroupService;
    use Develodesign\Punchout\Service\SetupRequestService;
    use Magento\Framework\App\Action\Action;
    use Magento\Framework\App\Request\InvalidRequestException;
    use Magento\Framework\App\RequestInterface;
    
    class CxmlSetup extends Action implements \Magento\Framework\App\CsrfAwareActionInterface
    {
        /**
         * @var CxmlService
         */
        protected $cxmlService;
        
        /**
         * @var CxmlResponse
         */
        protected $cxmlResponse;
        
        /**
         * @var PunchoutGroupService
         */
        protected $punchoutGroupService;
        
        /**
         * @var CustomerService
         */
        protected $customerService;
    
        /**
         * @var SetupRequestService
         */
        protected $setupRequestService;
    
        /**
         * @var EventServiceProvider
         */
        protected $eventServiceProvider;
    
        public function __construct(
            \Magento\Framework\App\Action\Context $context,
            CxmlService $cxmlService,
            CxmlResponse $cxmlResponse,
            PunchoutGroupService $punchoutGroupService,
            CustomerService $customerService,
            SetupRequestService $setupRequestService,
            EventServiceProvider $eventServiceProvider
        ) {
            parent::__construct($context);
            $this->cxmlService = $cxmlService;
            $this->cxmlResponse = $cxmlResponse;
            $this->punchoutGroupService = $punchoutGroupService;
            $this->customerService = $customerService;
            $this->setupRequestService = $setupRequestService;
            $this->eventServiceProvider = $eventServiceProvider;
        }
        public function execute()
        {
            try {
                $xmlRawData = file_get_contents('php://input');
                if (!$xmlRawData) {
                    $this->eventServiceProvider->dispatchCxmlSetupRequestEvent('','','No POST data included in request');
                    return $this->cxmlResponse->respondWithData(400, 'Post body is missing or XML appears invalid');
                }
    
                $parsedXMLData = $this->cxmlService->parseXmlResponse($xmlRawData);
                $violations = $this->cxmlService->validateSetupRequest($parsedXMLData);
                if ($violations['error'] === true) {
                    $this->eventServiceProvider->dispatchCxmlSetupRequestEvent('', '',
                        "Validation Error : " . json_encode($violations));
        
                    return $this->cxmlResponse->respondWithData(422, json_encode($violations));
                }
                $sharedSecret = $parsedXMLData->Header->Sender->Credential->SharedSecret;
                $dunsIdentity = $parsedXMLData->Header->Sender->Credential->Identity;
                $aribaNetworkId = $this->cxmlService->getAribaNetworkId($parsedXMLData);
    
                $matchingPunchoutGroup = $this->punchoutGroupService->loadPunchOutGroupByCredentials($sharedSecret,
                    $dunsIdentity, $aribaNetworkId);
    
                $extrinsicData = $this->cxmlService->getExtrinsicData($parsedXMLData->Request->PunchOutSetupRequest->Extrinsic);
    
                if ($this->cxmlService->isCreate($parsedXMLData) === true) {
                    $useEmail = $this->cxmlService->fetchEmail($extrinsicData, $parsedXMLData);
                    if (empty(trim($useEmail))) {
                        $useEmail = $this->cxmlService->createEmail($extrinsicData, $parsedXMLData,
                            $matchingPunchoutGroup->getGroupEmail());
                    }
                    $matchingCustomer = $this->customerService->fetchCustomer($useEmail);
                    if (!$matchingCustomer->getId()) {
                        $nameData = $this->cxmlService->getFirstLastName($extrinsicData);
                        $customerDTO = $this->customerService->prepareCustomerData($matchingPunchoutGroup, $useEmail,
                            $nameData);
                        $matchingCustomer = $this->customerService->createCustomer($customerDTO);
                        $this->customerService->createCustomerAddress($matchingCustomer, $matchingPunchoutGroup);
                    }
                    $setupRequestDTO = $this->setupRequestService->prepareSetupData($matchingCustomer->getId(),
                        $parsedXMLData);
                    $punchoutSetupRequestModel = $this->setupRequestService->createPunchoutSetupRequest($setupRequestDTO);
                    $startUrl = $this->setupRequestService->getStartUpUrlResponse($punchoutSetupRequestModel);
                    $info = 'Successful CXML PunchOutSetupResponse';
                    $this->eventServiceProvider->dispatchCxmlSetupRequestEvent($matchingCustomer->getId(),
                        $matchingPunchoutGroup->getPunchoutgroupId(), $info);
        
                    return $this->cxmlResponse->punchoutUpResponse(200, $parsedXMLData->getAttribute('payloadID'),
                        $startUrl);
        
                }
    
    
            } catch (\Exception $exception) {
                $this->eventServiceProvider->dispatchExceptionPunchoutRequestEvent('CXML PunchOutSetupRequest',
                    'CxmlSetup',
                    sprintf('Message:%s File:%s', $exception->getMessage(),
                        $exception->getFile()));
    
                return $this->cxmlResponse->respondWithData(400, $exception->getMessage());
            }
    
            return $this->cxmlResponse->respondSuccess();
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

