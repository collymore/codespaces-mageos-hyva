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
                        <cXML version="1.2.055"  xml:lang="en" timestamp=""' . $this->getTimeStamp() . '"">
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
                        <cXML version="1.2.055"  xml:lang="en" timestamp="' . $this->getTimeStamp() . '">
                          <Response>
                            <Status code="' . $statusCode . '" text="' . $message . '" />
                          </Response>
                        </cXML>');
        $result->setStatusHeader($statusCode);
        return $result;
    }

    public function punchoutUpResponse($statusCode, $payloadId, $startUrl): Raw
    {
        $result = $this->resultRawFactory->create();
        $result->setHeader('Content-Type', 'text/xml');
        $result->setContents('<?xml version="1.0" encoding="UTF-8"?>
            <!DOCTYPE cXML SYSTEM "http://xml.cxml.org/schemas/cXML/1.1.010/cXML.dtd">
            <cXML payloadID="'.$payloadId.'" version="1.1.007" xml:lang="en" timestamp="' . $this->getTimeStamp() . '">
                <Response>
                <Status code="' . $statusCode . '" text="success"></Status>
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

    private function getTimeStamp()
    {
        return (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d\TH:i:s');
    }
    
    public function getPunchoutOrderMessage(array $cxmlSessionData, array $punchoutOrder): string
    {
        return sprintf(
            '<?xml version="1.0" encoding="UTF-8"?>
                    <!DOCTYPE cXML SYSTEM "http://xml.cxml.org/schemas/cXML/1.2.055/cXML.dtd">
                    <cXML version="1.2.055" payloadID="%s" timestamp="%s" xml:lang="en">
                        <Header>
                            <From>
                                <Credential domain="DUNS">
                                    <Identity></Identity>
                                </Credential>
                            </From>
                            <To>
                                <Credential domain="DUNS">
                                    <Identity>%s</Identity>
                                </Credential>
                            </To>
                            <Sender>
                                <Credential domain="DUNS">
                                    <Identity>%s</Identity>
                                </Credential>
                                <UserAgent>Store</UserAgent>
                            </Sender>
                        </Header>
                        <Message deploymentMode="production">
                            <PunchOutOrderMessage>
                                <BuyerCookie>%s</BuyerCookie>
                                <PunchOutOrderMessageHeader operationAllowed="create">
                                    <Total>
                                        <Money currency="GBP">%s</Money>
                                    </Total>
                                </PunchOutOrderMessageHeader>',
            $cxmlSessionData['payloadId'],
            $this->getTimeStamp(),
            $punchoutOrder['punchoutgroup_duns'] ?? $cxmlSessionData['sender_identity'],
            $cxmlSessionData['sender_identity'],
            $cxmlSessionData['buyer_cookie'],
            $punchoutOrder['grand_total']
        );
    }
}
