<?php 
/*
/* add product widget 
*/
function add_product_widget(){
	global $product;
  $instal = wc_price($product->get_price() / 4);
  $curr = get_woocommerce_currency_symbol();
 

  echo '<div onclick="render()"  style="line-height: 1.25; cursor: pointer; margin-top:1.0em; margin-bottom:1em;color: #555555;font-family:sans-serif;">
		   or 4 cost-free payments of 
		   <span class="spotii-price">'. $instal . '</span>
		   <script>
		   jQuery(document).ready(function() {

			jQuery( ".variations_form" ).each( function() {
		
				jQuery(this).on( "found_variation", function( event, variation ) {
		
					console.log(variation);//all details here
		
					var price = variation.display_price;//selectedprice
		
					var instaPrice = parseFloat(price/4).toFixed(2);
		
					var currency = "'.$curr.'";
		
					console.log(price,instaPrice);
		
					jQuery(".spotii-price").html(instaPrice+" "+currency);
		
				});
		
			});
		
		   });
		 
		   </script>
		</div>	
  ';
}