<?php

namespace Spromoter\Api;

class SpromoterApi {
	protected $api_key = '';
	protected $app_id = '';
	protected $api_url = 'http://spromoter.test/api/v1/';

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

		// Check response
		$response = json_decode($result, true);

		if ($response['status'] == 'success') {
			return $response['data'];
		} else {
			return false;
		}
	}

	public function createOrder( $data = array() ) {

		$result = $this->sendRequest('orders/'. $this->app_id, 'POST', $data);

		$response = json_decode($result, true);
		if ($response['status'] == 'success') {
			return true;
		} else {
			return false;
		}
	}
}