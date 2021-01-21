<?php 
/*
/* add cart widget 
*/
function add_cart_widget($cart){
global $woocommerce;
$amount = $woocommerce->cart->cart_contents_total + $woocommerce->cart->tax_total;
$instal = wc_price($amount / 4);

  echo '<div onclick="render()"  style="line-height: 1.25; cursor: pointer; margin-top:1.0em; margin-bottom:1em;color: #555555;font-family:sans-serif;" class="cart-widget">
		   or 4 cost-free payments of 
		   <span class="spotii-price">'. $instal . '</span>
		</div>	
  ';
}