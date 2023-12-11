<?php

function spromoter_admin_settings()
{
	add_action('admin_print_styles', 'spromoter_admin_styles');
	add_action('admin_print_scripts', 'spromoter_admin_scripts');
	add_menu_page('SPromoter', 'SPromoter', 'manage_options', 'spromoter-settings-page', 'spromoter_display_admin_page', spromoter_get_image_url('small-logo.jpg'));
}

function spromoter_get_settings()
{
	$default_settings = spromoter_get_default_settings();
	$settings = get_option('spromoter_settings', $default_settings);

	return array_merge($default_settings, $settings);
}

function spromoter_compatible() {
	return version_compare(phpversion(), '5.2.0') >= 0 && function_exists('curl_init');
}

function spromoter_get_image_url($image_name)
{
	return plugins_url('../../assets/images/' . $image_name, __FILE__);
}

/**
 * Get Product data
 *
 * @param $product
 *
 * @return array
 */
function spromoter_get_product_data($product) {
	$settings = get_option('spromoter_settings',spromoter_get_default_settings());
	$product_data = array(
		'app_id' => esc_attr($settings['app_id']),
		'shop_domain' => esc_attr(parse_url(get_bloginfo('url'),PHP_URL_HOST)),
		'url' => esc_attr(get_permalink($product->get_id())),
		'lang' => esc_attr('en'),
		'description' => esc_attr(wp_strip_all_tags($product->get_description())),
		'id' => esc_attr($product->get_id()),
		'title' => esc_attr($product->get_title()),
		'image-url' => esc_attr(wp_get_attachment_url(get_post_thumbnail_id($product->get_id())))
	);

	//if($settings['yotpo_language_as_site'] == true) {
	$lang = explode('-', get_bloginfo('language'));
	if(strlen($lang[0]) == 2) {
		$product_data['lang'] = $lang[0];
	}
	//}
	$specs_data = get_specs_data($product);
	if(!empty($specs_data)){ $product_data['specs'] = $specs_data;  }

	return $product_data;
}

/**
 * Get product specs data
 *
 * @param $product
 *
 * @return array
 */
function get_specs_data($product) {
	$specs_data = array();
	if($product->get_sku()){ $specs_data['external_sku'] =$product->get_sku();}
	if($product->get_attribute('upc')){ $specs_data['upc'] =$product->get_attribute('upc');}
	if($product->get_attribute('isbn')){ $specs_data['isbn'] = $product->get_attribute('isbn');}
	if($product->get_attribute('brand')){ $specs_data['brand'] = $product->get_attribute('brand');}
	if($product->get_attribute('mpn')){ $specs_data['mpn'] =$product->get_attribute('mpn');}
	return $specs_data;
}

/**
 * Track order conversion
 *
 * @param $order_id
 *
 * @return void
 */
function spromoter_wc_on_thank_you( $order_id ) {
	$settings = spromoter_get_settings();
	$order    = new WC_Order( $order_id );

	// TODO: Add conversion tracking logic here
}

/**
 * Get the order currency
 *
 * @param $order
 *
 * @return mixed|string
 */
function spromoter_get_order_currency( $order ) {
	if ( is_null( $order ) || ! is_object( $order ) ) {
		return '';
	}
	if ( method_exists( $order, 'get_currency' ) ) {
		return $order->get_currency();
	}
	if ( isset( $order->order_custom_fields ) && isset( $order->order_custom_fields['_order_currency'] ) ) {
		if ( is_array( $order->order_custom_fields['_order_currency'] ) ) {
			return $order->order_custom_fields['_order_currency'][0];
		}
	}

	return '';
}

function spromoter_get_product_image_url($product_id) {
	return wp_get_attachment_url(get_post_thumbnail_id($product_id));
}

function spromoter_debug($msg, $name = '', $date = true) {
	if (!spromoter_get_settings()['debug_mode']) {
		return;
	}
	$name = $name ?: debug_backtrace()[1]['function'];
	$error_dir = plugin_dir_path(__FILE__) . 'spromoter_debug.log';
	$msg = print_r($msg, true);
	$log = ($date ? "[" . date('m/d/Y @ g:i:sA', time()) . "] " : "") . $name . ' ' . $msg . "\n";
	file_put_contents($error_dir, $log, FILE_APPEND);
}