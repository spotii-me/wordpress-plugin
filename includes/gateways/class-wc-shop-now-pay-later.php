<?php
/*
/* Shop Now Pay Later
*/
class WC_Spotii_Gateway_Shop_Now_Pay_Later extends WC_Payment_Gateway{

    public function __construct(){

        add_action('woocommerce_api_wc_spotii_gateway_shop_now_pay_later', array($this, 'spotii_response_handler'));
        gatewayParameters($this, "Shop Now Pay Later");
    }
    /**
     * Define fields and labels in Admin Panel
     */
    public function init_form_fields(){
        form_fields($this);
    }
    /**
     * Get icon for Spotii option on checkout page
     */
    public function get_icon(){
        $icon = $this->icon ? '<img src="' . WC_HTTPS::force_https_url($this->icon) . '" alt="' . esc_attr($this->get_title()) . '" class="spotii-checkout-img" />' : '';
        return apply_filters('woocommerce_gateway_icon', $icon, $this->id);
    }
    /*
    * Get description text for Spotii option on checkout page
    */
    public function payment_fields(){

        $total = WC()->cart->total;
        $instalment = wc_price($total / 3);
        if(get_locale() == 'ar'){
            $timesch = 'جدول المدفوعات';
            $time = ['اليوم', 'الدفعة الثانية', 'الدفعة الثالثة', 'الدفعة الرابعة'];
            $total = 'المجموع: '.wc_price($total) ;
            $align = 'right';
        }else{
            $timesch = 'Payment Schedule';
            $time = ['Today', 'Second payment', 'Third payment', 'Fourth payment'];
            $total = 'Total : ' . wc_price($total) ;
            $align = 'left';
        }
        echo '
            <div class="spotii-cover" id="cover" style="text-align:\''.$align.'\';">
                <span class="spotii-payment-text" >'.$timesch .'</span>
                <div class="spotii-progressbar-container">
                    <div class="spotii-bar"></div>
                    <ul class="spotii-steps">
                            <span class="spotii-highlight">
                            <span class="spotii-installment-amount">' . $instalment . '</span>
                            <span class="spotii-time-period">' . $time[0] . '</span>
                            </span>
                            <span class="spotii-step">
                            <span class="spotii-installment-amount">' . $instalment . '</span>
                            <span class="spotii-time-period">' . $time[1] . '</span>
                            </span>
                            <span class="spotii-step">
                            <span class="spotii-installment-amount">' . $instalment . '</span>
                            <span class="spotii-time-period">' . $time[2] . '</span>
                            </span>
                    </ul>
                </div>
                <span class="spotii-grand-total">'.$total .' </span>
            </div>
            ';
    }
    /*
    * Process payments: magic begins here
    */
    public function process_payment($order_id){

        return processPayment($order_id, $this, "Shop Now Pay Later", "wc_spotii_gateway_shop_now_pay_later");
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
