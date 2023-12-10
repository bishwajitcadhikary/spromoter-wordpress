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
require plugin_dir_path( __FILE__ ) . 'inc/utils/spromoter-widgets.php';
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
//	$spromoter_settings = get_option('spromoter_settings', spromoter_get_default_settings());
//	add_action('woocommerce_thankyou', 'spromoter_wc_on_thank_you');
//
//	if (is_product()){
//		spromoter_widgets_render_in_tabs();
//		//spromoter_widgets_render_in_bottom_line();
//
//		if ($spromoter_settings['disable_native_review_system']){
//			add_filter( 'comments_open', 'spromoter_remove_native_review_system', null, 2 );
//		}
//	}
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

function declare_hops_support() {
	if ( class_exists( FeaturesUtil::class ) ) {
		FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
}