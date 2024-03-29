<?php

require __DIR__.'/spromoter-settings-functions.php';

function spromoter_display_admin_page(){
	if (function_exists('current_user_can') && !current_user_can('manage_options')) {
		die(__('You do not have sufficient permissions to access this page.'));
	}

	// Check if SPromoter is compatible
	if (spromoter_compatible()) {
		$spromoter = spromoter_get_settings();

		// Check if app_id and api_key are empty
		if (empty($spromoter['app_id']) && empty($spromoter['api_key'])) {
			// Check if page_type is set in POST
           if (isset($_POST['page_type'])) {
				// Handle settings and registration based on page_type
				if ($_POST['page_type'] == 'settings') {
					spromoter_save_settings();
					spromoter_display_settings_page();
				} elseif ($_POST['page_type'] == 'register') {
					if (spromoter_register_user()){
                        spromoter_display_settings_page();
                    }else{
                        spromoter_display_register_page();
                    }
				}elseif ($_POST['page_type'] == 'login') {
                    if (spromoter_login_user() && $_POST['app_id'] && $_POST['api_key']){
                        spromoter_display_settings_page();
                    }else{
                        spromoter_display_login_page();
                    }
                }else{
                    spromoter_display_login_page();
                }
			}  else {
				// Display registration page if page_type is not set
				spromoter_display_register_page();
			}
		} else {
			// Save settings if page_type is set to 'settings'
			if (isset($_POST['page_type']) && $_POST['page_type'] == 'settings') {
				spromoter_save_settings();
			}

            if (isset($_POST['submit_past_orders'])){
                spromoter_send_past_orders();
            }

			// Always display settings page
			spromoter_display_settings_page();
		}
	} else {
		// Check PHP version requirement
		if (version_compare(phpversion(), '5.2.0') < 0) {
			echo '<div class="error"><p>' . __('SPromoter requires PHP 5.2.0 or higher. Please upgrade PHP to use this plugin.') . '</p></div>';
		}

		// Check for the existence of CURL extension
		if (!function_exists('curl_init')) {
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