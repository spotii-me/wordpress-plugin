<?php

/**
 * Plugin Name: Spotii Payment Gateway
 * Plugin URI: https://spotii.me/
 * Description: A buy-now-pay-later payment platform for WooCommerce
 * Version: 0.1.1
 * Author: Spotii
 * Author URI: https://spotii.me
 * Text Domain: spotii
 * Domain Path: /languages/
 *
 * @package Spotii
 */


/*
 * Register our PHP class as a WooCommerce payment gateway
 */

require __DIR__ . '/src/cart-widget.php';
require __DIR__ . '/src/product-widget.php';

add_filter('woocommerce_payment_gateways', 'spotii_add_gateway_class');
function spotii_add_gateway_class($gateways)
{
    $gateways[] = 'WC_Spotii_Gateway';
    return $gateways;
}

/*
 * Load Spotii Gateway class on plugins_loaded action
 */
add_action('plugins_loaded', 'spotii_init_gateway_class');

function spotii_init_gateway_class()
{
    if (class_exists('WC_Spotii_Gateway') || !class_exists('WC_Payment_Gateway')) return;
    
    // ADD WIDGETS, ENQUEUE NEEDED CSS AND JS
    add_action('woocommerce_proceed_to_checkout', 'add_cart_widget');
    add_action( 'woocommerce_single_product_summary', 'add_product_widget' );
    // Enqueue styles and scripts 

    function spotiiEnqueue() {
        global $wp_scripts;
        $wp_scripts->registered['jquery-core']->src = 'https://code.jquery.com/jquery-3.5.1.min.js';
        $wp_scripts->registered['jquery']->deps = ['jquery-core'];
       
        wp_enqueue_style('spotii-gateway', plugins_url('/spotii-gateway/assets/css/spotii-checkout.css', dirname(__FILE__)), array(), true);
        wp_enqueue_script( 'spotii-widget', plugins_url('spotii-gateway/assets/js/spotii-product-widget.js', dirname(__FILE__)), false, null);
        if (is_checkout() ) {
            // fancybox 
            wp_enqueue_style( 'spotii-fancybox', esc_url_raw( 'https://widget.spotii.me/v1/javascript/fancybox-2.0.min.css' ), array(), true );
            wp_enqueue_script( 'spotii-fancybox', esc_url_raw( 'https://widget.spotii.me/v1/javascript/fancybox-2.0.min.js' ), array('jquery'), '2.0', true  );
        }
        // spotii checkout 
        wp_enqueue_script( 'spotii-checkout', plugin_dir_url( __FILE__ ) . 'assets/js/spotii-checkout.js', array('jquery'), '2.0', true );
        if (is_checkout() ) {
            // over ride woo commerce checkout js 
            wp_deregister_script('wc-checkout');
            wp_enqueue_script('wc-checkout', plugin_dir_url( __FILE__ ) . 'assets/js/woocommerce-checkout.js', array('jquery'), '2.0', true);
        }
        wp_enqueue_script('jquery');
        wp_localize_script( 'jquery', 'spotii_ajax', array('ajax_url' => admin_url( 'admin-ajax.php' )));
        
    }


    add_action( 'wp_enqueue_scripts', 'spotiiEnqueue', 12 );


    class WC_Spotii_Gateway extends WC_Payment_Gateway
    {

        public function __construct()
        {
            add_action('woocommerce_api_wc_gateway_spotii', array($this, 'spotii_response_handler'));

            $this->id = 'spotii';
            $this->icon = 'https://spotii.me/img/logo.svg';

            $this->method_title = 'Spotii Gateway';
            $this->method_description = 'Have your customers pay over time for their purchases. No hidden fees, no interest.';

            // Options supported by Spotii payment gateway
            $this->supports = array(
                'products',
                'refunds'
            );

            // Initialize fields in admin panel
            $this->init_form_fields();

            // Load settings
            $this->init_settings();
            $this->title = $this->get_option('title', 'Spotii');
            $this->description = $this->get_option('description', 'Buy now. Pay later');
            $this->enabled = $this->get_option('enabled', 'yes');
            $this->testmode = false;
            $this->testmode = 'yes' === $this->get_option('testmode', 'yes');
            $this->public_key = $this->get_option('public_key', '');
            $this->private_key = $this->get_option('private_key', '');
            $this->test_public_key = $this->get_option('test_public_key', '');
            $this->test_private_key = $this->get_option('test_private_key', '');
            
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

            $auth_url = $this->testmode ? 'https://auth.dev.spotii.me/api/v1.0/merchant/authentication'
                : 'https://auth.spotii.me/api/v1.0/merchant/authentication/';

            $headers = array(
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            );

            $body = array(
                'public_key' => $this->testmode ? $this->test_public_key : $this->public_key,
                'private_key' => $this->testmode ? $this->test_private_key : $this->private_key
            );

            $payload = array(
                'method' => 'POST',
                'headers' => $headers,
                'body' => wp_json_encode($body),
                'timeout' => 20
            );

            $response = wp_remote_post($auth_url, $payload);

            if (is_wp_error($response)) {
                error_log("Exception [WP_Error_Spotii Authentication]: " . $response);
                throw new Exception(__('Network connection issue'));
            }
            if (empty($response['body'])) {
                error_log("Response Body Empty [WP_Error_Spotii Authentication]: " . $response);
                throw new Exception(__('Empty response body'));
            }

            $response_body = $response['body'];
            $response_body_arr = json_decode($response_body, true);

            if (array_key_exists('token', $response_body_arr)) {
                $this->token = $response_body_arr['token'];
            } else {
                error_log("Error on authentication: " . $response_body);
            }
        }

        /**
         * Define fields and labels in Admin Panel
         */
        public function init_form_fields()
        {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => 'Enable/Disable',
                    'label' => 'Enable Spotii Gateway',
                    'type' => 'checkbox',
                    'description' => __('Don&rsquo;t have a Spotii Merchant account yet?', 'woocommerce') . ' ' . '<a href="https://dashboard.dev.spotii.me/signup" target="_blank">' . __('Apply online today!', 'woocommerce') . '</a>',
                    'default' => 'no',
                ),
                'title' => array(
                    'title' => 'Title',
                    'type' => 'text',
                    'description' => 'This controls the title that the user sees during checkout',
                    'default' => 'Spotii',
                    'desc_tip' => true,
                ),
                'description' => array(
                    'title' => 'Description',
                    'type' => 'textarea',
                    'description' => 'This controls the description which the user sees during checkout',
                    'default' => 'Shop Now. Pay Later',
                ),
                'testmode' => array(
                    'title' => 'Test mode',
                    'label' => 'Enable Test Mode',
                    'type' => 'checkbox',
                    'description' => 'Place the payment gateway in test mode using test API keys',
                    'default' => 'yes',
                    'desc_tip' => true,
                ),
                'test_public_key' => array(
                    'title' => 'Test Public Key',
                    'type' => 'text',
                ),
                'test_private_key' => array(
                    'title' => 'Test Private Key',
                    'type' => 'password',
                ),
                'public_key' => array(
                    'title' => 'Live Public Key',
                    'type' => 'text',
                ),
                'private_key' => array(
                    'title' => 'Live Private Key',
                    'type' => 'password',
                )
            );
        }

        /**
         * Get icon for Spotii option on checkout page
         */
        public function get_icon()
        {
            $icon = $this->icon ? '<img src="' . WC_HTTPS::force_https_url($this->icon) . '" alt="' . esc_attr($this->get_title()) . '" class="spotii-checkout-img" />' : '';
            return apply_filters('woocommerce_gateway_icon', $icon, $this->id);
        }

        /*
         * Get description text for Spotii option on checkout page
         */
        public function payment_fields()
        {
            //            if ($this->description) echo wpautop(wptexturize($this->description));

            $total = WC()->cart->total;
            $instalment = wc_price($total / 4);
            $time = ['Today', 'Second payment', 'Third payment', 'Fourth payment'];

            echo '
                <div class="spotii-cover" id="cover">
                    <span class="spotii-payment-text">Payment Schedule</span>
                    <div class="spotii-progressbar-container">
                        <div class="spotii-bar"></div>
                        <ul class="spotii-steps">
                             <span class="spotii-highlight">
                             <span class="spotii-installment-amount">' . $instalment . '</span>
                             <span class="spotii-time-period">' . $time[0] . '</span>
                             </span>
                             <span class="spotii-step">
                             <span class="spotii-installment-amount">' . $instalment . '</span>
                             <span class="spotii-time-period">' . $time[1] . '</span>
                             </span>
                             <span class="spotii-step">
                             <span class="spotii-installment-amount">' . $instalment . '</span>
                             <span class="spotii-time-period">' . $time[2] . '</span>
                             </span>
                             <span class="spotii-step">
                             <span class="spotii-installment-amount">' . $instalment . '</span>
                             <span class="spotii-time-period">' . $time[3] . '</span>
                             </span>
                        </ul>
                    </div>
                    <span class="spotii-grand-total">Total : ' . wc_price($total) . ' </span>
                </div>
                ';
        }

        /*
        * Override field validation on checkout page
        */
        public function validate_fields()
        {
        }

        public function validate_curr($curr){
            if($curr == "AED" || $curr == "SAR" || $curr == "USD" ){
                return true;
            }else{
                return false;
            }
        }
        /*
         * Process payments: magic begins here
         */
        public function process_payment($order_id)
        {
            $order = new WC_Order($order_id);
           
            $orderId = $order_id;
            $curr =$order->get_currency();
            if(!$this->validate_curr($curr)){
                error_log("Exception [WP_Error_Spotii Process Payment] Currency is not supported by Spotii: " . $curr);
                wc_add_notice(__('Checkout Error: ', 'woothemes') . "Currency is not supported by Spotii", 'error');
                throw new Exception(__('Currency is not supported by Spotii'));
            }
            try {
                $url = $this->testmode ? 'https://api.dev.spotii.me/api/v1.0/checkouts/'
                    : 'https://api.spotii.me/api/v1.0/checkouts/';
                $payload = $this->get_checkout_payload($order);
                $response = wp_remote_post($url, $payload);

                if (is_wp_error($response)) {
                    error_log("Exception [WP_Error_Spotii Process Payment]: " . $response);
                    throw new Exception(__('Network connection issue'));
                }
                if (empty($response['body'])) {
                    error_log("Exception [Response Body Empty]: " . $response);
                    throw new Exception(__('Empty response body'));
                }
                $response_body = $response['body'];
                $response_body_arr = json_decode($response_body, true);
         
                if (array_key_exists('checkout_url', $response_body_arr)) {
                    $redirect_url = $response_body_arr['checkout_url'];
                    $curr= $response_body_arr['currency'];
                    $total= $response_body_arr['total'];
                    return array('result' => 'success', 'redirect' => "", "checkout_url" => $redirect_url, "orderId" => $orderId, "total" => $total, "curr" => $curr);

                } else {
                    error_log("Error on process payment: " . $response_body);
                    $order->add_order_note('Checkout with Spotii failed: ' . $response_body);
                    $res = json_decode($response_body, true);

                    if ($res['total']) {

                        foreach ($res['total'] as $msg) {

                            if (!in_array('less than allowed', $msg)) {

                                wc_add_notice(__('Oops! ', 'woothemes') . "You don't quite have enough in your basket: Spotii is available for purchases over AED 200. With a little more shopping, you can split your payment over 4 cost-free instalments.", 'error');
                            }
                        }
                    } else
                        wc_add_notice(__('Checkout Error: ', 'woothemes') . "Please try again", 'error');
                }
            } catch (Exception $e) {
                error_log("Error on process_payment[Spotii]: " . $e->getMessage());
            }
        }

        /**
         * Helper to prepare checkout payload
         */
        private function get_checkout_payload($order)
        {
            $order_id = $order->get_id();

            $headers = $this->get_header();
            $notify_url = get_home_url(null, "?wc-api=wc_gateway_spotii");
            $body = array(
                "reference" => $order_id,
                "display_reference" => $order_id,
                "description" => "Order #" . $order_id,
                "total" => $order->get_total(),
                "currency" => $order->get_currency(),
                "confirm_callback_url" => $notify_url . "&o=" . $order_id . "&s=s",
                "reject_callback_url" => $notify_url . "&o=" . $order_id . "&s=f",

                // Order
                "order" => array(
                    "tax_amount" => $order->get_total_tax(),
                    "shipping_amount" => $order->get_shipping_total(),
                    "discount" => $order->get_total_discount(),
                    "customer" => array(
                        "first_name" => $order->get_user()->first_name,
                        "last_name" => $order->get_user()->last_name,
                        "email" => $order->get_user()->user_email,
                        "phone" => $order->get_billing_phone(),
                    ),

                    "billing_address" => array(
                        "title" => "",
                        "first_name" => $order->get_billing_first_name(),
                        "last_name" => $order->get_billing_last_name(),
                        "line1" => $order->get_billing_address_1(),
                        "line2" => $order->get_billing_address_2(),
                        "line3" => "",
                        "line4" => $order->get_billing_city(),
                        "state" => $order->get_billing_state(),
                        "postcode" => $order->get_billing_postcode(),
                        "country" => $order->get_billing_country(),
                        "phone" => $order->get_billing_phone(),
                    ),

                    "shipping_address" => array(
                        "title" => "",
                        "first_name" => $order->get_shipping_first_name(),
                        "last_name" => $order->get_shipping_last_name(),
                        "line1" => $order->get_shipping_address_1(),
                        "line2" => $order->get_shipping_address_2(),
                        "line3" => "",
                        "line4" => $order->get_shipping_city(),
                        "state" => $order->get_shipping_state(),
                        "postcode" => $order->get_shipping_postcode(),
                        "country" => $order->get_shipping_country(),
                        "phone" => $order->get_billing_phone(),
                    )
                )
            );

            foreach ($order->get_items() as $item) {
                $product = wc_get_product($item['product_id']);
                $lines[] = array(
                    "sku" => $product->get_sku(),
                    "reference" => $item->get_id(),
                    "title" => $product->get_title(),
                    "upc" => $product->get_sku(),
                    "quantity" => $item->get_quantity(),
                    "price" => $product->get_price(),
                    "currency" => $order->get_currency(),
                    "image_url" => "",       //$product->get_image(),
                );
            }
            $body['order']['lines'] = $lines;

            $payload = array(
                'method' => 'POST',
                'headers' => $headers,
                'body' => wp_json_encode($body),
                'timeout' => 20
            );

            return $payload;
        }

        /**
         * Helper to prepare authentication header
         */
        private function get_header()
        {
            $headers = array(
                'Accept' => 'application/json; indent=4',
                'Content-Type' => 'application/json',
                'Access-Control-Allow-Origin' => '*',
                'Authorization' => 'Bearer ' . $this->token
            );
            return $headers;
        }  
        public function check_amount($spotii_amount, $spotii_currency, $merchant_amount,$merchant_currency){
            try{ 
              
            if ($spotii_currency != $merchant_currency){
                 if($spotii_currency == "AED"){
                 switch($merchant_currency){
                     case "USD":
                         $merchant_amount=$merchant_amount*3.6730 ;
                     break;
                     case "SAR":
                         $merchant_amount=$merchant_amount*0.9506 ;
                     break;
                 }
              }  
                 if(abs(($spotii_amount-$merchant_amount))<6){
                     return true;
                 }
        
             }else if ($spotii_amount == $merchant_amount){
                     return true;
             }else {
                     return false;
             }
         }catch (Exception $e) {
             wc_add_notice(__('Checkout Error: ', 'woothemes') . "Amount from Spotii doesn't match amount from merchant. Please try again", 'error');
             error_log("Error on amount match " . $e->getMessage());
         }
           }

            /**
         * Process refunds
         */
        public function process_refund($order_id, $amount = null, $reason = '')
        {

            $order = wc_get_order($order_id);

            $url_part = $this->testmode ? 'https://api.dev.spotii.me/api/v1.0/orders/'
                : 'https://api.spotii.me/api/v1.0/orders/';
            $url = $url_part . $order_id . '/refund';

            $headers = $this->get_header();
            $body = array(
                "total"     => $amount,
                "currency"  => $order->get_currency(),
            );
            $payload = array(
                'method' => 'POST',
                'headers' => $headers,
                'body' => wp_json_encode($body),
                
            );

            $response = wp_remote_post($url, $payload);
            $response_body = $response['body'];
            $res = json_decode($response_body, true);

            if (is_wp_error($response)) {
                error_log('WP_ERROR [Spotii Process Refund] ');
                throw new Exception(__('Network connection issue'));
            }
            if (empty($response['body'])) {
                error_log('Response Body Empty [Spotii Process Refund] ');
                throw new Exception(__('Empty response body'));
            }
            // Check for capture success 
            if ($res['status'] == 'SUCCESS' &&  $this->check_amount(floatval($res['amount']), $res['currency'], floatval($amount), $order->get_currency())) {
                $order->add_order_note('Refund successful');
                wc_add_notice(__('Refund Success: ', 'woothemes') . "Refund complete", 'success');
                return true;
            } else {
                $order->add_order_note('Refund failed' . $response_body);
                wc_add_notice(__('Refund Error: ', 'woothemes') . "Refund with Spotii failed", 'error');

                error_log("Error on refund: " . $response_body);
                return false;
            }
        }
    }
    
    add_action('wp_footer', 'spotii_footer'); 
    function spotii_footer() { 
        echo '
        <button style="display:none" id="closeclick">set overlay closeClick to false</button>                                
        <button style="display:none" id="closeiframebtn">set overlay closeClick to false</button>
        <div class="fancy-box-container">
            <a id="fancy" style="display: none;" class="fancy-box" href="">open fancybox</a>
        </div>
        <a href="#top" style="display:none"></a>
        '; 
    }


