<?php 
/*
/* add cart widget 
*/
function add_cart_widget($cart){
$th = new WC_Spotii_Gateway_Shop_Now_Pay_Later;
global $woocommerce;
$instal = $woocommerce->cart->cart_contents_total + $woocommerce->cart->tax_total;
$curr = get_woocommerce_currency_symbol();
$currency = get_woocommerce_currency();
$th = new WC_Spotii_Gateway_Shop_Now_Pay_Later;
$widget_text=$th->widget_text;
$url = $th->popup_link;
$custom_note_ar = $th->show_custom_note_ar;
$custom_note_en = $th->show_custom_note_en;
$theme = $th->widget_theme;
$render = $th->render_path_cart;

if(get_locale() == 'ar'){
  echo '<div id="spotii-product-widget">'.
  '</div><div id="spotii-product-widget-price" style="display:none;">'. $instal . '</div>'.
  '<script>window.spotiiConfig = {targetXPath: [\'#spotii-product-widget-price\'], renderToPath: [\''.$render.'\'],currency: "'.$currency.'",theme:"'.$theme.'",howItWorksURL : "'.$url.'",minNote:"'.$custom_note_ar.'"};</script>'.
  ' <script>(function(w,d,s) {var f=d.getElementsByTagName(s)[0];var a=d.createElement(\'script\');a.async=true;a.src=\'https://widget.spotii.me/v1/javascript/priceWidget-ar.js\';f.parentNode.insertBefore(a,f);}(window, document, \'script\'));</script> ';
	  
}else {
  echo '<div id="spotii-product-widget">'.
	'</div><div id="spotii-product-widget-price" style="display:none;">'. $instal . '</div>'.
	'<script>window.spotiiConfig = {targetXPath: [\'#spotii-product-widget-price\'], renderToPath: [\''.$render.'\'],currency: "'.$currency.'",templateLine:"'.$widget_text.'",theme:"'.$theme.'",minNote:"'.$custom_note_en.'",howItWorksURL : "'.$url.'",};</script>'.
	' <script>(function(w,d,s) {var f=d.getElementsByTagName(s)[0];var a=d.createElement(\'script\');a.async=true;a.src=\'https://widget.spotii.me/v1/javascript/priceWidget-en.js\';f.parentNode.insertBefore(a,f);}(window, document, \'script\'));</script> ';
		}
}