<?php

function spromoter_get_default_settings(){
	return array(
		'app_id' => '',
		'api_key' => '',
		'order_status' => 'completed',
		'review_show_in' => 'tab',
		'disable_native_review_system' => true,
		'show_bottom_line_widget' => true,
        'debug_mode' => true
	);
}