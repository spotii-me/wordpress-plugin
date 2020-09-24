<?php
/*
/* Currency Converter
*/
function check_amount($spotii_amount, $spotii_currency, $merchant_amount, $merchant_currency){
    try {

        if ($spotii_currency != $merchant_currency) {
            if ($spotii_currency == "AED") {
                switch ($merchant_currency) {
                    case "USD":
                        $merchant_amount = $merchant_amount * 3.6730;
                        break;
                    case "SAR":
                        $merchant_amount = $merchant_amount * 0.9604;
                        break;
                }
            }
            if (abs(($spotii_amount - $merchant_amount)) < 5) {
                return true;
            }
        } else if ($spotii_amount == $merchant_amount) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {

        wc_add_notice(__('Checkout Error: ', 'woothemes') . "Amount from Spotii doesn't match amount from merchant. Please try again", 'error');
        error_log("Error on amount match " . $e->getMessage());
    }
}
/*
/* validate Currency
*/
function validate_curr($curr){

    if ($curr == "AED" || $curr == "SAR" || $curr == "USD") {
        return true;
    } else {
        return false;
    }
}