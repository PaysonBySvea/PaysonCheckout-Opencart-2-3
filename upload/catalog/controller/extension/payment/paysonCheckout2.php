<?php

class ControllerExtensionPaymentPaysonCheckout2 extends Controller {
    private $testMode;
    public $data = array();

    const MODULE_VERSION = 'paysonEmbedded_1.0.4.3';

    function __construct($registry) {
        parent::__construct($registry);
        $this->testMode = ($this->config->get('paysonCheckout2_mode') == 0);
    }

    public function index() {
        $this->load->language('extension/payment/paysonCheckout2');
        $iframeSetup = array();
        
        $this->data['text_payson_comments'] = $this->language->get('text_payson_comments'); 
        $this->data['error_checkout_id'] = $this->language->get('error_checkout_id');
        $this->data['info_checkout'] = $this->language->get('info_checkout');
        $this->data['country_code'] = isset($this->session->data['payment_address']['iso_code_2'])? $this->session->data['payment_address']['iso_code_2'] : NULL;
        $this->data['is_comments'] = $this->config->get('paysonCheckout2_comments') == 1?1:0;
        $this->data['customerIsLogged'] = !$this->customer->isLogged() ? 0 : 1 ;

        if($this->config->get('paysonCheckout2_request_registered_customer') && !$this->customer->isLogged()){
            return $this->load->view('extension/payment/paysonCheckout_registered_customer', $this->data);
        }else{ 
            if (isset($this->request->get['snippet'])) {
                $iframeSetup['snippet'] = $this->getSnippetUrl($this->request->get['snippet']);
                $iframeSetup['width'] = (int) $this->config->get('paysonCheckout2_iframe_size_width');
                $iframeSetup['width_type'] = $this->config->get('paysonCheckout2_iframe_size_width_type');
                $iframeSetup['height'] = (int) $this->config->get('paysonCheckout2_iframe_size_height');
                $iframeSetup['height_type'] = $this->config->get('paysonCheckout2_iframe_size_height_type');
                $iframeSetup['status'] = 'readyToPay';
                $iframeSetup['column_left'] = $this->load->controller('common/column_left');
                $iframeSetup['column_right'] = $this->load->controller('common/column_right');
                $iframeSetup['content_top'] = $this->load->controller('common/content_top');
                $iframeSetup['content_bottom'] = $this->load->controller('common/content_bottom');
                $iframeSetup['footer'] = $this->load->controller('common/footer');
                $iframeSetup['header'] = $this->load->controller('common/header');
            }

            if (count($iframeSetup) > 0) {
                $this->load->model('checkout/order');                
                $this->response->setOutput($this->load->view('extension/payment/paysonCheckout2', $iframeSetup));
            } else {
                $this->setupPurchaseData();
                return $this->load->view('extension/payment/paysonCheckout2', $this->data);
            }
        }
    }

    public function getSnippetUrl($snippet) {
        $url = explode("url='", $snippet);
        $checkoutUrl = explode("'", $url[1]);
        return $checkoutUrl[0];
    }

    public function confirm() {
        if ($this->session->data['payment_method']['code'] == 'paysonCheckout2') {
            $this->setupPurchaseData();
        }
    }

