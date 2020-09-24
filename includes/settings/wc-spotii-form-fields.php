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
			'description' => __('Don&rsquo;t have a Spotii Merchant account yet?', 'woocommerce') . ' ' . '<a href="https://dashboard.dev.spotii.me/signup" target="_blank">' . __('Apply online today!', 'woocommerce') . '</a>',
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
}