<?php
/**
 * Process refunds
 */
function processRefund($order_id, $amount = null, $reason = '', $th){

    $order = wc_get_order($order_id);
    $url = $th->api . 'orders/' . $order_id . '/refund';

    $headers = $th->get_header();
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
    if ($res['status'] == 'SUCCESS' &&  check_amount(floatval($res['amount']), $res['currency'], floatval($amount), $order->get_currency())) {
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