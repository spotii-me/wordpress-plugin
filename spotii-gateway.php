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
    add_action( 'woocommerce_before_add_to_cart_form', 'add_product_widget' );
    function enqueue_popup_scripts() {
        wp_enqueue_script( 'spotii-gateway', plugins_url('spotii-gateway/assets/js/spotii-product-widget.js', dirname(__FILE__)));
        wp_enqueue_style('spotii-gateway', plugins_url('/spotii-gateway/assets/css/spotii-checkout.css', dirname(__FILE__)), array(), null);
    }
    add_action( 'wp_enqueue_scripts', 'enqueue_popup_scripts' );

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

            $auth_url = $this->testmode ? 'https://auth.sandbox.spotii.me/api/v1.0/merchant/authentication'
                : 'https://auth.spotii.me/api/v1.0/merchant/authentication';

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
                'body' => wp_json_encode($body)
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
                    'description' => __('Don&rsquo;t have a Spotii Merchant account yet?', 'woocommerce') . ' ' . '<a href="https://dashboard.sandbox.spotii.me/signup" target="_blank">' . __('Apply online today!', 'woocommerce') . '</a>',
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
            $icon = $this->icon ? '<img src="' . WC_HTTPS::force_https_url($this->icon) . '" alt="' . esc_attr($this->get_title()) . '" />' : '';
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
                <div class="cover" id="cover">
                    <span class="spotii-payment-text">Payment Schedule</span>
                    <div class="progressbar-container">
                        <div class="bar"></div>
                        <ul class="steps">
                             <span class="highlight">
                             <span class="spotii-installment-amount">' . $instalment . '</span>
                             <span class="time-period">' . $time[0] . '</span>
                             </span>
                             <span class="step">
                             <span class="spotii-installment-amount">' . $instalment . '</span>
                             <span class="time-period">' . $time[1] . '</span>
                             </span>
                             <span class="step">
                             <span class="spotii-installment-amount">' . $instalment . '</span>
                             <span class="time-period">' . $time[2] . '</span>
                             </span>
                             <span class="step">
                             <span class="spotii-installment-amount">' . $instalment . '</span>
                             <span class="time-period">' . $time[3] . '</span>
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

        /*
         * Process payments: magic begins here
         */
        public function process_payment($order_id)
        {
            $order = new WC_Order($order_id);
            //$order = wc_get_order($order_id);
            try {
                $url = $this->testmode ? 'https://api.sandbox.spotii.me/api/v1.0/checkouts/'
                    : 'https://api.spotii.me/api/v1.0/checkouts/';
                $payload = $this->get_checkout_payload($order);
                $response = wp_remote_post($url, $payload);
                add_action('woocommerce_api_wc_gateway_spotii', array($this, 'spotii_response_handler'));


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
                    return array('result' => 'success', 'redirect' => $redirect_url);
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
            $notify_url = get_home_url(null, "wc-api/wc_gateway_spotii");
            $body = array(
                "reference" => $order_id,
                "display_reference" => $order_id,
                "description" => "Order #" . $order_id,
                "total" => $order->get_total(),
                "currency" => $order->get_currency(),
                "confirm_callback_url" => $notify_url . "?o=" . $order_id . "&s=s",
                "reject_callback_url" => $notify_url . "?o=" . $order_id . "&s=f",

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
                'body' => wp_json_encode($body)
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

        /**
         * Called when Spotii checkout page redirects back to merchant page
         */
        public function spotii_response_handler()
        {
            $order_id = $_GET['o'];
            $order = wc_get_order($order_id);
            error_log('orderstatus' . $order->get_status());
            if ($order->has_status('completed') || $order->has_status('processing')) {

                return;
            }

            $status = $_GET['s'];
            // Check for url param success
            if ($status == 's') {
                try {
                    // Capture payment
                    $url_part = $this->testmode ? 'https://api.sandbox.spotii.me/api/v1.0/orders/'
                        : 'https://api.spotii.me/api/v1.0/orders/';
                    $url = $url_part . $order_id . '/capture/';

                    $headers = $this->get_header();
                    $payload = array(
                        'method' => 'POST',
                        'headers' => $headers,
                        'body' => '{}'
                    );
                    $response = wp_remote_post($url, $payload);
                    if (is_wp_error($response)) {
                        error_log('WP_ERROR [Spotii spotii_response_handler] ');
                        throw new Exception(__('Network connection issue'));
                    }
                    if (empty($response['body'])) {
                        error_log('Response Empty [Spotii spotii_response_handler] ');
                        throw new Exception(__('Empty response body'));
                    }
                    $response_body = $response['body'];
                    $res = json_decode($response_body, true);

                    if ($res) {
                        // Check for capture success
                        if ($res['status'] == 'SUCCESS' && $res['amount'] == $order->get_total() && $res['currency'] == $order->get_currency()) {
                            $order->add_order_note('Payment successful');
                            //wc_add_notice(__('Payment Success: ', 'woothemes') . "Payment complete", 'success');
                            $order->payment_complete();
                            $redirect_url = $order->get_checkout_order_received_url();
                            wp_redirect($redirect_url);
                            error_log('redirect_url ' . $redirect_url);
                            exit;
                        } else {
                            // capture failed
                            error_log('capture failed [Spotii spotii_response_handler]');
                        }
                    } else {
                        error_log('Json Response in capture empty[Spotii spotii_response_handler] ');
                    }
                } catch (Exception $e) {
                    error_log("Error on spotii_response handler[Spotii spotii_response_handler]: " . $e->getMessage());
                }
            } else {
                // url param failed
                error_log('url param failed [Spotii spotii_response_handler]');
            }

            // If you are here, payment was unsuccessful
            $order->add_order_note('Payment with Spotii failed' . $response_body);
            wc_add_notice(__('Checkout Error: ', 'woothemes') . "Payment with Spotii failed. Please try again", 'error');
            $order->update_status('failed', __('Payment with Spotii failed', 'woocommerce'));
            $redirect_url = $order->get_cancel_order_url();
            wp_redirect($redirect_url);
            exit;
        }

        /**
         * Process refunds
         */
        public function process_refund($order_id, $amount = null, $reason = '')
        {
            $order = wc_get_order($order_id);

            $url_part = $this->testmode ? 'https://api.sandbox.spotii.me/api/v1.0/orders/'
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
                'body' => wp_json_encode($body)
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
            if ($res['status'] == 'SUCCESS' && $res['amount'] == $amount && $res['currency'] == $order->get_currency()) {
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
}