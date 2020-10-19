<?php
 /**
 * Called when Spotii checkout page redirects back to merchant page
 */
function spotiiResponseHandler($th){
    $order_id = $_GET['o'];
    $order = wc_get_order($order_id);
    if ($order->has_status('completed') || $order->has_status('processing')) {
        return;
    }

    $status = $_GET['s'];
    // Check for url param success
    if ($status == 's') {
        try {
            $order->add_order_note('Payment successful');
            //wc_add_notice(__('Payment Success: ', 'woothemes') . "Payment complete", 'success');
            $order->payment_complete();
            $redirect_url = $order->get_checkout_order_received_url();
            wp_redirect($redirect_url);
            error_log('redirect_url ' . $redirect_url);
            error_log('Order placed successfully [Spotii spotii_response_handler]');
            exit;
        } catch (Exception $e) {
            error_log("Error on spotii_response handler[Spotii spotii_response_handler]: " . $e->getMessage());
        }
    } else {
        // url param failed
        error_log('url param failed [Spotii spotii_response_handler]');
    }

    // If you are here, payment was unsuccessful
    $order->add_order_note('Payment with Spotii failed');
    wc_add_notice(__('Checkout Error: ', 'woothemes') . "Payment with Spotii failed. Please try again", 'error');
    $order->update_status('failed', __('Payment with Spotii failed', 'woocommerce'));
    $redirect_url = $order->get_cancel_order_url();
    wp_redirect($redirect_url);
    exit;
}