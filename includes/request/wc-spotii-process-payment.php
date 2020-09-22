<?php
/*
* Process payments: magic begins here
*/
function processPayment($order_id, $th, $type = null, $addon){
    $order = new WC_Order($order_id);
    // Spotii minimum limit 
    if ($type != "Pay Now" && (int)$order->total < 200) {
        error_log("Exception [WP_Error_Spotii Process Payment] Order total less than spotii minimum limit: " . $curr);
        throw new Exception(__('Order total less than spotii minimum limit'));
    }

    $orderId = $order_id;
    $curr = $order->get_currency();
    // validate currency 
    if (!validate_curr($curr)) {
        error_log("Exception [WP_Error_Spotii Process Payment] Currency is not supported by Spotii: " . $curr);
        throw new Exception(__('Currency is not supported by Spotii'));
    }
    try {
        $url = $th->api . 'checkouts/';

        $payload = get_checkout_payload($order, $th, $type, $addon);

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
            $curr = $response_body_arr['currency'];
            $total = $response_body_arr['total'];
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