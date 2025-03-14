<?php

namespace Develodesign\Punchout\Response;

use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;
use Develodesign\Punchout\Helper\PunchoutConfigHelper;

class CxmlResponse
{
    /**
     * @var PunchoutConfigHelper
     */
    protected $configHelper;
    
    /**
     * @var RawFactory
     */
    protected $resultRawFactory;
    public function __construct(
        RawFactory $rawFactory,
        PunchoutConfigHelper $configHelper
    ) {
        $this->resultRawFactory = $rawFactory;
        $this->configHelper = $configHelper;
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

    public function respondWithData($statusCode, $message, $payloadId =''): Raw
    {
        $result = $this->resultRawFactory->create();
        $result->setHeader('Content-Type', 'text/xml');
        $result->setContents('<?xml version="1.0" encoding="UTF-8"?>
                     <!DOCTYPE cXML SYSTEM "http://xml.cxml.org/schemas/cXML/1.2.055/cXML.dtd">
                        <cXML version="1.2.055"  xml:lang="en" timestamp="' . $this->getTimeStamp() . '" payloadID="'.$payloadId.'">
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
        $defaultCurrencyCode = $this->configHelper->getDefaultCurrencyCode();
        $totalTax = $punchoutOrder['cxml_node_tax_message_header'] ?? 0.0;
        $shippingCost = $punchoutOrder['cxml_node_shipping_cost_message_header'] ?? 0.0;
        $shippingDescription = $punchoutOrder['cxml_node_shipping_method'] ?? '';
        $taxDescription = 'Sales Tax';
        return sprintf(
            '<?xml version="1.0" encoding="UTF-8"?>
                    <!DOCTYPE cXML SYSTEM "http://xml.cxml.org/schemas/cXML/1.2.055/cXML.dtd">
                    <cXML version="1.2.055" payloadID="%s" timestamp="%s" xml:lang="en">
                        <Header>
                            <From>
                                <Credential domain="DUNS">
                                    <Identity>%s</Identity>
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
                                        <Money currency="%s">%s</Money>
                                    </Total>
                                    %s
                                    %s
                                </PunchOutOrderMessageHeader>',
            $cxmlSessionData['payloadId'],
            $this->getTimeStamp(),
            $this->configHelper->getDefaultDunsNumber() ?? '',
            $punchoutOrder['punchoutgroup_duns'] ?? $cxmlSessionData['sender_identity'],
            $cxmlSessionData['sender_identity'],
            $cxmlSessionData['buyer_cookie'],
            $defaultCurrencyCode,
            $punchoutOrder['grand_total'],
            $totalTax > 0 ? sprintf('<Tax><Money currency="%s">%s</Money><Description>%s</Description></Tax>',
                $defaultCurrencyCode, number_format($totalTax, 2), $taxDescription) : '',
            $shippingCost > 0 ? sprintf('<Shipping><Money currency="%s">%s</Money><Description xml:lang="en-US">%s</Description></Shipping>',
                $defaultCurrencyCode, number_format($shippingCost, 2), $shippingDescription) : ''
        
        );
    }
}
