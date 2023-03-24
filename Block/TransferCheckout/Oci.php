<?php
    
    namespace Develodesign\Punchout\Block\TransferCheckout;
    
    use Magento\Catalog\Model\ProductRepository;
    use Magento\Framework\Exception\NoSuchEntityException;

    class Oci
    {
        protected $productRepository;
        public function __construct(
            ProductRepository $productRepository
        )
        {
          $this->productRepository = $productRepository;
        }
        
        public function getOCIForm(array $ociSessionData, $items, $punchoutGroupId): string
        {
            $form = sprintf(
                '<form id="punchout_oci_form"  action="%s" method="post" target="%s">',
                $ociSessionData['hook_url'],
                $ociSessionData['target']
            );
            $form .= sprintf('<input type="hidden" name="~caller" value="%s" />', $ociSessionData['caller']);
            $form .=  $this->getOciItems(items:$items,punchoutGroupId: $punchoutGroupId );
            $form .= '</form>';
            return $form;
        }
        
        public function getOciButton(string $configLabel): string
        {
            $form = '#punchout_oci_form';
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
        
        private function getOciItems($items,$punchoutGroupId): string
        {
            $i = 0;
            $itemCode = '';
            foreach ($items as $item) {
                if ($item->getParentItemId()) {
                    continue;
                }
                $i++;
                $itemCode .= $this->getOCIItemsByCompany($punchoutGroupId, $i, $item);
            }
            return $itemCode;
        }
        
        private function getOCIItemsByCompany(int $punchoutGroupId,$i, $item): string
        {
            $itemCode = $this->createSharedOciFields($i, $item);
            switch ($punchoutGroupId) {
                default: // All other companies
                    $unspsc = $this->getUnspscCode($item->getSku());
                    $schemaType = 'UNSPSC';
                    $itemCode .= $this->addUnspSchemaTypeFields($i, $unspsc, $schemaType, $unspsc);
                    break;
            }
            return $itemCode;
        }
    
        private function createSharedOciFields($i, $item): string
        {
            $leadtime = $item->getLeadTime();
            $desc = htmlentities($item->getDescription());
            return sprintf(
                '<input type="hidden" name="NEW_ITEM-DESCRIPTION[%d]"  value="%s">
                                        <input type="hidden" name="NEW_ITEM-UNIT[%d]"  value="EA">
                                        <input type="hidden" name="NEW_ITEM-PRICE[%d]"   value="%s">
                                        <input type="hidden" name="NEW_ITEM-PRICEUNIT[%d]"   value="1">
                                        <input type="hidden" name="NEW_ITEM-QUANTITY[%d]"   value="%s">
                                        <input type="hidden" name="NEW_ITEM-CURRENCY[%d]"   value="GBP">
                                        <input type="hidden" name="NEW_ITEM-LEADTIME[%d]"    value="%s">
                                        <input type="hidden" name="NEW_ITEM-MANUFACTCODE[%d]"    value="%s">
                                        <input type="hidden" name="NEW_ITEM-MANUFACTMAT[%d]"  value="%s">
                                        <input type="hidden" name="NEW_ITEM-LONGTEXT[%d]"   value="%s">
                                        <input type="hidden" name="NEW_ITEM-VENDORMAT[%d]"   value="%s">
                           ',
                $i,
                substr(
                    $item->getName(),
                    0,
                    40
                ),
                $i,
                $i,
                $item->getPrice(),
                $i,
                $i,
                $item->getQty(),
                $i,
                $i,
                $leadtime,
                $i,
                $item->getBrand(),
                $i,
                $item->getMpn(),
                $i,
                $desc,
                $i,
                $item->getSku()
            );
        }
    
        private function addUnspSchemaTypeFields($i, $matGrp, $schemaType, $unspsc): string
        {
            return sprintf(
                '<input type="hidden" name="NEW_ITEM-MATGROUP[%d]"  value="%s">
             <input type="hidden" name="NEW_ITEM-EXT_SCHEMA_TYPE[%d]"  value="%s">
             <input type="hidden" name="NEW_ITEM-EXT_CATEGORY_ID[%d]"  value="%s">',
                $i,
                $matGrp,
                $i,
                $schemaType,
                $i,
                $unspsc
            );
        }
        /**
         * @throws NoSuchEntityException
         */
        private function getUnspscCode($sku): string
        {
            return $this->productRepository->get($sku)->getUnspsc();
        }
        
        
    }
