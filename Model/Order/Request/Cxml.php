<?php

namespace Develodesign\Punchout\Model\Order\Request;

use Develodesign\Punchout\Exceptions\CxmlDocumentLoadingException;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Develodesign\Punchout\Model\PunchoutGroup;

class Cxml extends AbstractRequest
{
    protected $items;
    
    protected $cxml;
    
    /**
     * @throws CxmlDocumentLoadingException
     */
    public function isValid(): bool
    {
        if (!is_bool($this->getCxml())) {
            return true;
        }
        return false;
    }
    
    /**
     * @throws CxmlDocumentLoadingException
     */
    public function getCxml(): \SimpleXMLElement
    {
        if ($this->cxml === null) {
            $this->cxml = $this->cxmlService->parseOrderRequest($this->getDocument());
        }
        return $this->cxml;
    }
    
    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getCustomer(): CustomerInterface
    {
        $customer = parent::getCustomer();
        if (null !== $customer) {
            return $customer;
        }
	$customerEmail = '';
	$parsedXMLData = $this->cxml->Request->OrderRequest;
        $punchoutGroup = $this->getPunchoutGroup();
        $xpathSelector = $punchoutGroup->getCxmlNodeXpathConfigEmail();
	if($xpathSelector){
            $xpathEmail = $parsedXMLData->xpath($xpathSelector);
	    if($xpathEmail){
                $customerEmail = (string)$xpathEmail[0];
            }
	}
	if(!$customerEmail){
	    $customerEmail = (string)$this->cxml->Request->OrderRequest->OrderRequestHeader->Contact->Email;
        }
	return $this->customerService->getCustomerByEmail($customerEmail);
    }
    
    /**
     * @return PunchoutGroup
     * @throws NoSuchEntityException
     */
    public function getPunchoutGroup(): PunchoutGroup
    {
        $punchoutGroup = parent::getPunchoutGroup();
        if (null !== $punchoutGroup) {
            return $punchoutGroup;
        }
        $sharedSecret = (string)$this->cxml->Header->Sender->Credential->SharedSecret;
        $dunsIdentityConfig  = $this->punchoutConfigHelper->getDefaultDunsIdentitySource();
        $dunsIdentity = $this->cxmlService->getDunsIdentity($dunsIdentityConfig, $this->cxml);
        $aribaNetworkId = $this->cxmlService->getAribaNetworkId($this->cxml);
        
        if (!$aribaNetworkId) {
            $matchingResult = $this->punchoutGroupService->loadPunchOutGroupBySecretDuns($sharedSecret, $dunsIdentity);
            if ($matchingResult->getPunchoutgroupId()) {
                $punchoutGroup = $matchingResult;
            } elseif ($dunsIdentityConfig === 'both') {
                // Fallback to Sender Credential if From Credential is invalid or not found
                $dunsIdentity = (string)$this->cxml->Header->Sender->Credential->Identity;
                $matchingResult = $this->punchoutGroupService->loadPunchOutGroupBySecretDuns($sharedSecret, $dunsIdentity);
                if ($matchingResult->getPunchoutgroupId()) {
                    $punchoutGroup = $matchingResult;
                }
            }
        }
        if ($aribaNetworkId) {
            $networkResult = $this->punchoutGroupService->loadPunchOutGroupByAribaNetworkSecret(
                $sharedSecret,
                $aribaNetworkId
            );
            if ($networkResult->getPunchoutgroupId()) {
                $punchoutGroup = $networkResult;
            }
        }
        if ($punchoutGroup === null || !$punchoutGroup->getPunchoutgroupId()) {
            throw new NoSuchEntityException(
                __(
                    'No PunchoutGroup secret: %fieldValue, duns: %field2Value, ariba: %field3Value',
                    [
                        'fieldName'   => 'sharedSecret',
                        'fieldValue'  => $sharedSecret,
                        'field2Name'  => 'dunsIdentity',
                        'field2Value' => $dunsIdentity,
                        'field3Name'  => 'aribaNetworkId',
                        'field3Value' => $aribaNetworkId,
                    ]
                )
            );
        }
        
        return $punchoutGroup;
    }
    
    /**
     * Get ShipToAddress
     */
    public function getShipToAddress()
    {
        $address = $this->cxml->Request->OrderRequest->OrderRequestHeader->ShipTo->Address;
        return $this->cxmlService->parseAddress($address);
    }
    
    /**
     * Get billToAddress
     */
    public function getBillToAddress()
    {
        $address = $this->cxml->Request->OrderRequest->OrderRequestHeader->BillTo->Address;
        return $this->cxmlService->parseAddress($address);
    }
    
