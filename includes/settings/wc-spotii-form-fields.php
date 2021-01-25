<?php
/*
/* Form Fields 
*/
function form_fields($th, $type = null){

	$th->form_fields = array(
		'enabled' => array(
			'title' => 'Enable/Disable',
			'label' => $type == "Pay Now" ? "Spotii: Pay Now" : 'Spotii: Shop Now, Pay Later',
			'type' => 'checkbox',
			'description' => __('Don&rsquo;t have a Spotii Merchant account yet?', 'woocommerce') . ' ' . '<a href="https://dashboard.spotii.me/signup" target="_blank">' . __('Apply online today!', 'woocommerce') . '</a>',
			'default' => 'no',
		),
		'title' => array(
			'title' => 'Title',
			'type' => 'text',
			'description' => 'This controls the title that the user sees during checkout',
			'default' => $type == "Pay Now" ? "Spotii: Pay Now" : 'Spotii: Shop Now, Pay Later',
			'desc_tip' => true,
		),
		'description' => array(
			'title' => 'Description',
			'type' => 'textarea',
			'description' => 'This controls the description which the user sees during checkout',
			'default' => $type == "Pay Now" ? "Spotii: Pay Now" : 'Spotii: Shop Now, Pay Later',
		),
		'testmode' => array(
			'title' => 'Test Mode',
			'label' => 'Enable Test Mode',
			'type' => 'checkbox',
			'description' => 'Place the payment gateway in test mode using test API keys',
			'default' => 'no',
			'desc_tip' => false,
		),
		'add_aed_key' => array(
			'title' => 'Add keys for AED currency',
			'label' => 'Enable Keys',
			'type' => 'checkbox',
			'default' => 'no',
			'desc_tip' => false,
		),
		'public_key_test_aed' => array(
			'title' => 'Sandbox Public Key',
			'type' => 'text',
		),
		'private_key_test_aed' => array(
			'title' => 'Sandbox Private Key',
			'type' => 'password',
		),
		'public_key_live_aed' => array(
			'title' => 'Live Public Key',
			'type' => 'text',
		),
		'private_key_live_aed' => array(
			'title' => 'Live Private Key',
			'type' => 'password',
		),
		'add_sar_key' => array(
			'title' => 'Add keys for SAR currency',
			'label' => 'Enable Keys',
			'type' => 'checkbox',
			'default' => 'no',
			'desc_tip' => false,
		),
		'public_key_test_sar' => array(
			'title' => 'Sandbox Public Key',
			'type' => 'text',
		),
		'private_key_test_sar' => array(
			'title' => 'Sandbox Private Key',
			'type' => 'password',
		),
		'public_key_live_sar' => array(
			'title' => 'Live Public Key',
			'type' => 'text',
		),
		'private_key_live_sar' => array(
			'title' => 'Live Private Key',
			'type' => 'password',
		)
	);
	if($type != "Pay Now"){
	$widget = array(
		'order_minimum' => array(
			'title' => 'Order minimum',
			'type' => 'text',
			'default' => '200',
			'description' => 'The order minimum is set in AED'
		),
		'widget_theme' => array(
			'title' => 'Widget Theme',
			'type' => 'select',
			'default' => 'default',
			'options' => array(
				'default' => 'Default',
				'dark' => 'Dark'
		)),
		'widget_text' => array(
			'title' => 'Widget text',
			'type' => 'text',
			'description' => 'This is the text for the product and cart widget',
			'default' => "or \${number} interest-free payments of \${amount} with \${logo}\${info}"
		),
		'popup_learnMore_link' =>  array(
			'title' => 'The URL for Learn more button in the popup',
				'type' => 'text',
				'description' => __('The link here will be used inside the information pop-up for the "Learn more" button', 'woocommerce'),
				'default' => "https://www.spotii.me/how-it-works.html"
		),
		'show_custom_note_en' => array(
			'title' => 'Show custom note on pop up - English',
			'type' => 'text',
			'description' => __('This custom message will be showed in the pop-up', 'woocommerce')

		),
		'show_custom_note_ar' => array(
			'title' => 'Show custom note on pop up - Arabic ',
			'type' => 'text',
			'description' => __('This custom message will be showed in the pop-up', 'woocommerce')

			),
		'render_path_product' => array(
			'title' => 'Spotii product widget path',
			'type' => 'text',
			'default' => '#spotii-product-widget',
			'description' => __('Path of the element to which to render widget. For children, separate by space This path is relative to the corresponding price element. So, the first path in this will be relative to the price path in Price block selector. Second to the second and so on. Eg: If .product-price .price is the path to the price element and you want to put the widget in the 2nd parent of that element, you should use .product-price', 'woocommerce')

			)
			,
		'render_path_cart' => array(
			'title' => 'Spotii cart widget path',
			'type' => 'text',
			'default' => '#spotii-product-widget',
			'description' => __('Path of the element to which to render widget. For children, separate by space This path is relative to the corresponding price element. So, the first path in this will be relative to the price path in Price block selector. Second to the second and so on. Eg: If .product-price .price is the path to the price element and you want to put the widget in the 2nd parent of that element, you should use .product-price', 'woocommerce')

			)
		);
	   $th->form_fields+= $widget;
	}
}