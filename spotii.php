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
 * Developer: Abu Sufyan
 * Developer URI: https://github.com/chodri
 *
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * @package Spotii
 */
/*
 * Register our PHP class as a WooCommerce payment gateway
 */
defined( 'ABSPATH' ) || exit;

require __DIR__ . '/includes/settings/wc-spotii-cart-widget.php';
require __DIR__ . '/includes/settings/wc-spotii-product-widget.php';


/*
 *  spotii add gateway class
 */
add_filter('woocommerce_payment_gateways', 'spotii_add_gateway_class');

function spotii_add_gateway_class($gateways){

    $gateways[] = 'WC_Spotii_Gateway_Shop_Now_Pay_Later';
    $gateways[] = 'WC_Spotii_Gateway_Pay_Now';
    return $gateways;

}
/*
 * Load Spotii Gateway class on plugins_loaded action
 */
add_action('plugins_loaded', 'spotii_init_gateway_class');


function spotii_init_gateway_class(){

    if (class_exists('WC_Spotii_Gateway_Pay_Now') || class_exists('WC_Spotii_Gateway_Shop_Now_Pay_Later') || !class_exists('WC_Payment_Gateway')) return;

    define( 'WC_SPOTII_DIR_PATH', plugin_dir_path( __FILE__ ) );
    /*
    /* Include files
    */
    require_once WC_SPOTII_DIR_PATH . 'includes/settings/wc-spotii-gateway-parameters.php';
    require_once WC_SPOTII_DIR_PATH . 'includes/request/wc-spotii-auth.php';
    require_once WC_SPOTII_DIR_PATH . 'includes/settings/wc-spotii-form-fields.php';
    require_once WC_SPOTII_DIR_PATH . 'includes/settings/wc-spotii-validation.php';
    require_once WC_SPOTII_DIR_PATH . 'includes/request/wc-spotii-payload.php';
    require_once WC_SPOTII_DIR_PATH . 'includes/request/wc-spotii-process-payment.php';
    require_once WC_SPOTII_DIR_PATH . 'includes/request/wc-spotii-response-handler.php';
    require_once WC_SPOTII_DIR_PATH . 'includes/request/wc-spotii-refund.php';
    /*
    * Load Spotii Gateway
    */
    require_once WC_SPOTII_DIR_PATH . '/includes/gateways/class-wc-pay-now.php';
	require_once WC_SPOTII_DIR_PATH . '/includes/gateways/class-wc-shop-now-pay-later.php';
    /*
    * Load Spotii function 
    */
    require_once WC_SPOTII_DIR_PATH . '/includes/wc-spotii-function.php';
}
