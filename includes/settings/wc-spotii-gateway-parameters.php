<?php
/*
/* Plugin Parameters 
*/
function gatewayParameters($th, $type = null){

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
    $th->testMode = false;
    $th->testMode = 'yes' === $th->get_option('testmode', 'yes');
    // AED api 
    $th->publicKeyAED = $th->get_option('public_key_live_aed', '');
    $th->privateKeyAED = $th->get_option('private_key_live_aed', '');
    $th->testPublicKeyAED = $th->get_option('public_key_test_aed', '');
    $th->testPrivateKeyAED = $th->get_option('private_key_test_aed', '');
    // SAR Api 
    $th->publicKeySAR = $th->get_option('public_key_live_sar', '');
    $th->privateKeySAR = $th->get_option('private_key_live_sar', '');
    $th->testPublicKeySAR = $th->get_option('public_key_test_sar', '');
    $th->testPrivateKeySAR = $th->get_option('private_key_test_sar', '');

    $th->auth = $th->testMode ? "https://auth.dev.spotii.me/api/v1.0/" : "https://auth.spotii.me/api/v1.0/";
    $th->api = $th->testMode ? "https://api.dev.spotii.me/api/v1.0/" : "https://api.spotii.me/api/v1.0/";

    add_action('woocommerce_update_options_payment_gateways_' . $th->id, array($th, 'process_admin_options'));
    
}