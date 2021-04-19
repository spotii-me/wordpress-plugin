<?php
/**
 * Helper to prepare checkout payload
 */
function get_checkout_payload($order, $th, $type, $addon){
    $order_id = $order->get_meta('_alg_wc_custom_order_number') !== "" ? $order->get_meta('_alg_wc_custom_order_number') : $order->get_id();
    $currency = $order->get_currency();
    $total=$order->get_total();
    spotiiAuth($th, $addon,  $currency);
//    if($currency == "USD" ){
//        $total = $total * 3.6730;
//        $currency= "AED";
//    }
    $headers =  getHeader($th);
    $notify_url = get_home_url(null, "?wc-api=" . $addon);
    $body = array(
        "reference" => $order_id,
        "display_reference" => $order_id,
        "description" => "Woo- Commerce Order #" . $order->get_id(),
        "total" => round($total, 4),
        "currency" => $currency,
        "confirm_callback_url" => $notify_url . "&o=" . $order->get_id() . "&s=s",
        "reject_callback_url" => $notify_url . "&o=" . $order->get_id() . "&s=f",

        // Order
        "order" => array(
            "tax_amount" => $order->get_total_tax(),
            "shipping_amount" => $order->get_shipping_total(),
            "discount" => $order->get_total_discount(),
            "customer" => array(
                "first_name" => $order->get_user()->first_name ? $order->get_user()->first_name : $order->get_billing_first_name(),
                "last_name" => $order->get_user()->last_name ? $order->get_user()->last_name : $order->get_billing_last_name(),
                "email" => $order->get_user()->user_email ? $order->get_user()->user_email : $order->get_billing_email(),
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
    if ($type == "Pay Now") {
        $body['plan'] = "pay-now";
    }
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
function getHeader($th){
    $headers = array(
        'Accept' => 'application/json; indent=4',
        'Content-Type' => 'application/json',
        'Access-Control-Allow-Origin' => '*',
        'Authorization' => 'Bearer ' . $th->token
    );
    return $headers;
}