    private function setupPurchaseData() {
        $this->load->language('extension/payment/paysonCheckout2');
        $this->load->model('checkout/order');

        $order_data = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $this->data['store_name'] = html_entity_decode($order_data['store_name'], ENT_QUOTES, 'UTF-8');
        $this->data['payson_comment'] = html_entity_decode($order_data['comment'], ENT_QUOTES, 'UTF-8');
        //Payson send the responds to the shop 
                
        $this->data['ok_url'] = $this->url->link('extension/payment/paysonCheckout2/returnFromPayson&order_id=' . $this->session->data['order_id']);
        $this->data['ipn_url'] = $this->url->link('extension/payment/paysonCheckout2/paysonIpn&order_id=' . $this->session->data['order_id']);
        $this->data['checkout_url'] = $this->url->link('extension/payment/paysonCheckout2/returnFromPayson&order_id=' . $this->session->data['order_id']);
        $this->data['terms_url'] = $this->url->link('information/information/agree', 'information_id=5');
        $this->data['validation_url'] = $this->url->link('extension/payment/paysonCheckout2/validation&order_id=' . $this->session->data['order_id']);

        $this->data['order_id'] = $order_data['order_id'];
        $this->data['amount'] = $this->currency->format($order_data['total'] * 100, $order_data['currency_code'], $order_data['currency_value'], false) / 100;
        $this->data['currency_code'] = $order_data['currency_code'];
        $this->data['language_code'] = $order_data['language_code'];
        $this->data['salt'] = md5($this->config->get('paysonCheckout2_secure_word')) . '1-' . $this->data['order_id'];
        //Customer info
        $this->data['sender_email'] = isset($order_data['customer_id']) ? $order_data['email'] : '';
        //$this->data['sender_email'] = (int)$order_data['customer_id'] > 0 ? $order_data['email'] : '';
        $this->data['sender_first_name'] = $this->customer->isLogged()? html_entity_decode($order_data['payment_firstname'], ENT_QUOTES, 'UTF-8') : $this->session->data['payment_address']['firstname'];
        $this->data['sender_last_name'] = $this->customer->isLogged()? html_entity_decode($order_data['payment_lastname'], ENT_QUOTES, 'UTF-8') : $this->session->data['payment_address']['lastname'];
        $this->data['sender_telephone'] = html_entity_decode($order_data['telephone'], ENT_QUOTES, 'UTF-8');
        $this->data['sender_address'] = $this->customer->isLogged()? html_entity_decode($order_data['payment_address_1'], ENT_QUOTES, 'UTF-8'): $this->session->data['payment_address']['address_1'];
        $this->data['sender_postcode'] = $this->customer->isLogged() ? html_entity_decode($order_data['payment_postcode'], ENT_QUOTES, 'UTF-8'): $this->session->data['payment_address']['postcode'];
        $this->data['sender_city'] = $this->customer->isLogged()? html_entity_decode($order_data['payment_city'], ENT_QUOTES, 'UTF-8') : $this->session->data['payment_address']['city'];
        $this->data['sender_countrycode'] = $this->customer->isLogged()? html_entity_decode($order_data['payment_iso_code_2'], ENT_QUOTES, 'UTF-8'): $this->session->data['payment_address']['iso_code_2'];

        //Call PaysonAPI        
        $result = $this->getPaymentURL();

        $returnData = array();

        if ($result != NULL AND $result->status == "created") {
            $this->data['checkoutId'] = $result->id;
            $this->data['width'] = (int) $this->config->get('paysonCheckout2_iframe_size_width');
            $this->data['height'] = (int) $this->config->get('paysonCheckout2_iframe_size_height');
            $this->data['width_type'] = $this->config->get('paysonCheckout2_iframe_size_width_type');
            $this->data['height_type'] = $this->config->get('paysonCheckout2_iframe_size_height_type');
            $this->data['testMode'] = !$this->testMode ? TRUE : FALSE;
            $this->data['snippet'] = $result->snippet;
            $this->data['status'] = $result->status;
        } else {

            $returnData["error"] = $this->language->get("text_payson_payment_error");
        }
    }

    private function getPaymentURL() {
        require_once 'paysonEmbedded/paysonapi.php';
        $this->load->language('extension/payment/paysonCheckout2');

        $callPaysonApi = $this->getAPIInstanceMultiShop();
        $paysonMerchant = new PaysonEmbedded\Merchant($this->data['checkout_url'], $this->data['ok_url'], $this->data['ipn_url'], $this->data['terms_url'], $this->data['validation_url'], null, ('PaysonCheckout2.0_Opencart2.3|' . $this->config->get('paysonCheckout2_modul_version') . '|' . VERSION));
        
        $paysonMerchant->reference = $this->session->data['order_id'];
        $payData = new PaysonEmbedded\PayData($this->currencypaysonCheckout2());

        $this->getOrderItems($payData);

        $gui = new PaysonEmbedded\Gui($this->languagepaysonCheckout2(), $this->config->get('paysonCheckout2_color_scheme'), $this->config->get('paysonCheckout2_verification'), (int) $this->config->get('paysonCheckout2_request_phone'),  (int) $this->config->get('paysonCheckout2_request_phone_optional'));
        $customer = new PaysonEmbedded\Customer(
                $this->data['sender_first_name'], $this->data['sender_last_name'], $this->data['sender_email'], $this->data['sender_telephone'], '', $this->data['sender_city'], $this->data['sender_countrycode'], $this->data['sender_postcode'], $this->data['sender_address']);
        
        $checkout = new PaysonEmbedded\Checkout($paysonMerchant, $payData, $gui, $customer, $this->session->data['order_id']);
    
        $checkoutTempObj = NULL;
        try {
            $paysonEmbeddedStatus = '';
            if ($this->getCheckoutIdPayson($this->session->data['order_id']) != Null) {
                $checkoutTempObj = $callPaysonApi->GetCheckout($this->getCheckoutIdPayson($this->session->data['order_id']));
                //$callPaysonApi->doRequest('GET', $this->getCheckoutIdPayson($this->session->data['order_id']));
                $paysonEmbeddedStatus = $checkoutTempObj->status;
            }

            if ($this->getCheckoutIdPayson($this->session->data['order_id']) != Null AND $paysonEmbeddedStatus == 'created') {
                $checkoutIdTemp = $callPaysonApi->CreateCheckout($checkout);
                $checkoutTemp = $callPaysonApi->GetCheckout($checkoutIdTemp);
                $checkoutTempObj = $callPaysonApi->UpdateCheckout($checkoutTemp);

                if ($checkoutTempObj->id != null) {
                    $this->storePaymentResponseDatabase($checkoutTempObj->id, $this->session->data['order_id']);
                }
            } else {
                $checkoutId = $callPaysonApi->CreateCheckout($checkout);
                $checkoutTempObj = $callPaysonApi->GetCheckout($checkoutId);

                if ($checkoutTempObj->id != null) {
                    $this->storePaymentResponseDatabase($checkoutTempObj->id, $this->session->data['order_id']);
                }
            }
            //$callPaysonApi->doRequest();

            return $checkoutTempObj;
        } catch (Exception $e) {
            $message = '<Payson OpenCart Checkout 2.0> ' . $e->getMessage();
            $this->writeToLog($message);
            $this->load->model('extension/payment/paysonCheckout2');
            //$status = $this->session->data['status'];
            //$this->model_payment_paysonCheckout2;
                    
            //echo '<pre>';print_r($this->model_payment_paysonCheckout2->config);echo '</pre>';exit;
            //$class = new ModelPaymentPaysonCheckout2();
            
            //return NULL;
           // $this->paysonApiError('ERROR');
        }
    }

