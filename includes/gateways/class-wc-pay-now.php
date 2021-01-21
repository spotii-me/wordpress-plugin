<?php
/*
/* Pay Now with spotii 
*/
class WC_Spotii_Gateway_Pay_Now extends WC_Payment_Gateway{

	public function __construct(){
		add_action('woocommerce_api_wc_spotii_gateway_pay_now', array($this, 'spotii_response_handler'));
		gatewayParameters($this, "Pay Now");
	}
	/**
	 * Define fields and labels in Admin Panel
	 */
	public function init_form_fields(){
		form_fields($this, "Pay Now");
	}
	/*
	* Process payments: magic begins here
	*/
	public function process_payment($order_id){
		return processPayment($order_id, $this, "Pay Now", "wc_spotii_gateway_pay_now");
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
