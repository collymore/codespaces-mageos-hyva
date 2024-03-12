<?php
    
namespace Develodesign\Punchout\Block\TransferCheckout;

use Develodesign\Punchout\Response\CxmlResponse;
use Develodesign\Punchout\Service\CxmlService;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Develodesign\Punchout\Helper\PunchoutConfigHelper;

class Cxml
{
    protected $cxmlResponse;
        
    protected $productRepository;
    
    protected $cxmlService;

    protected $configHelper;
    
    public function __construct(
        CxmlResponse $cxmlResponse,
        ProductRepository $productRepository,
        CxmlService $cxmlService,
        PunchoutConfigHelper $configHelper
    ) {
        $this->cxmlResponse = $cxmlResponse;
        $this->productRepository = $productRepository;
        $this->cxmlService = $cxmlService;
        $this->configHelper = $configHelper;
    }
    
    /**
     * @throws NoSuchEntityException
     */
    public function getCxmlForm($cxmlSessionData, $punchoutOrder, $quote, $uom): string
    {
        $xml = $this->cxmlResponse->getPunchoutOrderMessage($cxmlSessionData, $punchoutOrder);
        $xml .= $this->getCXMLItems($quote->getAllItems(), $uom);
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
     * @throws NoSuchEntityException
     */
    private function getCXMLItems($items, $uom): string
    {
	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();       
	$storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
	$defaultCurrencyCode = $storeManager->getStore()->getCurrentCurrencyCode();
        $itemCode = '';
        foreach ($items as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            //remove double and single quotes from product names as it's breaking punchout
            $name = str_replace("'", "", $item->getName());
            $name = str_replace('"', "", $name);
            $unspsc = $this->getUnspscCode($item->getSku());
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
                    </ItemIn>',
                $item->getQty(),
                $item->getSku(),
                $item->getId(),
		$defaultCurrencyCode,
                $item->getPrice(),
                $name,
                $uom,
                $unspsc,
                $item->getBrand()
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
        if($this->productRepository->get($sku)){
        	return $this->productRepository->get($sku)->getUnspscCode();
	    }else{
            return '';
        }
    }

    public function getConfigHelper() : PunchoutConfigHelper
    {
        return $this->configHelper;
    }
}
