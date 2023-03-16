<?php

namespace Develodesign\Punchout\Response;

    use Magento\Framework\Controller\Result\Raw;
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

        public function respondSuccess(): Raw
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

        public function respondWithData($statusCode, $message): Raw
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

        public function punchoutUpResponse($statusCode, $startUrl): Raw
        {
            $result = $this->resultRawFactory->create();
            $result->setHeader('Content-Type', 'text/xml');
            $result->setContents('<?xml version="1.0" encoding="UTF-8"?>
                       <!DOCTYPE cXML SYSTEM "http://xml.cxml.org/schemas/cXML/1.1.010/cXML.dtd">
                        <cXML payloadID="" version="1.1.007" xml:lang="en" timestamp="">
                           <Response>
                            <Status code="' . $statusCode. '" text="success"></Status>
                            <PunchOutSetupResponse>
                              <StartPage>
                                 <URL>' . $startUrl . '</URL>
                              </StartPage>
                            </PunchOutSetupResponse>
                          </Response>
                        </cXML>');
            $result->setStatusHeader($statusCode);
            return $result;
        }

        
    }
