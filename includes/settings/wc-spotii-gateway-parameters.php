<?php
/*
/* Plugin Parameters 
*/
function gatewayParameters($th, $type){

    $th->id = $type == "Pay Now" ? "spotii_pay_now" : 'spotii_shop_now_pay_later';
    $type == "Shop Now Pay Later" ? $th->icon = 'https://spotii.me/img/logo.svg' : '';
    $th->method_title = 'Spotii';
    $th->method_description =  $type == "Pay Now" ? "Pay instant payment with spotii." : 'Have your customers pay over time for their purchases. No hidden fees, no interest.';

    // Options supported by Spotii payment gateway
    $th->supports = array(
        'products',
        'refunds'
    );

    // Initialize fields in admin panel
    $th->init_form_fields();

    // Load settings
    $th->init_settings();
    $th->title = $th->get_option('title', $type == "Pay Now" ? "Spotii: Pay Now" : 'Shop now, Pay later');
    if ($type != "Pay Now") {
        $th->description = $th->get_option('description', 'Shop now, Pay later');
    }
    $th->enabled = $th->get_option('enabled', 'yes');
    // AED api 
    $th->testMode = false;
    $th->testMode = 'yes' === $th->get_option('testmode', '');
    $th->add_sar_key = 'yes' === $th->get_option('add_sar_key', 'yes');
    $th->add_aed_key = 'yes' === $th->get_option('add_aed_key', 'yes');
    $th->publicKeyAED = $th->get_option('public_key_aed', '');
    $th->privateKeyAED = $th->get_option('private_key_aed', '');
    $th->testPublicKeyAED = $th->get_option('test_public_key_aed', '');
    $th->testPrivateKeyAED = $th->get_option('test_private_key_aed', '');
    // SAR Api 
    $th->publicKeySAR = $th->get_option('public_key_sar', '');
    $th->privateKeySAR = $th->get_option('private_key_sar', '');
    $th->testPublicKeySAR = $th->get_option('test_public_key_sar', '');
    $th->testPrivateKeySAR = $th->get_option('test_private_key_sar', '');

    $th->auth = $th->testMode ? "https://auth.dev.spotii.me/api/v1.0/" : "https://auth.spotii.me/api/v1.0/";
    $th->api = $th->testMode ? "https://api.dev.spotii.me/api/v1.0/" : "https://api.spotii.me/api/v1.0/";

    add_action('woocommerce_update_options_payment_gateways_' . $th->id, array($th, 'process_admin_options'));

    $auth_url =  $th->auth . 'merchant/authentication/';
    if (get_woocommerce_currency() == "SAR") {
        $public_key =  $th->testMode ? $th->testPublicKeySAR : $th->publicKeySAR;
        $private_key = $th->testMode ? $th->testPrivateKeySAR : $th->privateKeySAR;
    } else {
        $public_key =  $th->testMode ? $th->testPublicKeyAED : $th->publicKeyAED;
        $private_key = $th->testMode ? $th->testPrivateKeyAED : $th->privateKeyAED;
    }


    $headers = array(
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    );

    $body = array(
        'public_key' => $public_key,
        'private_key' => $private_key
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
        $th->token = $response_body_arr['token'];
    } else {
        error_log("Error on authentication: " . $response_body);
    }
}