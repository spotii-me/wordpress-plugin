<?php
 /**
 * Called when Spotii checkout page redirects back to merchant page
 */
function spotiiResponseHandler($th){
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
            $url = $th->api . 'orders/' . $order_id .  '/capture/';
            $headers = getHeader($th);
            $payload = array(
                'method' => 'POST',
                'headers' => $headers,
                'body' => '{}',
                'timeout' => 20
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