    //Returns from Payson after the transaction has ended.
    public function returnFromPayson() {

        require_once 'paysonEmbedded/paysonapi.php';
        $this->load->model('checkout/order');
        $this->load->language('extension/payment/paysonCheckout2');
        
        if(isset($_GET['address_data']) && $_GET['address_data'] != NULL){
            $payment_address_payson = json_decode($_GET['address_data'], true); 
            
            $country_info = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE `iso_code_2` = '" . $this->db->escape($payment_address_payson['CountryCode']) . "' AND `status` = '1' LIMIT 1")->row;
            
            
            $this->session->data['payment_address']['firstname'] = $payment_address_payson['FirstName'];
            $this->session->data['payment_address']['lastname'] = $payment_address_payson['LastName'];
            $this->session->data['payment_address']['address_1'] = $payment_address_payson['Street'];
            $this->session->data['payment_address']['city'] = $payment_address_payson['City'];
            $this->session->data['payment_address']['postcode'] = $payment_address_payson['PostalCode'];              
            $this->session->data['payment_address']['country'] = $country_info['name'];
            $this->session->data['payment_address']['country_id'] = $country_info['country_id'];
            $this->session->data['payment_address']['iso_code_2'] = $country_info['iso_code_2'];
        
            $this->session->data['shipping_address']['firstname'] = $payment_address_payson['FirstName'];
            $this->session->data['shipping_address']['lastname'] = $payment_address_payson['LastName'];
            $this->session->data['shipping_address']['address_1'] = $payment_address_payson['Street'];
            $this->session->data['shipping_address']['city'] = $payment_address_payson['City'];
            $this->session->data['shipping_address']['postcode'] = $payment_address_payson['PostalCode'];        
            $this->session->data['shipping_address']['country'] = $country_info['name'];
            $this->session->data['shipping_address']['country_id'] = $country_info['country_id'];
            $this->session->data['shipping_address']['iso_code_2'] = $country_info['iso_code_2'];       
                        
            $this->response->redirect($this->url->link('checkout/checkout'));
        } 
       //error_log(print_r($this->getAPIInstanceMultiShop(),true));
        
        $callPaysonApi = $this->getAPIInstanceMultiShop();
        try {
            //Check if the checkoutid exist in the database.
            //print_r($this->session->data['order_id']);exit;
            if (isset($this->request->get['order_id'])) {
                $orderId = $this->request->get['order_id'];
                $checkoutObj = $callPaysonApi->GetCheckout($this->getCheckoutIdPayson($orderId));

                //This row update database with info from the return object.
                $this->updatePaymentResponseDatabase($checkoutObj, $this->getCheckoutIdPayson($orderId), 'returnCall');
                //Create the order order

                $this->handlePaymentDetails($checkoutObj, $orderId, 'returnCall');
            } else {
                $this->writeToLog('orderid: ' . isset($this->request->get['order_id']) ? $this->request->get['order_id'] : $this->session->data['order_id']);
                $this->response->redirect($this->url->link('checkout/checkout'));
            }
        } catch (Exception $e) {
            $message = '<Payson OpenCart Checkout 2.0 - Return-Exception> ' . $e->getMessage();
            $this->writeToLog($message);
        }
    }

    function validation(){
        if(!$this->config->get('paysonCheckout2_out_of_stock'))
        {
            http_response_code(200);
            exit;
        }else{

            if (isset($this->request->get['order_id'])) 
            {
                $order_id_temp = $this->request->get['order_id'];
            } else 
            {
                http_response_code(303);exit;
            }

            $compare_product_quantity = $this->db->query("SELECT " . DB_PREFIX . "order_product.product_id as id, " . DB_PREFIX . "order_product.quantity as o_quantity, " . DB_PREFIX . "product.quantity as p_quantity FROM "
           . "" . DB_PREFIX . "order_product INNER JOIN " . DB_PREFIX . "product ON " . DB_PREFIX . "order_product.product_id = " . DB_PREFIX . "product.product_id WHERE " . DB_PREFIX . "order_product.order_id = '" . (int) $order_id_temp . "'");
            
            foreach ($compare_product_quantity->rows as $product_quantity) 
            {
                if ($product_quantity['p_quantity'] >= $product_quantity['o_quantity'])
                {
                    http_response_code(200);
                }else 
                {
                    $this->writeToLog("One or more products are not in stock before payment is made. ProductId: ". $product_quantity['id']);
                    http_response_code(303);exit;
                }
            }

        }

    }

