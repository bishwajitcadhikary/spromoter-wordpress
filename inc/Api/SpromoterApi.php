<?php

namespace plugins\spromoter\inc\Api;

class SpromoterApi {
	protected $api_key = '';
	protected $app_id = '';
	protected $api_url = 'http://api.spromoter.test/v1/';

	protected $headers = array();

	public function __construct() {
		$spromoter_settings = spromoter_get_settings();

		$this->api_key = $spromoter_settings['api_key'];
		$this->app_id = $spromoter_settings['app_id'];
		$this->headers = array(
			'Authorization: Bearer ' . $this->api_key,
			'Content-Type: application/json',
			'Accept: application/json'
		);
	}

	public function sendRequest( $endpoint, $method = 'GET', $body = array() ) {
		$ch = curl_init();

		if ($method == 'GET') {
			$endpoint .= '?' . http_build_query($body);
		}

		curl_setopt($ch, CURLOPT_URL, $this->api_url . $endpoint);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // TODO: Remove this (for testing only)

		if ($method == 'POST') {
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
			curl_setopt($ch, CURLOPT_POST, 1);
		}

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			return false;
		}

		curl_close($ch);

		return $result;
	}

	public function checkCredentials() {
		// Send request to SPromoter API
		$result = $this->sendRequest('check-credentials', 'POST', array(
			'app_id' => $this->app_id,
		));

		// Check response
		$response = json_decode($result, true);
		if ($response['status'] == 'success') {
			return true;
		} else {
			return false;
		}
	}

	public function registerUser( $data = array() ) {
		// Send request to SPromoter API
		$result = $this->sendRequest('auth/register', 'POST', $data);

		return json_decode($result, true);
	}

	public function verifyUser( $data = array() ) {
		// Send request to SPromoter API
		$result = $this->sendRequest('auth/check-credentials', 'POST', $data);

		return json_decode($result, true);
	}

	public function createOrder( $data = array() ) {

		$result = $this->sendRequest('orders/'. $this->app_id, 'POST', $data);

		return json_decode($result, true);
	}

    public function sendPastOrders()
    {
        $settings = spromoter_get_settings();
        if (defined('WC_VERSION') && version_compare(WC_VERSION, '3.0.0', '>=')) {
            $orders = $this->prepareOrders($settings);
        } else {
            $orders = $this->prepareOrdersLegacy();
        }

        dd($orders);
    }

    private function prepareOrders($settings)
    {
        $configuredAt = $settings['configured_at'] ? strtotime($settings['configured_at']) : time();
        $orders = wc_get_orders(array(
            'limit' => -1,
            'status' => array('completed', 'processing'),
            'type' => 'shop_order',
            'date_created' => '<=' . date('Y-m-d', $configuredAt)
        ));

        $data = array();
        foreach ($orders as $order) {
            $data[] = array(
                $order_id = $order->get_id(),
                'customer_name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                'customer_email' => $order->get_billing_email(),
                'order_id' => "$order_id",
                'order_date' => $order->get_date_created()->format('Y-m-d H:i:s'),
                'currency' => $order->get_currency(),
                'status' => $order->get_status(),
                'total' => $order->get_total(),
                'data' => $order->get_data(),
                'platform' => 'woocommerce',
                'items' => $this->prepareOrderItems($order->get_items())
            );
        }

        return $data;
    }

    private function prepareOrdersLegacy()
    {
        global $wpdb;

        $orders = $wpdb->get_results("
            SELECT * FROM {$wpdb->prefix}posts
            WHERE post_type = 'shop_order'
            AND post_status IN ('wc-completed', 'wc-processing')
            AND post_date >= '" . date('Y-m-d', strtotime('-1 month')) . "'
        ");

        $data = array();
        foreach ($orders as $order) {
            $order = new WC_Order($order->ID);
            $data[] = array(
                $order_id = $order->id,
                'customer_name' => $order->billing_first_name . ' ' . $order->billing_last_name,
                'customer_email' => $order->billing_email,
                'order_id' => "$order_id",
                'order_date' => $order->order_date,
                'currency' => $order->order_currency,
                'status' => $order->status,
                'total' => $order->order_total,
                'data' => $order->get_data(),
                'platform' => 'woocommerce',
                'items' => $this->prepareOrderItems($order->get_items())
            );
        }

        return $data;
    }

    private function prepareOrderItems(array $get_items)
    {
        $items = array();
        foreach ($get_items as $item) {
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

        return $items;
    }
}