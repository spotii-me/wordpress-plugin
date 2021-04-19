<?php
/*
/* Pay Now with spotii
*/
class WC_Spotii_Gateway_Annual_Subscription extends WC_Payment_Gateway{

    public function __construct(){
        add_action('woocommerce_api_wc_spotii_gateway_annual_subscription', array($this, 'spotii_response_handler'));
        gatewayParameters($this, "Annual Subscription");
    }
    /**
     * Define fields and labels in Admin Panel
     */
    public function init_form_fields(){
        form_fields($this, "Annual Subscription");
    }
    /*
    * Process payments: magic begins here
    */
    public function process_payment($order_id){
        return processPayment($order_id, $this, "Annual Subscription", "wc_spotii_gateway_annual_subscription");
    }
    /**
     * Called when Spotii checkout page redirects back to merchant page
     */
    public function spotii_response_handler(){
        return spotiiResponseHandler($this);
    }
    /**
     * Process refunds
     */
    public function process_refund($order_id, $amount = null, $reason = ''){
        return processRefund($order_id, $amount, $reason, $this);
    }
}
