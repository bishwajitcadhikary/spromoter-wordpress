<?php

require __DIR__.'/../inc/utils/spromoter-settings-functions.php';

function spromoter_display_admin_page(){
	if (function_exists('current_user_can') && !current_user_can('manage_options')) {
		die(__(''));
	}

	if (spromoter_compatible()){
		$spromoter = get_option('spromoter_settings', spromoter_get_default_settings());

		if (empty($spromoter['app_id']) && empty($spromoter['api_key'])){
			if (isset($_POST['page_type']) && $_POST['page_type'] == 'settings') {
				spromoter_save_settings();
				spromoter_display_settings_page();
			} elseif ($_POST['page_type'] == 'login'){
				spromoter_display_settings_page();
			}else{
				spromoter_display_register_page();
			}
		}else{
			if (isset($_POST['page_type']) && $_POST['page_type'] == 'settings') {
				spromoter_save_settings();
			}
			spromoter_display_settings_page();
		}
	} else{
		if (version_compare(phpversion(), '5.2.0') < 0){
			echo '<div class="error"><p>' . __('SPromoter requires PHP 5.2.0 or higher. Please upgrade PHP to use this plugin.') . '</p></div>';
		}

		if (!function_exists('curl_init')){
			echo '<div class="error"><p>' . __('SPromoter requires the CURL PHP extension. Please install or enable CURL.') . '</p></div>';
		}
	}
}

function spromoter_display_messages($messages = array(), $type = false){
	$class = $type ? 'error' : 'updated fade show';
	if (is_array($messages)) {
		foreach ($messages as $message) {
			echo "<div id='message' class='$class'><p><strong>$message</strong></p></div>";
		}
	} elseif (is_string($messages)) {
		echo "<div id='message' class='$class'><p><strong>$messages</strong></p></div>";
	}
}