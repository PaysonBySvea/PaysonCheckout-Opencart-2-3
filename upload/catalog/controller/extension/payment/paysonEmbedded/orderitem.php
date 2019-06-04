<?php
namespace PaysonEmbedded{
    abstract class OrderItemType
    {
        const PHYSICAL = 'physical';
        const DISCOUNT = 'discount';
        const SERVICE  = 'service';
        const FEE      = 'fee';
    }
    
    class OrderItem {
        /** @var string $itemId */
        public $itemId;
        /** @var float $discountRate Discount rate of the article (Decimal number with two decimals (0.00-1.00)). */
        public $discountRate;
        /** @var float $creditedAmount Credited amount (Decimal number with two decimals (79.00)). */
        public $creditedAmount;
        /** @var url $imageUri URI to an image of the article. */
        public $imageUri;
        /** @var string $name Name of the article. */
        public $name;
        /** @var float $unitPrice Unit price including tax of the article (99.00). */
        public $unitPrice ;
        /** @var float $quantity  Quantity of the article (1.00). */
        public $quantity;
        /** @var float taxRate Tax rate of the article (0.00-1.00). */
        public $taxRate;
        /** @var string $reference Article reference, usually the article number. */
        public $reference;
        /** @var string $type Type of article ("Physical" (default), "Fee", "Discount" or "Service"). */
        public $type;
        /** @var url $uri URI to a the article page of the order item. */
        public $uri;
        /** @var string $ean European Article Number. (8-18 digits ("45678321465")) */
        public $ean;
 
        public function __construct($name, $unitPrice, $quantity, $taxRate, $reference, $type = OrderItemType::PHYSICAL, $discountRate = null, $ean = null, $uri = null, $imageUri = null, $creditedAmount = null) {
            if(!$name || is_null($unitPrice) || !$quantity || is_null($taxRate) || !$type || !$reference) {
                throw new PaysonApiException("Not all of mandatory fields are set for creating of an OrderItem object");
            }
            // Mandatory 
            $this->name = $name;
            $this->unitPrice = $unitPrice;
            $this->quantity = $quantity;
            $this->taxRate = $taxRate;
            $this->reference = $reference;
            // Optional
            $this->type = $type;
            $this->discountRate = $discountRate;
            $this->ean = $ean;
            $this->uri = $uri;
            $this->imageUri = $imageUri;
            $this->creditedAmount = $creditedAmount;
        }
        public static function create($data) {
            $item = new OrderItem($data->name, $data->unitPrice, $data->quantity, $data->taxRate, $data->reference, $data->type);
            $item->discountRate = isset($data->discountRate) ? $data->discountRate : null;
            $item->ean = isset($data->ean) ? $data->ean : null;
            $item->uri = isset($data->uri) ? $data->uri : null;
            $item->imageUri = isset($data->imageUri) ? $data->imageUri : null;
            $item->creditedAmount = isset($data->creditedAmount) ? $data->creditedAmount : null;
            $item->itemId = isset($data->itemId) ? $data->itemId : null;
            $item->totalPriceIncludingTax = isset($data->totalPriceIncludingTax) ? $data->totalPriceIncludingTax : null;
            $item->totalPriceExcludingTax = isset($data->totalPriceExcludingTax) ? $data->totalPriceExcludingTax : null;
            $item->totalTaxAmount = isset($data->totalTaxAmount) ? $data->totalTaxAmount : null;
            return $item;
        }
        
        public function toArray() {
            return get_object_vars($this);   
        }
    }
}