    function paysonIpn() {
        // Give time to return-url
        sleep(10);
        require_once 'paysonEmbedded/paysonapi.php';
        $this->load->model('checkout/order');
        $this->load->language('extension/payment/paysonCheckout2');
        
        $callPaysonApi = $this->getAPIInstanceMultiShop();
        try {
            
                //Check if the checkoutid exist in the database.
                if (isset($this->request->get['checkout'])) {
                    $checkoutID = $this->request->get['checkout'];
                    $checkoutObj = $callPaysonApi->GetCheckout($checkoutID);
                    //This row update database with info from the return object.
                    $this->updatePaymentResponseDatabase($checkoutObj , $checkoutID, 'ipnCall');
                    //Create, canceled or dinaid the order.
                    $this->handlePaymentDetails($checkoutObj, $this->request->get['order_id'], 'ipnCall');
                }
         
        } catch (Exception $e) {
            $message = '<Payson OpenCart Checkout 2.0 - IPN-Exception> ' . $e->getMessage();
            $this->writeToLog($message);
        }
    }

    /**
     * 
     * @param PaymentDetails $paymentDetails
     */
    private function handlePaymentDetails($paymentResponsObject, $orderId = 0, $ReturnCallUrl = Null) {
        $this->load->language('extension/payment/paysonCheckout2');
        $this->load->model('checkout/order');

        $orderIdTemp = $orderId ? $orderId : $this->session->data['order_id'];

        $paymentStatus = $paymentResponsObject->status;
        $paymentCheckoutId = $paymentResponsObject->id;

        $order_info = $this->model_checkout_order->getOrder($orderIdTemp);
        if (!$order_info) {
            return false;
        }

        $succesfullStatus = null;

        switch ($paymentStatus) {
            case "readyToShip":
                $totals_payson = round($paymentResponsObject->payData->totalPriceIncludingTax);
                $totals_opencart = round($this->currency->format($order_info['total'] * 100, $order_info['currency_code'], $order_info['currency_value'], false) / 100);

                $succesfullStatus = $this->config->get('paysonCheckout2_order_status_id');
                $comment = "";
                if(($totals_opencart + 1 < $totals_payson) || ($totals_opencart - 1 > $totals_payson)){
                    $comment .= "OBS! The price does not match, please check the value of the order. Checkout ID: " . $paymentCheckoutId . "\n\n";
                }
                
                if($this->testMode){
                    $comment .= "Checkout ID: " . $paymentCheckoutId . "\n\n";
                    $comment .= "Payson status: " . $paymentStatus . "\n\n";
                    $comment .= "Paid Order: " . $orderIdTemp;
                    $this->testMode ? $comment .= "\n\nPayment mode: " . 'TEST MODE' : '';
                }
                
                $this->db->query("UPDATE `" . DB_PREFIX . "order` SET
                                firstname  = '" . $this->db->escape($paymentResponsObject->customer->firstName) . "',
                                lastname   = '" . $this->db->escape($paymentResponsObject->customer->lastName) . "',
                                telephone  = '" . (isset($paymentResponsObject->customer->phone) ? $this->db->escape($paymentResponsObject->customer->phone) :'')."',
                                email      = '" . $this->db->escape($paymentResponsObject->customer->email) . "',
                                
                                payment_firstname  = '" . $this->db->escape($paymentResponsObject->customer->firstName) . "',
                                payment_lastname   = '" . $this->db->escape($paymentResponsObject->customer->lastName) . "',
                                payment_address_1  = '" . $this->db->escape($paymentResponsObject->customer->street) . "',
                                payment_city       = '" . $this->db->escape($paymentResponsObject->customer->city) . "', 
                                payment_country    = '" . $this->db->escape($paymentResponsObject->customer->countryCode) . "', 
                                payment_postcode   = '" . $this->db->escape($paymentResponsObject->customer->postalCode) . "',
                                
                                shipping_firstname  = '" . $this->db->escape($paymentResponsObject->customer->firstName) . "',
                                shipping_lastname   = '" . $this->db->escape($paymentResponsObject->customer->lastName) . "',
                                shipping_address_1  = '" . $this->db->escape($paymentResponsObject->customer->street) . "',
                                shipping_city       = '" . $this->db->escape($paymentResponsObject->customer->city) . "', 
                                shipping_country    = '" . $this->db->escape($paymentResponsObject->customer->countryCode) . "', 
                                shipping_postcode   = '" . $this->db->escape($paymentResponsObject->customer->postalCode) . "',
                                
                                payment_code        = 'paysonCheckout2'
                                WHERE order_id      = '" . (int) $orderIdTemp . "'");
                
                if ($this->config->get('paysonCheckout2_logg') == 1) {
                    $this->writeArrayToLog($comment);
                }

                $this->model_checkout_order->addOrderHistory($orderIdTemp, $succesfullStatus, $comment, false, true);
                $showReceiptPage = $this->config->get('paysonCheckout2_receipt');

                if ($showReceiptPage == 1) {
                    $this->unsetData($orderIdTemp);
                    $this->response->redirect($this->url->link('extension/payment/paysonCheckout2/index', 'snippet=' . $paymentResponsObject->snippet));
                } else {
                    $this->response->redirect($this->url->link('checkout/success'));
                }
                break;
            case "readyToPay":
                if ($paymentResponsObject->id != Null) {
                    $this->response->redirect($this->url->link('extension/payment/paysonCheckout2/index', 'snippet=' . $paymentResponsObject->snippet));
                }
                break;
            case "denied":
                $this->paysonApiError($this->language->get('text_denied'));
                $this->updatePaymentResponseDatabase($paymentResponsObject, $orderId, $ReturnCallUrl);
                $this->response->redirect($this->url->link('checkout/cart'));
                break;
            case "canceled":
                $this->updatePaymentResponseDatabase($paymentResponsObject, $orderId, $ReturnCallUrl);
                $this->response->redirect($this->url->link('checkout/cart'));
                break;
            case "Expired":
                $this->writeToLog('Order was Expired by payson.&#10;Checkout status:&#9;&#9;' . $paymentStatus . '&#10;Checkout id:&#9;&#9;&#9;&#9;' . $paymentCheckoutId, $paymentResponsObject);
                return false;
                break;
            default:
                $this->response->redirect($this->url->link('checkout/cart'));
        }
    }

    private function getCredentials() {
        $storesInShop = $this->db->query("SELECT store_id FROM `" . DB_PREFIX . "store`");

        $numberOfStores = $storesInShop->rows;

        $keys = array_keys($numberOfStores);
        //Since the store table do not contain the fist storeID this must be entered manualy in the $shopArray below
        $shopArray = array(0 => 0);
        for ($i = 0; $i < count($numberOfStores); $i++) {

            foreach ($numberOfStores[$keys[$i]] as $value) {
                array_push($shopArray, $value);
            }
        }
        return $shopArray;
    }

    private function getAPIInstanceMultiShop() {
        require_once 'paysonEmbedded/paysonapi.php';
        /* Every interaction with Payson goes through the PaysonApi object which you set up as follows.  
         * For the use of our test or live environment use one following parameters:
         * TRUE: Use test environment, FALSE: use live environment */
        if (!$this->testMode) {
            $merchant = explode('##', $this->config->get('paysonCheckout2_merchant_id'));
            $key = explode('##', $this->config->get('paysonCheckout2_api_key'));
            $storeID = $this->config->get('config_store_id');

            $shopArray = $this->getCredentials();
            $multiStore = array_search($storeID, $shopArray);

            $merchant_id = $merchant[$multiStore];
            $api_key = $key[$multiStore];
            return new PaysonEmbedded\PaysonApi($merchant_id, $api_key, FALSE);
        } else {
            return new PaysonEmbedded\PaysonApi('4', '2acab30d-fe50-426f-90d7-8c60a7eb31d4', TRUE);
        }
    }

    private function getOrderItems($payData) {
        require_once 'paysonEmbedded/orderitem.php';

        $this->load->language('extension/payment/paysonCheckout2');

        $orderId = $this->session->data['order_id'];

        $order_data = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $query = "SELECT `order_product_id`, `name`, `model`, `price`, `quantity`, `tax` / `price` as 'tax_rate' FROM `" . DB_PREFIX . "order_product` WHERE `order_id` = " . (int) $orderId . " UNION ALL SELECT 0, '" . $this->db->escape($this->language->get('text_gift_card')) . "', `code`, `amount`, '1', 0.00 FROM `" . DB_PREFIX . "order_voucher` WHERE `order_id` = " . (int) $orderId;
        $product_query = $this->db->query($query)->rows;

        foreach ($product_query as $product) {

            $productOptions = $this->db->query("SELECT name, value FROM " . DB_PREFIX . 'order_option WHERE order_id = ' . (int) $orderId . ' AND order_product_id=' . (int) $product['order_product_id'])->rows;
            $optionsArray = array();
            if ($productOptions) {
                foreach ($productOptions as $option) {
                    $optionsArray[] = $option['name'] . ': ' . $option['value'];
                }
            }

            $productTitle = $product['name'];

            if (!empty($optionsArray))
                $productTitle .= ' | ' . join('; ', $optionsArray);

            $productTitle = (strlen($productTitle) > 180 ? substr($productTitle, 0, strpos($productTitle, ' ', 180)) : $productTitle);
            $product_price = $this->currency->format(($product['price'] + ($product['price'] * $product['tax_rate'])), $order_data['currency_code'], $order_data['currency_value'], false);

            $payData->AddOrderItem(new PaysonEmbedded\OrderItem(html_entity_decode($productTitle, ENT_QUOTES, 'UTF-8'), $product_price, $product['quantity'], $product['tax_rate'], $product['model']));
        }

        $orderTotals = $this->getOrderTotals();
//error_log(print_r($orderTotals, true));
       
        foreach ($orderTotals as $orderTotal) {
            $orderTotalType = PaysonEmbedded\OrderItemType::SERVICE;

            $orderTotalAmountTemp = 0;
            
            if($orderTotal['sort_order'] >= $this->config->get('tax_sort_order')){
              $orderTotalAmountTemp = $orderTotal['value'];  
            }else{
                $orderTotalAmountTemp = $orderTotal['value'] * (1 + ($orderTotal['lpa_tax'] > 0 ? $orderTotal['lpa_tax'] / 100 : 0));
            }
            
            $orderTotalAmount = $this->currency->format($orderTotalAmountTemp, $order_data['currency_code'], $order_data['currency_value'], false) ;
			
            if ($orderTotalAmount == null || $orderTotalAmount == 0) {
                continue;
            }

            //$orderTotalTemp = new PaysonEmbedded\OrderItem(html_entity_decode($orderTotal['title'], ENT_QUOTES, 'UTF-8'), $orderTotalAmount * (1 + (VERSION >= 2.2 ? $orderTotal['lpa_tax'] : $orderTotal['tax_rate']) / 100), 1, (VERSION >= 2.2 ? $orderTotal['lpa_tax'] : $orderTotal['tax_rate']) / 100);
            // $payData->AddOrderItem(new  PaysonEmbedded\OrderItem(html_entity_decode($orderTotal['title'], ENT_QUOTES, 'UTF-8'), $orderTotalAmount * (1 + (VERSION >= 2.2 ? $orderTotal['lpa_tax'] : $orderTotal['tax_rate']) / 100), 1, (VERSION >= 2.2 ? $orderTotal['lpa_tax'] : $orderTotal['tax_rate']) / 100));

            if ($orderTotal['code'] == 'coupon') {
                $orderTotalType = PaysonEmbedded\OrderItemType::DISCOUNT;
            }

            if ($orderTotal['code'] == 'voucher') {
                $orderTotalType = PaysonEmbedded\OrderItemType::DISCOUNT;
            }

            if ($orderTotal['code'] == 'shipping') {
                $orderTotalType = PaysonEmbedded\OrderItemType::SERVICE;
            }

            if($orderTotalAmount < 0) {
                $orderTotalType = PaysonEmbedded\OrderItemType::DISCOUNT;
            }  

            $payData->AddOrderItem(new PaysonEmbedded\OrderItem(html_entity_decode($orderTotal['title'], ENT_QUOTES, 'UTF-8'), $orderTotalAmount, 1, ($orderTotal['lpa_tax']) / 100, $orderTotal['code'], $orderTotalType));
        }
        if ($this->config->get('paysonCheckout2_logg') == 1) {
            $this->writeArrayToLog($payData->toJson(), 'Items list: ');
        }
     
    }

    
    
   
    
    
    private function getOrderTotals() {
        // Totals
        $this->load->model('extension/extension');
        $totals = array();
        $taxes = $this->cart->getTaxes();
        $total = 0;

        // Because __call can not keep var references so we put them into an array.
        $total_data = array(
            'totals' => &$totals,
            'taxes' => &$taxes,
            'total' => &$total
        );

        $old_taxes = $taxes;
        $lpa_tax = array();

        $sort_order = array();

        $results = $this->model_extension_extension->getExtensions('total');

        foreach ($results as $key => $value) {
                $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
        }

        array_multisort($sort_order, SORT_ASC, $results);

        foreach ($results as $result) {
            if (isset($result['code'])) {
                $code = $result['code'];
            } else {
                $code = $result['key'];
            }

            if ($this->config->get($code . '_status')) {
                $this->load->model('extension/total/' . $code);

                // We have to put the totals in an array so that they pass by reference.
                $this->{'model_extension_total_' . $code}->getTotal($total_data);

                if (!empty($totals[count($totals) - 1]) && !isset($totals[count($totals) - 1]['code'])) {
                    $totals[count($totals) - 1]['code'] = $code;
                }

                $tax_difference = 0;

                foreach ($taxes as $tax_id => $value) {
                    if (isset($old_taxes[$tax_id])) {
                        $tax_difference += $value - $old_taxes[$tax_id];
                    } else {
                        $tax_difference += $value;
                    }
                }

                if ($tax_difference != 0) {
                    $lpa_tax[$code] = $tax_difference;
                }

                $old_taxes = $taxes;
            }
        }

        $sort_order = array();

        foreach ($totals as $key => $value) {
            $sort_order[$key] = $value['sort_order'];

            if (isset($lpa_tax[$value['code']])) {
                $total_data['totals'][$key]['lpa_tax'] = abs($lpa_tax[$value['code']] / $value['value'] * 100);
            } else {
                $total_data['totals'][$key]['lpa_tax'] = 0;
            }
        }

        $ignoredTotals = $this->config->get('paysonCheckout2_ignored_order_totals');
        if ($ignoredTotals == null)
            $ignoredTotals = 'sub_total, total, tax';

        $ignoredOrderTotals = array_map('trim', explode(',', $ignoredTotals));
        foreach ($totals as $key => $orderTotal) {
            if (in_array($orderTotal['code'], $ignoredOrderTotals)) {
                unset($totals[$key]);
            }
        }

        return $totals;
    }
    

    /** 
     * @param PaymentDetails $paymentDetails
     * @param checkout_id int $id
     */
    private function updatePaymentResponseDatabase($paymentDetails, $id, $call = 'returnCall') {
        $this->db->query("UPDATE `" . DB_PREFIX . "payson_embedded_order` SET 
                        payment_status  = '" . $this->db->escape($paymentDetails->status) . "',
                        updated                       = NOW(), 
                        sender_email                  = 'sender_email', 
                        currency_code                 = 'currency_code',
                        tracking_id                   = 'tracking_id',
                        type                          = 'type',
                        shippingAddress_name          = '" . $this->db->escape(str_replace( array( '\'', '"', ',' , ';', '<', '>', '&' ), ' ', $paymentDetails->customer->firstName))  . "',
                        shippingAddress_lastname      = '" . $this->db->escape($paymentDetails->customer->lastName) . "', 
                        shippingAddress_street_ddress = '" . $this->db->escape(str_replace( array( '\'', '"', ',' , ';', '<', '>', '&' ), ' ', $paymentDetails->customer->street)) . "',
                        shippingAddress_postal_code   = '" . $this->db->escape($paymentDetails->customer->postalCode) . "',
                        shippingAddress_city          = '" . $this->db->escape($paymentDetails->customer->city) . "', 
                        shippingAddress_country       = '" . $this->db->escape($paymentDetails->customer->countryCode) . "'
			            WHERE  checkout_id            = '" . $this->db->escape($id) . "'"
        );
    }

    private function storePaymentResponseDatabase($checkoutId, $orderId) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "payson_embedded_order SET 
                            payson_embedded_id  = '',
                            order_id            = '" . (int) $orderId . "', 
                            checkout_id         = '" . $this->db->escape($checkoutId) . "', 
                            purchase_id         = '" . $this->db->escape($checkoutId) . "',
                            payment_status      = 'created', 
                            added               = NOW(), 
                            updated             = NOW()"
        );
    }

    private function getCheckoutIdPayson($order_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "payson_embedded_order` WHERE order_id = '" . (int) $order_id . "' ORDER BY `added` DESC");
        if ($query->num_rows && $query->row['checkout_id']) {
            if ($query->row['payment_status'] == ('created' || 'readyToPay')) {

                return $query->row['checkout_id'];
            } else {
                return null;
            }
        }
    }

    private function getPaysonEmbeddedOrder($order_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "payson_embedded_order` WHERE order_id = '" . (int) $order_id . "' ORDER BY `added` DESC");
        if($query->num_rows){
           return $query->row;
        } else {
            return null;
        } 
    }

    private function unsetData($order_id) {

        $this->cart->clear();

        // Add to activity log
        $this->load->model('account/activity');

        if ($this->customer->isLogged()) {
            $activity_data = array(
                'customer_id' => $this->customer->getId(),
                'name' => $this->customer->getFirstName() . ' ' . $this->customer->getLastName(),
                'order_id' => $order_id
            );

            $this->model_account_activity->addActivity('order_account', $activity_data);
        }
//			else {
//				$activity_data = array(
//					'name'     => $this->session->data['guest']['firstname'] . ' ' . $this->session->data['guest']['lastname'],
//					'order_id' => $order_id
//				);
//
//				$this->model_account_activity->addActivity('order_guest', $activity_data);
//			}

        unset($this->session->data['shipping_method']);
        unset($this->session->data['shipping_methods']);
        unset($this->session->data['payment_method']);
        unset($this->session->data['payment_methods']);
        unset($this->session->data['guest']);
        unset($this->session->data['comment']);
        unset($this->session->data['order_id']);
        unset($this->session->data['coupon']);
        unset($this->session->data['reward']);
        unset($this->session->data['voucher']);
        unset($this->session->data['vouchers']);
        unset($this->session->data['totals']);
    }

    public function languagepaysonCheckout2() {
        $language = explode("-", $this->data['language_code']);
        switch (strtoupper($language[0])) {    
            case "SE":
            case "SV":
                return "SV";
            case "FI":
                return "FI";
            case "DA":
            case "DK":
                return "DA";
            case "NB":
            case "NO":
                return "NO";
            case "CA":
            case "GL":
            case "ES":
                return "ES";
            case "DE":
                return "DE";
            default:
                return "EN";
        }
    }

    public function currencypaysonCheckout2() {
        switch (strtoupper($this->data['currency_code'])) {
            case "SEK":
                return "SEK";
            default:
                return "EUR";
        }
    }

    /**
     * 
     * @param string $message
     * @param PaymentDetails $paymentDetails
     */
    function writeToLog($message, $paymentResponsObject = False) {
        $paymentDetailsFormat = "Payson reference:&#9;%s&#10;Correlation id:&#9;%s&#10;";
        if ($this->config->get('paysonCheckout2_logg') == 1) {

            $this->log->write('PAYSON CHECKOUT 2.0&#10;' . $message . '&#10;' . ($paymentResponsObject != false ? sprintf($paymentDetailsFormat, $paymentResponsObject->status, $paymentResponsObject->id) : '') . $this->writeModuleInfoToLog());
        }
    }

    private function writeArrayToLog($array, $additionalInfo = "") {
        if ($this->config->get('paysonCheckout2_logg') == 1) {
            $this->log->write('PAYSON CHECKOUT 2.0&#10;Additional information:&#9;' . $additionalInfo . '&#10;&#10;' . print_r($array, true) . '&#10;' . $this->writeModuleInfoToLog());
        }
    }

    private function writeModuleInfoToLog() {
        return 'Module version: ' . $this->config->get('paysonCheckout2_modul_version') . '&#10;------------------------------------------------------------------------&#10;';
    }

    private function writeTextToLog($additionalInfo = "") {
        $module_version = 'Module version: ' . $this->config->get('paysonCheckout2_modul_version') . '&#10;------------------------------------------------------------------------&#10;';
        $this->log->write('PAYSON CHECKOUT 2.0' . $additionalInfo . '&#10;&#10;'.$module_version);
    }

    public function paysonComments(){
        $this->load->model('checkout/order');
        if(isset($this->request->get['payson_comments']) && !empty($this->request->get['payson_comments'])){
            $p_comments = $this->request->get['payson_comments'];
            if(is_string($p_comments)){
                $this->session->data['comment'] = $p_comments;
                $this->db->query("UPDATE `" . DB_PREFIX . "order` SET 
                comment  = '" . $this->db->escape(nl2br($p_comments)) . "'
                WHERE order_id      = '" . (int) $this->session->data['order_id'] . "'");  
            }
        }
    }

    public function paysonApiError($error) {
        $this->load->language('extension/payment/paysonCheckout2');
        $error_code = '<html>
                            <head>
                                <script type="text/javascript"> 
                                    alert("' . $error . $this->language->get('text_payson_payment_method') . '");
                                    window.location="' . (HTTPS_SERVER . 'index.php?route=checkout/cart') . '";
                                </script>
                            </head>
                    </html>';
        echo ($error_code);
        exit;
    }

    public function notifyStatusToPayson($route, &$data){
        $getCheckoutObject = $this->getPaysonEmbeddedOrder($data[0]);
        if(isset($getCheckoutObject['checkout_id']) && ($getCheckoutObject['payment_status'] == 'readyToShip' || $getCheckoutObject['payment_status'] == 'shipped' || $getCheckoutObject['payment_status'] == 'paidToAccount'))
        {
            try
            {
                $additionalInfo = '';
                $callPaysonApi = $this->getAPIInstanceMultiShop();
                $checkout = $callPaysonApi->GetCheckout($getCheckoutObject['checkout_id']);
                //paysonCheckout2_request_phone
                if($data[1] == $this->config->get('paysonCheckout2_order_status_shipped_id')) 
                { 
                    $checkout = $callPaysonApi->ShipCheckout($checkout);
                }
                elseif($data[1] == $this->config->get('paysonCheckout2_order_status_canceled_id')) 
                {
                    $checkout = $callPaysonApi->CancelCheckout($checkout);
                }
                elseif($data[1] == $this->config->get('paysonCheckout2_order_status_refunded_id')) 
                {
                    if ($checkout->status == 'readyToShip' || $checkout->status == 'shipped' || $checkout->status == 'paidToAccount') 
                    {
                        if ($checkout->status == 'readyToShip') 
                        {
                            $checkout = $callPaysonApi->ShipCheckout($checkout);
                        }
                        foreach ($checkout->payData->items as $item) 
                        {
                            $item->creditedAmount = ($item->totalPriceIncludingTax);
                        }
                        unset($item);
                        $checkout2 = $callPaysonApi->UpdateCheckout($checkout);
                        $checkout = $callPaysonApi->GetCheckout($checkout2->id);
                    }
                }
                else
                {
                    // Do nothing
                }
                $additionalInfo = '&#10;Notification is sent on and the order has been: &#9;'. $checkout->status. '&#10;&#10; Order: ' . $data[0]. '&#10;&#10; checkout: ' . $checkout->id . '&#10;&#10; Payson-ref: '. $checkout->purchaseId;
                $this->writeTextToLog($additionalInfo);
            } 
            catch (Exception $e) 
            {
                $message = '<Payson OpenCart Checkout 2.0 -  - Payson Order Status> &#10;' . $e->getMessage() . '&#10;'.  $e->getCode();
                $this->writeToLog($message);
            }
        }
        else
        {
            //Do nothing
        }
    } 

}

?>