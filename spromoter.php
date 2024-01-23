<?php

/*
Plugin Name: SPromoter
Description: A simple plugin to manage reviews and ratings for your products.
Version: 1.0.0
Author: SPromoter
Author URI:  https://spromoter.com
Text Domain: spromoter
License: GPLv2 or later
*/

use Automattic\WooCommerce\Utilities\FeaturesUtil;
use plugins\spromoter\classes\SPromoterReviewExport;
use plugins\spromoter\inc\Api\SpromoterApi;

register_activation_hook(__FILE__, 'spromoter_activate');
register_deactivation_hook(__FILE__, 'spromoter_deactivate');
register_uninstall_hook(__FILE__, 'spromoter_uninstall');
add_action('plugins_loaded', 'spromoter_init');
add_action('before_woocommerce_init', 'declare_hops_support');
add_action('woocommerce_order_status_changed', 'spromoter_wc_on_order_status_changed');

require plugin_dir_path( __FILE__ ) . 'inc/utils/spromoter-defaults.php';
require plugin_dir_path( __FILE__ ) . 'inc/utils/spromoter-functions.php';
require plugin_dir_path( __FILE__ ) . 'inc/utils/spromoter-widgets.php';
require plugin_dir_path( __FILE__ ) . 'inc/Api/SpromoterApi.php';

function spromoter_init()
{
	$is_admin = is_admin();
	$spromoter_settings = get_option('spromoter_settings', spromoter_get_default_settings());

	if ($is_admin) {
		// Export reviews
		if (isset($_POST['export_reviews']) && $_POST['export_reviews']) {
			require plugin_dir_path(__FILE__) . 'classes/class-spromoter-export-reviews.php';
			$exporter = new SPromoterReviewExport();
			list($file_name, $error) = $exporter->exportReviews();
			if (is_null($error)){
				$exporter->downloadReviews($file_name);
			}
			exit();
		}

		require ( plugin_dir_path( __FILE__ ) . 'templates/spromoter-settings.php' );
		add_action('admin_menu', 'spromoter_admin_settings');

		if (spromoter_compatible() && !empty($spromoter_settings['api_key']) && !empty($spromoter_settings['app_id'])){
			add_action('wp_enqueue_styles', 'spromoter_admin_styles');
			add_action('wp_enqueue_scripts', 'spromoter_admin_scripts');
		}
	}elseif(spromoter_compatible() && !empty($spromoter_settings['api_key']) && !empty($spromoter_settings['app_id'])) {
        add_action('template_redirect', 'spromoter_frontend_init');
    }
}

function spromoter_frontend_init()
{
	$spromoter_settings = spromoter_get_settings();
	add_action('woocommerce_thankyou', 'spromoter_wc_on_thank_you');

	if (is_product()){
        if ($spromoter_settings['disable_native_review_system']){
            add_filter( 'comments_open', 'spromoter_remove_native_review_system', null, 2 );

            if ($spromoter_settings['review_show_in'] == 'tab') {
                spromoter_widgets_render_in_tabs();
            } elseif ($spromoter_settings['review_show_in'] == 'footer') {
                spromoter_widgets_render_in_footer();
            }

            spromoter_widgets_render_in_bottom_line();


            add_action('wp_enqueue_scripts', 'spromoter_front_styles');
            add_action('wp_enqueue_scripts', 'spromoter_front_scripts');
		}
	}
}

function spromoter_wc_on_order_status_changed($order_id){
	do_action('woocommerce_init');

	$order = wc_get_order($order_id);
	$orderStatus = $order->get_status();

	$settings = spromoter_get_settings();

	if ($orderStatus == $settings['order_status']) {
        $apiKey = $settings['api_key'];
        $appId = $settings['app_id'];

        if (!empty($apiKey) && !empty($appId) && spromoter_compatible()) {
            require plugin_dir_path(__FILE__) . 'classes/class-spromoter-export-reviews.php';

            $spromoter = new SpromoterApi();

            $orderData = [
                'customer_name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                'customer_email' => $order->get_billing_email(),
                'order_id' => "$order_id",
                'order_date' => $order->get_date_created()->format('Y-m-d H:i:s'),
                'currency' => $order->get_currency(),
                'status' => $orderStatus,
                'total' => $order->get_total(),
                'data' => $order->get_data(),
                'platform' => 'woocommerce',
            ];


            $items = array();
            foreach ($order->get_items() as $item) {
                $product = wc_get_product($item['product_id']);
                $productId = $product->get_id();
                $items[] = array(
                    'id' => "$productId",
                    'name' => $product->get_name(),
                    'image' => spromoter_get_product_image_url($product->get_id()),
                    'url' => $product->get_permalink(),
                    'description' => wp_strip_all_tags($product->get_description()),
                    'lang' => get_locale(),
                    'price' => $product->get_price(),
                    'quantity' => $item['quantity'],
                    'specs' => array(
                        'sku' => $product->get_sku(),
                        'upc' => $product->get_attribute('upc'),
                        'ean' => $product->get_attribute('ean'),
                        'isbn' => $product->get_attribute('isbn'),
                        'asin' => $product->get_attribute('asin'),
                        'gtin' => $product->get_attribute('gtin'),
                        'mpn' => $product->get_attribute('mpn'),
                        'brand' => $product->get_attribute('brand'),
                    )
                );
            }

            $orderData['items'] = $items;

            $response = $spromoter->createOrder($orderData);

            if (!$response['status']) {
                spromoter_debug($response['message'] . '::' . json_encode($response['errors']), 'spromoter_wc_on_order_status_changed');
            }

        }
    }
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
        delete_option('spromoter_settings');
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
}

function spromoter_front_scripts()
{
    if (class_exists('woocommerce')){
        $settings = spromoter_get_settings();
        wp_enqueue_script('spromoter-lightbox-scripts', 'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js');
        wp_enqueue_script('spromoter-front-scripts', plugins_url('assets/js/spromoter-front.js', __FILE__));

        wp_localize_script('spromoter-front-scripts', 'spromoterSettings', array(
            'app_id' => $settings['app_id'],
            'bottom_line' => $settings['show_bottom_line_widget'],
        ));
    }
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