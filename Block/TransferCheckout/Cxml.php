<?php

namespace Develodesign\Punchout\Block\TransferCheckout;

use Develodesign\Punchout\Response\CxmlResponse;
use Develodesign\Punchout\Service\CxmlService;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Develodesign\Punchout\Helper\PunchoutConfigHelper;
use Magento\Quote\Model\Quote\Item;
use Magento\Tax\Model\TaxCalculation;

class Cxml
{
    protected $cxmlResponse;
    
    protected $productRepository;
    
    protected $cxmlService;

    protected $configHelper;
    
    protected $taxCalculation;
    
    public function __construct(
        CxmlResponse $cxmlResponse,
        ProductRepository $productRepository,
        CxmlService $cxmlService,
        PunchoutConfigHelper $configHelper,
        TaxCalculation $taxCalculation
    ) {
        $this->cxmlResponse = $cxmlResponse;
        $this->productRepository = $productRepository;
        $this->cxmlService = $cxmlService;
        $this->configHelper = $configHelper;
        $this->taxCalculation = $taxCalculation;
    }
    
    /**
     * @throws NoSuchEntityException
     */
    public function getCxmlForm($cxmlSessionData, $punchoutOrder, $quote, $uom): string
    {
        $xml = $this->cxmlResponse->getPunchoutOrderMessage($cxmlSessionData, $punchoutOrder);
        $xml .= $this->getCXMLItems($quote->getAllItems(), $uom,$punchoutOrder);
        $xml .= '</PunchOutOrderMessage>
                            </Message>
                        </cXML>';
        return sprintf("<form id=\"punchout_cxml_form\"
                            action=\"%s\" method=\"post\"
                            enctype=\"application/x-www-form-urlencoded\">
                        <input name=\"cXML-urlencoded\"
                            id=\"urlencoded_bottom\"
                            type=\"hidden\" value= '%s'>
                        </form>", $cxmlSessionData['return_url'], $xml);
    }
    
    /**
     * @param $items Item[]
     * @param string $uom
     * @param array $punchoutOrder
     * @throws NoSuchEntityException
     */
    private function getCXMLItems($items, $uom, $punchoutOrder): string
    {
	$defaultCurrencyCode = $this->configHelper->getDefaultCurrencyCode();
        $itemCode = '';
        foreach ($items as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            //remove double and single quotes from product names as it's breaking punchout
            $name = str_replace("'", "", $item->getName());
            $name = str_replace('"', "", $name);
            $unspsc = trim($this->getUnspscCode($item->getSku()));
            if (!$unspsc) {
                $unspsc = $this->configHelper->getDefaultUnspsc();
            }
            $taxAmount = $this->getItemTaxAmount($punchoutOrder, $item->getSku());
            
            $unitPrice = $item->getCustomPrice() ?? $item->getPrice();
            $itemCode .= sprintf(
                '<ItemIn quantity="%s">
                        <ItemID>
                            <SupplierPartID>%s</SupplierPartID>
                            <SupplierPartAuxiliaryID>%s</SupplierPartAuxiliaryID>
                        </ItemID>
                        <ItemDetail>
                            <UnitPrice>
                                <Money currency="%s">%s</Money>
                            </UnitPrice>
                            <Description xml:lang="en"><![CDATA[%s]]></Description>
                            <UnitOfMeasure>%s</UnitOfMeasure>
                            <Classification domain="UNSPSC">%s</Classification>
                            <ManufacturerName>%s</ManufacturerName>
                        </ItemDetail>
                        %s
                    </ItemIn>',
                $item->getQty(),
                $item->getSku(),
                $item->getId(),
                $defaultCurrencyCode,
                number_format((float)$unitPrice, 2, '.', ''),
                $name,
                $uom,
                $unspsc,
                $item->getBrand(),
                $taxAmount !== '' ? sprintf('<Tax><Money currency="%s">%s</Money></Tax>', $defaultCurrencyCode, $taxAmount) : ''
            );
        }
        return $itemCode;
    }
    
    public function getSubmitButton(string $configLabel)
    {
        $form = '#punchout_cxml_form';
        $modal = '#punchout-modal';
        $label = $configLabel ?? 'Transfer Basket Items With Punchout';
    
        return '<button class=" button btn-proceed-checkout btn-checkout" id="punchout-button-submit" type="button">
                                <span>
                                    <span>' . $label . '</span>
                                </span>
                    </button>
                    <script>
                    require([\'jquery\', \'jquery/ui\'], function($){
                      $( "#punchout-button-submit" ).click(function() {
                            $("' . $modal . '").show();
                            $("' . $form . '").submit();
                        });
                    });
                    </script>';
    }
    
    /**
     * @throws NoSuchEntityException
     */
    private function getUnspscCode($sku)
    {
        if ($product = $this->productRepository->get($sku)) {
            return $product->getUnspscCode();
        }
        return '';
    }

    public function getConfigHelper() : PunchoutConfigHelper
    {
        return $this->configHelper;
    }
    
    /**
     * @throws NoSuchEntityException
     */
    private function getItemTaxAmount($punchoutOrder, $sku): string
    {
        $taxPercent = '';
        if (array_key_exists('cxml_node_tax_per_item',
                $punchoutOrder) && $product = $this->productRepository->get($sku)) {
                    $productTaxClassID = $product->getTaxClassId();
                    if($productTaxClassID){
                        $taxPercent = $this->taxCalculation->getCalculatedRate($productTaxClassID);
                        
                        // Format the tax amount to two decimal places
                        return number_format($taxPercent, 2);
                    }
                    
                }
        return $taxPercent;
    }
}
