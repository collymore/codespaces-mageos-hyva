<?php

    namespace Develodesign\Punchout\Response;

    use Magento\Framework\Controller\Result\Json;
    use Magento\Framework\Controller\Result\JsonFactory;

    class JsonResponse
    {
        /**
         * @var JsonFactory
         */
        protected $jsonFactory;
        public function __construct(
            JsonFactory $jsonFactory
        ) {
            $this->jsonFactory = $jsonFactory;
        }

        public function sendResponse($statusCode, $data): Json
        {
            return $this->jsonFactory->create()
                ->setHeader('Content-Type', 'application/json')
                ->setHttpResponseCode($statusCode)
                ->setData($data);
        }
    }
