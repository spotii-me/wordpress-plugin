<?php
/*
/* Plugin Parameters
*/
function gatewayParameters($th, $type = null){

    $th->id = $type == "Pay Now" ? "spotii_pay_now" : ($type == "Annual Subscription" ? "spotii_annual_subscription": 'spotii_shop_now_pay_later');
    $type == "Shop Now Pay Later" || $type == "Annual Subscription"  ? $th->icon = 'https://spotii.me/img/logo.svg' : '';
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
    $th->order_min = $th->get_option('order_minimum', '');
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
    // Widget settings
    $th->widget_theme = $th->get_option('widget_theme', '');
    $th->widget_text = $th->get_option('widget_text', '');
    $th->popup_link = $th->get_option('popup_learnMore_link', '');
    $th->show_custom_note_ar = $th->get_option('show_custom_note_ar', '');
    $th->show_custom_note_en = $th->get_option('show_custom_note_en', '');
    $th->render_path_product = $th->get_option('render_path_product', '');
    $th->render_path_cart = $th->get_option('render_path_cart', '');

    $th->auth = $th->testMode ? "https://auth.sandbox.spotii.me/api/v1.0/" : "https://auth.spotii.me/api/v1.0/";
    $th->api = $th->testMode ? "https://api.sandbox.spotii.me/api/v1.0/" : "https://api.spotii.me/api/v1.0/";

    add_action('woocommerce_update_options_payment_gateways_' . $th->id, array($th, 'process_admin_options'));

}
