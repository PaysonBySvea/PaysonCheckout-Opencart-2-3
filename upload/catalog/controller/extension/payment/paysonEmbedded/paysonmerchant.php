<?php
namespace PaysonEmbedded {
    class Merchant {
        /** @var url $checkoutUri URI to the merchants checkout page.*/
        public $checkoutUri = NULL;
        /** @var url $confirmationUri URI to the merchants confirmation page. */
        public $confirmationUri;
        /** @var url $notificationUri Notification URI which receives CPR-status updates. */
        public $notificationUri;
        /** @var url $validationUri Validation URI which is called to verify an order before it can be paid. */
        public $validationUri = null;
        /** @var url $termsUri URI som leder till s�ljarens villkor. */
        public $termsUri;
        /** @var string $reference Merchants own reference of the checkout.*/
        public $reference = NULL;
        /** @var int $partnerId Partners unique identifier */
        public $partnerId = NULL;
        /** @var string $integrationInfo Information about the integration. */
        public $integrationInfo = NULL;

        public function __construct($checkoutUri, $confirmationUri, $notificationUri, $termsUri, $validationUri,  $partnerId = NULL, $integrationInfo = ' PaysonEmbedded|1.0|NONE', $reference = NULL) {
            $this->checkoutUri = $checkoutUri;
            $this->confirmationUri = $confirmationUri;
            $this->notificationUri = $notificationUri;
            $this->termsUri = $termsUri;
            $this->validationUri = $validationUri;
            $this->partnerId = $partnerId;
            $this->integrationInfo = $integrationInfo;
            $this->reference = $reference;
        }
        
        public static function create($data) {
            $merchant =  new Merchant($data->checkoutUri,$data->confirmationUri,$data->notificationUri,$data->termsUri, $data->validationUri, $data->partnerId, $data->integrationInfo, $data->reference);
            return $merchant;
        }
     
        public function toArray(){
            return get_object_vars($this);      
        }
    }
}