<?php

namespace Develodesign\Punchout\Response;

    use Magento\Framework\Controller\Result\RawFactory;

    class CxmlResponse
    {
        /**
         * @var RawFactory
         */
        protected $resultRawFactory;
        public function __construct(
            RawFactory $rawFactory
        ) {
            $this->resultRawFactory = $rawFactory;
        }

        public function respondSuccess()
        {
            $result = $this->resultRawFactory->create();
            $result->setHeader('Content-Type', 'text/xml');
            $result->setContents('<?xml version="1.0" encoding="UTF-8"?>
                     <!DOCTYPE cXML SYSTEM "http://xml.cxml.org/schemas/cXML/1.2.055/cXML.dtd">
                        <cXML payloadID="" version="1.2.055"  xml:lang="en" timestamp="">
                          <Response>
                            <Status code="200" text="OK" />
                          </Response>
                        </cXML>');
            $result->setStatusHeader(200);
            return $result;
        }

        public function respondWithData($statusCode, $message)
        {
            $result = $this->resultRawFactory->create();
            $result->setHeader('Content-Type', 'text/xml');
            $result->setContents('<?xml version="1.0" encoding="UTF-8"?>
                     <!DOCTYPE cXML SYSTEM "http://xml.cxml.org/schemas/cXML/1.2.055/cXML.dtd">
                        <cXML payloadID="" version="1.2.055"  xml:lang="en" timestamp="">
                          <Response>
                            <Status code="' . $statusCode . '" text="' . $message . '" />
                          </Response>
                        </cXML>');
            $result->setStatusHeader($statusCode);
            return $result;
        }
        
        public function formatErrorMessage(array $violations)
        {
           $messages = [];
            foreach ($violations as $violation){
                $messages[] = $violation;
            }
            return $messages;
        }
    }
