<?php
/**
 * Spotii functions
 */

defined( 'ABSPATH' ) || exit;

/*
/* ADD WIDGETS, ENQUEUE NEEDED CSS AND JS
*/
add_action('woocommerce_proceed_to_checkout', 'add_cart_widget');
add_action('woocommerce_single_product_summary', 'add_product_widget');
$lang = get_locale();
/**
 * Register the script and inject parameters.
 *
 * @param string     $handle Script handle the data will be attached to.
 * @param array|null $params Parameters injected.
 */
function wc_spotii_script(){
    wp_enqueue_style('spotii-gateway', plugin_dir_url(__FILE__) .  '../assets/css/spotii-checkout.css', true);
    if (is_checkout()) {
        wp_enqueue_script('spotii-lightbox', plugin_dir_url(__FILE__) .  '../assets/js/iframe-lightbox.js', array('jquery'), '0.01', true);
    }
    wp_enqueue_script('spotii-checkout', plugin_dir_url(__FILE__) . '../assets/js/spotii-checkout.js', array('jquery'), '0.01', true);
    if (is_checkout()) {
        wp_deregister_script('wc-checkout');
        wp_enqueue_script('wc-country-select', plugin_dir_url(__FILE__) . '../assets/js/country-select.min.js', array('jquery'), '0.01', true);
        wp_enqueue_script('wc-address-i18n', plugin_dir_url(__FILE__) . '../assets/js/address-i18n.min.js', array('jquery'), '0.01', true);
        wp_enqueue_script('wc-checkout', plugin_dir_url(__FILE__) . '../assets/js/woocommerce-checkout.js', array('jquery'), '0.01', true);
    }
    wp_enqueue_script('jquery');
    wp_localize_script('jquery', 'spotii_ajax', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'wc_spotii_script', 12);

/*
/* Add Lightbox html to footer
*/
function spotii_footer(){
    echo '
    <button style="display:none" id="closeclick">set overlay closeClick to false</button>                                
    <button style="display:none" id="closeiframebtn">set overlay closeClick to false</button>
    <div class="fancy-box-container">
        <a id="fancy" style="display: none;" class="fancy-box lightbox" href="">open fancybox</a>
    </div>
    <a href="#top" style="display:none"></a>
    ';
}
add_action('wp_footer', 'spotii_footer');
/*
/* Admin js for hide and show sandbox fields
*/
function admin_js() { ?>
    <script type="text/javascript">

        jQuery(document).ready( function ($) { 
            // hide show test keys 
            if($("input[id*='testmode']").is(':checked')){
                $("input[id*='testmode']").parents("tr").siblings().find("input[id*='test']").parents("tr").show();
            }else{
                $("input[id*='testmode']").parents("tr").siblings().find("input[id*='test']").parents("tr").hide();
            }
            $("input[id*='testmode']").on("click","",function(){
                if($(this).is(':checked')){
                    $(this).parents("tr").siblings().find("input[id*='test']").parents("tr").show();
                }else{
                    $(this).parents("tr").siblings().find("input[id*='test']").parents("tr").hide();
                }
            })
            // hide show  aed keys
            if($("input[id*='add_aed_key']").is(':checked')){
                if($("input[id*='testmode']").is(':checked')){
                        $("input[id*='add_aed_key']").parents("tr").siblings().find("input[id*='_aed']").parents("tr").show();
                    }else{
                        $("input[id*='add_aed_key']").parents("tr").siblings().find("input[id*='live_aed']").parents("tr").show();
                    }
            }else{
                $("input[id*='aed_key']").parents("tr").siblings().find("input[id*='_aed']").parents("tr").hide();
            }
            $("input[id*='add_aed_key']").on("click","",function(){
                if($(this).is(':checked')){
                    if($("input[id*='testmode']").is(':checked')){
                        $(this).parents("tr").siblings().find("input[id*='_aed']").parents("tr").show();
                    }else{
                        $(this).parents("tr").siblings().find("input[id*='live_aed']").parents("tr").show();
                    }
                    
                }else{
                    $(this).parents("tr").siblings().find("input[id*='_aed']").parents("tr").hide();
                }
            })
            // hide show sar keys 
            if($("input[id*='add_sar_key']").is(':checked')){
                if($("input[id*='testmode']").is(':checked')){
                        $("input[id*='add_sar_key']").parents("tr").siblings().find("input[id*='_sar']").parents("tr").show();
                    }else{
                        $("input[id*='add_sar_key']").parents("tr").siblings().find("input[id*='live_sar']").parents("tr").show();
                    }
            }else{
                $("input[id*='sar_key']").parents("tr").siblings().find("input[id*='_sar']").parents("tr").hide();
            }
            $("input[id*='add_sar_key']").on("click","",function(){
                if($(this).is(':checked')){
                    if($("input[id*='testmode']").is(':checked')){
                        $(this).parents("tr").siblings().find("input[id*='_sar']").parents("tr").show();
                    }else{
                        $(this).parents("tr").siblings().find("input[id*='live_sar']").parents("tr").show();
                    }
                    
                }else{
                    $(this).parents("tr").siblings().find("input[id*='_sar']").parents("tr").hide();
                }
            })
            
        });

    </script>
<?php }

add_action('admin_head', 'admin_js');
/*
/* Update order status 
*/
function spotii_order_update(){
    
    $order_id = isset($_POST["order_id"]) ? $_POST["order_id"] : "";
    $order_status = isset($_POST["status"]) ? $_POST["status"] : "";
    $spotii_total = isset($_POST["total"]) ? floatval($_POST["total"]) : "";
    $spotii_curr = isset($_POST["curr"]) ? $_POST["curr"] : "";
    $spotiiApi = isset($_POST["api"]) ? $_POST["api"] : "";
    $order = wc_get_order($order_id);
    $lang = get_locale();
    $errorChe = $lang == 'ar' ? 'خطأ في تأكيد الطلب: ' : 'Checkout Error: ' ;

    if($order->has_status('completed') || $order->has_status('processing')){
        $error = $lang == 'ar' ? "الطلب موجود بالفعل بحالة " .$order->get_status() : "Order already exist with ".$order->get_status()." status";
        wc_add_notice(__($errorChe, 'woothemes') . $error, 'error');
        $redirect_url = $order->get_checkout_order_received_url();
        echo json_encode(array('result' => 'success', 'redirect' => $redirect_url));
        die;
    }
    $errorPaymentFailed = $lang == 'ar' ? "لقد حصل خطأ عند الدفع عن طريق سبوتي، رجاءً حاول مرة اخرى" : "Payment with Spotii failed. Please try again";
    if(!empty($spotii_total)){

        $spotiiRef = $order->get_meta('reference');
        $spotiiToken = $order->get_meta('token');
        error_log('orderstatus' . $order->get_status());
        if ( $order_status === "completed" && check_amount($spotii_total, $spotii_curr, floatval($order->get_total()), $order->get_currency())) {
            // Capture payment
            $url = $spotiiApi . 'orders/' . $spotiiRef .  '/capture/';
            $headers = array(
                'Accept' => 'application/json; indent=4',
                'Content-Type' => 'application/json',
                'Access-Control-Allow-Origin' => '*',
                'Authorization' => 'Bearer ' . $spotiiToken
            );
            $payload = array(
                'method' => 'POST',
                'headers' => $headers,
                'body' => '{}',
                'timeout' => 20
            );
            $response = wp_remote_post($url, $payload);
            if (is_wp_error($response)) {
                $order->add_order_note('Order capture failed');
                wc_add_notice(__($errorChe, 'woothemes') . $errorPaymentFailed, 'error');
                $order->update_status('failed', __('Order capture failed', 'woocommerce'));
                $redirect_url = $order->get_cancel_order_url();
                echo json_encode(array('result' => 'error', 'redirect' => $redirect_url));
                die;
            }
            if (empty($response['body'])) {
                error_log('Response Empty [Spotii spotii_response_handler] ');
                throw new Exception(__('Empty response body'));
            }
            
            $response_body = $response['body'];
            $res = json_decode($response_body, true);
            
            if ( $res['status'] === 'SUCCESS' && check_amount($spotii_total, $spotii_curr, floatval($order->get_total()), $order->get_currency())) {
                try {
                    $order->add_order_note('Payment successful');
                    $order->payment_complete();
                    $redirect_url = $order->get_checkout_order_received_url();
                    error_log('redirect_url ' . $redirect_url);
                    echo json_encode(array('result' => 'success', 'redirect' => $redirect_url));
                    die;
                } catch (Exception $e) {
                    error_log("Error on spotii_response handler[Spotii spotii_response_handler]: " . $e->getMessage());
                }
            }else{
                $order->add_order_note('Order capture failed');
                wc_add_notice(__($errorChe, 'woothemes') . $errorPaymentFailed, 'error');
                $order->update_status('failed', __('Order capture failed', 'woocommerce'));
                $redirect_url = $order->get_cancel_order_url();
                echo json_encode(array('result' => 'error', 'redirect' => $redirect_url));
                die;
            }
        } else if ($order_status == "canceled") {
            $order->add_order_note('Payment with Spotii failed');
            // wc_add_notice(__('Checkout Error: ', 'woothemes') . "Payment with Spotii failed. Please try again", 'error');
            $order->update_status('failed', __($errorPaymentFailed, 'woocommerce'));
            $redirect_url = $order->get_cancel_order_url();
            echo json_encode(array('result' => 'error', 'redirect' => $redirect_url));
            die;
        }

    }else{
        wc_add_notice(__($errorChe, 'woothemes') . $errorPaymentFailed, 'error');
        $redirect_url = $order->get_cancel_order_url();
        echo json_encode(array('result' => 'success', 'redirect' => $redirect_url));
        die;
    }
}
add_action('wp_ajax_spotii_order_update', 'spotii_order_update');
add_action('wp_ajax_nopriv_spotii_order_update', 'spotii_order_update');