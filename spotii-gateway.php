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
            //echo '<script>console.log("in construct() "+'.$response_body.');</script>';
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
            //if ($this->description) echo wpautop(wptexturize($this->description));

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
            if(session_id() == '' || !isset($_SESSION)) {
            // session isn't started
            session_start();
           // echo '<script>console.log("started ");</script>';
             }
            $order = new WC_Order($order_id);

            try {
                $url = $this->testmode ? 'https://api.sandbox.spotii.me/api/v1.0/checkouts/'
                    : 'https://api.spotii.me/api/v1.0/checkouts/';
                $payload = $this->get_checkout_payload($order);
               // echo '<div><script>console.log("$url "+'.$url.');</script>';
                $response = wp_remote_post($url, $payload);

                ob_start();?>
                <script>
                var array = <?php $response ?>;
                var iterator = array.values(); 
                // Here all the elements of the array is being printed. 
                for (let elements of iterator) { 
                console.log(elements); 
                }</script>          
                <?php echo ob_get_clean();
               // echo ' </div>';
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
                    //here is the redirect 
                     echo '<div><script>console.log("redirect "+'.$redirect_url.');</script></div>';
                    
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
                        "first_name" =>$order->get_billing_first_name(),
                        "last_name" => $order->get_billing_last_name(),
                        "email" => $order->get_billing_email(),
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
      /*  public function spotii_response_handler()
        {
            echo '<script>console.log("spotii handler");</script>';
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
                   //here
                    echo '<script>console.log("url "+'.$url.');</script>';
                    echo '<script>console.log("payload "+'.$payload.');</script>';
                    $response = wp_remote_post($url, $payload);
                    //echo '<script>console.log('.$response.');</script>';

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
                            echo '<script>console.log("success "+'.$redirect_url.');</script>';
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
            echo '<script>console.log("fail "+'.$redirect_url.');</script>';
            wp_redirect($redirect_url);
            exit;
        }*/

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

/*
echo'<script type="text/javascript" src="//code.jquery.com/jquery-3.5.1.min.js"></script>
                    <script src="https://widget.spotii.me/v1/javascript/fancybox-2.0.min.js"></script>
                    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
                    <link rel="stylesheet" href="https://widget.spotii.me/v1/javascript/fancybox-2.0.min.css">

                    <button style="display:none" id="closeclick">set overlay closeClick to false</button>                                
                    <button style="display:none" id="closeiframebtn">set overlay closeClick to false</button>
                    <div class="fancy-box-container">
                        <a id="fancy" style="display: none;" class="fancy-box" href="">open fancybox</a>
                    </div>

                    <style>@-webkit-keyframes sptiianimloading{to{-webkit-transform:rotate(360deg);transform:rotate(360deg)}}@keyframes sptiianimloading{to{-webkit-transform:rotate(360deg);transform:rotate(360deg)}}.sptii-overlay{z-index:1000;width:auto;height:auto;background:rgba(0,0,0,.88);display:flex;position:fixed;top:0;left:0;bottom:0;right:0;flex-direction:column;align-items:center;justify-content:center;text-align:center}.sptii-overlay .sptii-loading-icon{display:inline-block;vertical-align:middle;margin-top:-.125em;-webkit-animation:sptiianimloading 1s linear infinite;animation:sptiianimloading 1s linear infinite}.sptii-overlay .sptii-loading-icon svg{display:inline-block}.sptii-overlay .sptii-loading{width:24px;padding:16px}.sptii-overlay .sptii-loading .sptii-loading-icon{width:24px;height:24px}.sptii-overlay .sptii-logo svg{width:20em;height:10em}.sptii-overlay .sptii-spinnerText{display:flex;flex-direction:column;align-items:center;justify-content:center}.sptii-overlay .sptii-text{color:#858585;font-family:Overpass,sans-serif;font-size:large}
                    .sptii-overlay {
                    display: none;
                    }
                    </style>

                    <script>
                    var failedCheckOutStatus = "FAILED";
                    var submittedCheckOutStatus = "SUBMITTED";
                    var successCheckOutStatus = "SUCCESS";
                    const root=document.getElementsByTagName("body")[0];
                    continueToSpotiipay('.$redirect_url.');

                    window.closeIFrameOnCompleteOrder = function(message) {
                        var status = message.status;
                        rejectUrl = message.rejectUrl;
                        confirmUrl = message.confirmUrl;
                        console.log("status -"+status);
                        console.log("rejectUrl -"+rejectUrl);
                        console.log("confirmUrl -"+confirmUrl);
                        switch (status) {
                            case successCheckOutStatus:
                            console.log("successCheckOutStatus");
                            document.getElementById("closeiframebtn").onclick = function() {
                                closeIFrame();
                                location.href = confirmUrl; 
                            };
                            removeOverlay();
                            break;
                            case failedCheckOutStatus:
                            console.log("failedCheckOutStatus");
                            document.getElementById("closeiframebtn").onclick = function() {
                                closeIFrame();
                                location.href = rejectUrl; 
                            };
                            removeOverlay();
                            break;
                            case submittedCheckOutStatus: 
                            removeOverlay();
                            break;
                            default: 
                            removeOverlay();
                            break;
                        }
                        };
                    //Check if browser support the popup
                    const thirdPartySupported = root => {
                    return new Promise((resolve, reject) => {
                        const receiveMessage = function(evt) {
                        if (evt.data === "MM:3PCunsupported") {
                            reject();
                        } else if (evt.data === "MM:3PCsupported") {
                            resolve();
                        }
                        };
                        window.addEventListener("message", receiveMessage, false);
                        const frame = createElement("iframe", {
                        src: "https://mindmup.github.io/3rdpartycookiecheck/start.html",
                        });
                        frame.style.display = "none";
                        root.appendChild(frame);
                    });
                    };

                    //Redirect to Spotii
                    const redirectToSpotiiCheckout = function(checkoutUrl, timeout) {
                    setTimeout(function() {
                        window.location = checkoutUrl;
                    }, timeout); 
                    };

                    //Check if its a safari broswer
                    function isMobileSafari() {
                    const ua = (window && window.navigator && window.navigator.userAgent) || "";
                    const iOS = !!ua.match(/iPad/i) || !!ua.match(/iPhone/i);
                    const webkit = !!ua.match(/WebKit/i);
                    return iOS && webkit && !ua.match(/CriOS/i);
                    }

                    //needed functions for the loadin page
                    function createElement(tagName, attributes, content) {
                    const el = document.createElement(tagName);
                    if (attributes) {
                        Object.keys(attributes).forEach(function(attr) {
                            el[attr] = attributes[attr];
                        });
                    }
                    if (content && content.nodeType === Node.ELEMENT_NODE) {
                        el.appendChild(content);
                    } else {
                        el.innerHTML = content;
                    }
                    return el;
                    }

                    function Spinner() {
                        const span = createElement("span");
                        span.className = "sptii-loading-icon";
                        span.innerHTML =
                        \'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 1024 1024"><path d="M988 548c-19.9 0-36-16.1-36-36 0-59.4-11.6-117-34.6-171.3a440.45 440.45 0 0 0-94.3-139.9 437.71 437.71 0 0 0-139.9-94.3C629 83.6 571.4 72 512 72c-19.9 0-36-16.1-36-36s16.1-36 36-36c69.1 0 136.2 13.5 199.3 40.3C772.3 66 827 103 874 150c47 47 83.9 101.8 109.7 162.7 26.7 63.1 40.2 130.2 40.2 199.3.1 19.9-16 36-35.9 36z" fill="orange" /></svg>\';
                        return span;
                        }
                        function Logo() {
                        const span = createElement("span");
                        span.innerHTML = \'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 574.97 155.42"><defs><style>.cls-1{fill:#858585;}.cls-2{fill:#333;}</style></defs><title>Spotii_dark_logo</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><g id="Spotii_dark_logo"><path class="cls-1" d="M93.19,42.93l23.28-23.28A65.93,65.93,0,0,0,13.11,32.76,65.9,65.9,0,0,1,93.19,42.93Z"/><path class="cls-1" d="M93.19,42.93,23.28,112.84A65.93,65.93,0,0,0,103.37,123,65.93,65.93,0,0,0,93.19,42.93Z"/><path class="cls-2" d="M23.28,112.84,0,136.12A66,66,0,0,0,103.37,123,65.93,65.93,0,0,1,23.28,112.84Z"/><path class="cls-2" d="M23.28,112.84,93.19,42.93A65.9,65.9,0,0,0,13.11,32.76,65.9,65.9,0,0,0,23.28,112.84Z"/><path class="cls-2" d="M228,94.14c0,14.57-11.15,28.8-34,28.8-26.75,0-35.32-17.31-36-26.74l22.12-4c.35,5.83,4.46,11.49,13.37,11.49,6.69,0,9.95-3.6,9.95-7.37,0-3.09-2.06-5.66-8.4-7l-9.77-2.23c-18.18-3.94-25.38-14.23-25.38-26.23,0-15.6,13.72-28.29,32.75-28.29C217.33,32.59,225.9,48,226.76,58l-21.61,3.95c-.68-5.66-4.28-10.46-12.17-10.46-5,0-9.26,2.91-9.26,7.37,0,3.6,2.92,5.66,6.69,6.34l11.31,2.23C219.39,71,228,81.62,228,94.14ZM425.84,77.72a45.23,45.23,0,1,1-45.23-45.23A45.22,45.22,0,0,1,425.84,77.72Zm-26,0c0-11.73-8.6-21.23-19.2-21.23S361.4,66,361.4,77.72s8.6,21.22,19.21,21.22S399.81,89.44,399.81,77.72ZM518.92,0a13,13,0,1,0,13,13A13,13,0,0,0,518.92,0Zm-13,122.94H532V46.8L505.89,32.48ZM561.94,49.7a13,13,0,1,0-13-13A13,13,0,0,0,561.94,49.7Zm-13,6.43v66.81H575V70.45ZM447.18,32.48H431.49V58.64h15.69V94.21a28.73,28.73,0,0,0,28.74,28.73h13V96.88h-3.49c-6.74,0-12.2-6-12.2-13.48V58.64h15.69V32.48H473.24V14.53L447.1,0ZM265.33,115.93v39.49L239.26,141.1V32.48h26.07v7a39.48,39.48,0,0,1,22.42-7c23.18,0,42,20.25,42,45.23s-18.79,45.23-42,45.23A39.56,39.56,0,0,1,265.33,115.93Zm0-37.48c.36,11.38,8.79,20.48,19.16,20.48,10.61,0,19.21-9.5,19.21-21.22s-8.6-21.23-19.21-21.23c-10.37,0-18.8,9.11-19.16,20.48Z"/></g></g></g></svg>\';
                        return span;
                        }
                    function SpinTextNode() {
                    const text = isMobileSafari() ? "Redirecting you to Spotii..." : "Checking your payment status with Spotii...";
                    const first= createElement("p", {}, text);
                    const cont = createElement("span", {className: "sptii-text"}, first);
                    const spinner = createElement("span", { className: "sptii-loading" }, Spinner());
                    const spinText = createElement("span", { className: "sptii-spinnerText" }, cont);
                    spinText.appendChild(spinner);
                    return spinText;
                    }
                    //--------------------

                    //Show the loading page
                    function showOverlay() {
                    console.log("showOverlay");
                    const overlay = createElement("div", {className: "sptii-overlay"}, "");
                    const logo = createElement("span", { className: "sptii-logo" }, Logo());
                    document.getElementsByTagName("body")[0].appendChild(overlay);
                    overlay.appendChild(logo);
                    overlay.appendChild(SpinTextNode());
                    }

                    //Remove the loading page
                    function removeOverlay() {
                    console.log("removeOverlay");
                    var overlay = document.getElementsByClassName("sptii-overlay")[0];
                    document.getElementsByTagName("body")[0].removeChild(overlay);
                    }

                    const openIframeSpotiiCheckout = function(url) {
                        console.log("opened spotii iframe");
                        // Make a post request to redirect
                        $(".fancy-box").attr("href", url);
                        openIFrame();
                    };
                    const continueToSpotiipay = function(url){
                    console.log("continueToSpotiipay called");
                    showOverlay();
                    if (isMobileSafari()) {
                    redirectToSpotiiCheckout(url,2500);
                    } else  {
                    thirdPartySupported(root).then( () => {
                    openIframeSpotiiCheckout(url);
                        }).catch(() => {
                    redirectToSpotiiCheckout(url, 2500);
                    });
                    } 
                  };
                  </script>';

*/