    /**
     * Gets Product Items from Cxml xml object
     * @return DataObject
     */
    public function getItems(): DataObject
    {
        if ($this->items === null) {
            $itemOuts = $this->cxml->Request->OrderRequest->ItemOut;
            $items = [];
            foreach ($itemOuts as $itemOut) {
                $distribution = [];
                $extrinsic = [];
                
                if (isset($itemOut->ItemDetail->Extrinsic)) {
                    $extrinsic = [$this->cxmlService->getItemOutExtrinsic((array)$itemOut->ItemDetail->Extrinsic)];
                }
                if (isset(
                    $itemOut->Distribution,
                    $itemOut->Distribution->Accounting,
                    $itemOut->Distribution->Accounting->Segment
                )
                    ) {
                    $distribution = [
                        'accounting_name' => (string)$itemOut->Distribution->Accounting->attributes()->name,
                        'segment' => sprintf(
                            'type: %s  id: %s',
                            $itemOut->Distribution->Accounting->Segment->attributes()->type,
                            $itemOut->Distribution->Accounting->Segment->attributes()->id
                        ),
                        'charge' => (string)$itemOut->Distribution->Charge->Money
                    ];
                }
                $optionalSupplierPartAuxId = 0;
                if (isset($itemOut->ItemID->SupplierPartAuxiliaryID)) {
                    $optionalSupplierPartAuxId = (string) $itemOut->ItemID->SupplierPartAuxiliaryID;
                }
                $sku = (string)$itemOut->ItemID->SupplierPartID;
                
                $item  = [
                    'quantity' => (string)$itemOut->attributes()->quantity,
                    'line_number' => (string)$itemOut->attributes()->lineNumber,
                    'product_sku' => $sku, // SKU
                    'internal_reference_id' => $optionalSupplierPartAuxId, // Magento Product Id
                    'unit_price' => (string)$itemOut->ItemDetail->UnitPrice->Money,
                    'description' => (string)$itemOut->ItemDetail->Description,
                    'uom' => (string)$itemOut->ItemDetail->UnitOfMeasure,
                    'unspsc' => (string)$itemOut->ItemDetail->Classification,
                    'vat' => (string)$itemOut->Tax->Money,
                    'man_part_id' => (string)$itemOut->ItemDetail->ManufacturerPartID,
                    'brand_name' => (string)$itemOut->ItemDetail->ManufacturerName,
                    'extrinsic' => $extrinsic,
                    'distribution' => $distribution
                ];
                $items[] = new DataObject($item);
            }
            
            $this->setItems($items);
            
        }
        return $this->items;
    }
    
    /**
     * Set items in request document
     */
    public function setItems($items): void
    {
        if (is_array($items)) {
            $set = new DataObject();
            $set->setData($items);
            $this->items = $set;
        } else {
            $this->items = $items;
        }
    }
    
    /**
     * Get PO number from request xml
     */
    public function getPoNumber(): string
    {
        $orderId = (string)$this->cxml->Request->OrderRequest->OrderRequestHeader->attributes()->orderID;
        $poNumber = $this->poNumber ?? $orderId;
        $this->poNumber = $poNumber;
        return $this->poNumber;
    }
    
    /**
     * Get shipping code or default to freeshipping_freeshipping
     */
    public function getShippingCode(): string
    {
        if (null === $this->shippingCode) {
            $this->shippingCode = $this->punchoutConfigHelper->getDefaultShippingMethod();
        }
        return $this->shippingCode;
    }
    
    /**
     * Get shipping price from xml request
     */
    public function getShippingPrice(): string
    {
        if (null === $this->shippingPrice) {
            $this->shippingPrice = (string)$this->cxml->Request->OrderRequest->OrderRequestHeader->Shipping->Money;
        }
        return $this->shippingPrice;
    }
    
    /**
     * Get tax int value from xml request
     */
    public function getTax(): int
    {
        if (!isset($this->tax)) {
            $this->tax = 20;
        }
        return $this->tax;
    }
    
    /**
     * Get payment method from xml request
     */
    public function getPaymentMethod(): string
    {
        $paymentMethod = $this->paymentMethod ?? 'purchaseorder';
        $this->paymentMethod = $paymentMethod;
        return $this->paymentMethod;
    }
    
    /**
     * Get Cxml order GrandTotal float from xml
     */
    public function getGrandTotal(): float
    {
        if (null === $this->grandTotal) {
            $this->grandTotal = (float) $this->cxml->Request->OrderRequest->OrderRequestHeader->Total->Money;
        }
        return $this->grandTotal;
    }
}
