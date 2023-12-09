<?php

/*
Plugin Name: SPromoter
Description: A simple plugin to manage reviews and ratings for your products.
Version: 1.0
Author: Bishwajit Adhikary
Author URI: https://github.com/bishwajitcadhikary
Text Domain: spromoter
License: GPLv2 or later
*/

use Automattic\WooCommerce\Utilities\FeaturesUtil;
use Spromoter\Api\SpromoterApi;

register_activation_hook(__FILE__, 'spromoter_activate');
register_deactivation_hook(__FILE__, 'spromoter_deactivate');
register_uninstall_hook(__FILE__, 'spromoter_uninstall');
add_action('plugins_loaded', 'spromoter_init');
add_action('before_woocommerce_init', 'declare_hops_support');

require plugin_dir_path( __FILE__ ) . 'inc/utils/spromoter-defaults.php';
require plugin_dir_path( __FILE__ ) . 'inc/utils/spromoter-functions.php';
require plugin_dir_path( __FILE__ ) . 'inc/Api/SpromoterApi.php';


function spromoter_init()
{
	$is_admin = is_admin();
	$spromoter_settings = get_option('spromoter_settings', spromoter_get_default_settings());

	if ($is_admin) {

		require ( plugin_dir_path( __FILE__ ) . 'templates/spromoter-settings.php' );
		add_action('admin_menu', 'spromoter_admin_settings');

		if (spromoter_compatible() && !empty($spromoter_settings['api_key']) && !empty($spromoter_settings['app_id'])){
			add_action('wp_enqueue_styles', 'spromoter_admin_styles');
			add_action('wp_enqueue_scripts', 'spromoter_admin_scripts');
		}
	}elseif(spromoter_compatible() && !empty($spromoter_settings['api_key']) && !empty($spromoter_settings['app_id'])){
		add_action('template_redirect', 'spromoter_frontend_init');
		
		add_action('wp_enqueue_scripts', 'spromoter_front_styles');
		add_action('wp_enqueue_scripts', 'spromoter_front_scripts');
	}
}

function spromoter_frontend_init()
{
	$spromoter_settings = get_option('spromoter_settings', spromoter_get_default_settings());
	add_action('woocommerce_thankyou', 'spromoter_conversion_track');

	if (is_product()){
		spromoter_widgets_render_in_tabs();

		if ($spromoter_settings['disable_native_review_system']){
			add_filter( 'comments_open', 'spromoter_remove_native_review_system', null, 2 );
		}
	}
}

function spromoter_widgets_render_in_tabs() {
	add_action('woocommerce_product_tabs', 'spromoter_show_main_widget_in_tab');
}

function spromoter_show_main_widget_in_tab($tabs) {
	global $product;
	if ( $product->get_reviews_allowed() == true ) {
		$spromoter_settings = get_option('spromoter_settings', spromoter_get_default_settings());
		$tabs['spromoter_main_widget'] = array(
			'title'    => 'Reviews', // TODO: add dynamic title
			'priority' => 50,
			'callback' => 'spromoter_show_reviews_widget'
		);

		return $tabs;
	}
}

function spromoter_show_reviews_widget() {
	global $product;	

	if ( $product->get_reviews_allowed() == true ) {
		$product_data = spromoter_get_product_data($product);

		echo "<div class='spromoter-container'>
			<div id='spromoterReviews'></div>
			<div id='spromoter-reviews-form'></div>
		</div>";
	}
}

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

function get_specs_data($product) {
	$specs_data = array();
	if($product->get_sku()){ $specs_data['external_sku'] =$product->get_sku();}
	if($product->get_attribute('upc')){ $specs_data['upc'] =$product->get_attribute('upc');}
	if($product->get_attribute('isbn')){ $specs_data['isbn'] = $product->get_attribute('isbn');}
	if($product->get_attribute('brand')){ $specs_data['brand'] = $product->get_attribute('brand');}
	if($product->get_attribute('mpn')){ $specs_data['mpn'] =$product->get_attribute('mpn');}
	return $specs_data;
}

function spromoter_conversion_track( $order_id ) {
	$yotpo_settings = get_option( 'spromoter_settings', spromoter_get_default_settings() );
	$order          = new WC_Order( $order_id );

	// TODO: Add conversion tracking logic here
}

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

function spromoter_activate()
{
	if (current_user_can('activate_plugins')) {
		update_option('spromoter_recently_activated', true);
	}
}

function spromoter_deactivate()
{
	if (current_user_can('activate_plugins')) {
		// TODO: Add deactivation logic here
	}
}

function spromoter_uninstall()
{
	if (current_user_can('activate_plugins') && __FILE__ == WP_UNINSTALL_PLUGIN) {
		check_admin_referer( 'bulk-plugins' );
		delete_option('spromoter_settings');
	}
}

function spromoter_admin_styles()
{
	wp_enqueue_style('spromoter-main-styles', plugins_url('assets/css/spromoter-main.css', __FILE__));
	wp_enqueue_style('spromoter-auth-styles', plugins_url('assets/css/spromoter-auth.css', __FILE__));
}

function spromoter_admin_scripts()
{
	wp_enqueue_script('spromoter-main-scripts', plugins_url('assets/js/spromoter-main.js', __FILE__));
}

function spromoter_front_styles()
{
	wp_enqueue_style('spromoter-main-styles', plugins_url('assets/css/spromoter-main.css', __FILE__));
	wp_enqueue_style('spromoter-front-styles', plugins_url('assets/css/spromoter-front.css', __FILE__));
}

function spromoter_front_scripts()
{
	wp_enqueue_script('spromoter-front-scripts', plugins_url('assets/js/spromoter-front.js', __FILE__));
}

function spromoter_remove_native_review_system( $open, $post_id ) {
	if ( get_post_type( $post_id ) == 'product' ) {
		return false;
	}

	return $open;
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

function spromoter_get_logo_url()
{
	return plugins_url('assets/images/logo.png', __FILE__);
}

function spromoter_get_small_logo_url()
{
	return plugins_url('assets/images/small-logo.jpg', __FILE__);
}

function spromoter_background_shape_url()
{
	return plugins_url('assets/images/shape.png', __FILE__);
}

function declare_hops_support() {
	if ( class_exists( FeaturesUtil::class ) ) {
		FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
}