add_action( 'wp_ajax_spotii_order_update', 'spotii_order_update');
add_action( 'wp_ajax_nopriv_spotii_order_update', 'spotii_order_update' );

function spotii_order_update() {
    
    $order_id = $_POST["order_id"];
    $order_status = $_POST["status"];
    $spotii_total = floatval($_POST["total"]);
    $spotii_curr = $_POST["curr"];

    $order = wc_get_order($order_id);
    error_log('orderstatus' . $order->get_status());

    if($order_status == "completed" && (new WC_Spotii_Gateway())->check_amount($spotii_total, $spotii_curr, floatval($order->get_total()),$order->get_currency()) ){
        try {
            $order->add_order_note('Payment successful');
            $order->payment_complete();
            $redirect_url = $order->get_checkout_order_received_url();
            // wp_redirect($redirect_url);
            error_log('redirect_url ' . $redirect_url);
            //echo json_encode(array('result' => 'success', 'redirect' => $redirect_url));
            die;
        } catch (Exception $e) {
            error_log("Error on spotii_response handler[Spotii spotii_response_handler]: " . $e->getMessage());
        }
    }else if($order_status == "canceled" || !(new WC_Spotii_Gateway())->check_amount($spotii_total, $spotii_curr, $order->get_total(),$order->get_currency())){
        // If you are here, payment was unsuccessful
        $order->add_order_note('Payment with Spotii failed');
        wc_add_notice(__('Checkout Error: ', 'woothemes') . "Payment with Spotii failed. Please try again", 'error');
        $order->update_status('failed', __('Payment with Spotii failed', 'woocommerce'));
        $redirect_url = $order->get_cancel_order_url();
        // wp_redirect($redirect_url);
        //echo json_encode(array('result' => 'error', 'redirect' => $redirect_url));
        die;
    }
}
}