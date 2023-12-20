<?php

function spromoter_widgets_render_in_tabs() {
	add_action('woocommerce_product_tabs', 'spromoter_show_main_widget_in_tab');
}

function spromoter_show_main_widget_in_tab($tabs) {
	global $product;
	if ( $product->get_reviews_allowed() ) {
		$spromoter_settings = spromoter_get_settings();

		$tabs['spromoter_main_widget'] = array(
			'title'    => esc_html__( 'Reviews', 'spromoter' ),
			'priority' => 50,
			'callback' => 'spromoter_show_reviews_widget'
		);
	}

	return $tabs;
}

function spromoter_show_reviews_widget() {
	global $product;

	$product_data = spromoter_get_product_data($product);

	echo "<div 
			class='spromoter-container'
			data-spromoter-app-id='" .$product_data['app_id']. "'
			data-spromoter-product-id='" .$product_data['id']. "'
			data-spromoter-product-title='" .$product_data['title']. "'
			data-spromoter-product-image-url='" .$product_data['image-url']. "'
			data-spromoter-product-url='" .$product_data['url']. "'
			data-spromoter-product-description='" .$product_data['description']. "'
			data-spromoter-product-lang='" .$product_data['lang']. "'
			data-spromoter-product-shop-domain='" .$product_data['shop_domain']. "'
			data-spromoter-product-app-id='" .$product_data['app_id']. "'
			data-spromoter-product-specs='" .json_encode($product_data['specs']). "'
			
			>
			<div>
			    <button type='button' id='spromoter-write-review-button'>Write Review</button>
            </div>
			<div id='spromoter-reviews-form'>			
            </div>
		    <div id='spromoterReviews'></div>
		</div>";
}

function spromoter_widgets_render_in_bottom_line(){
	add_action('woocommerce_single_product_summary', 'spromoter_show_main_widget_in_bottom_line');
}

function spromoter_show_main_widget_in_bottom_line(){
	global $product;
	if ( $product->get_reviews_allowed() ) {
		$spromoter_settings = spromoter_get_settings();
		$product_data = spromoter_get_product_data($product);

		echo "<script>
		       jQuery(document).ready(function() {
			       jQuery('div.spromoter-bottom-line').click(function() {
				       if (jQuery('li.spromoter_main_widget_tab>a').length) { jQuery('li.spromoter_main_widget_tab>a').click(); }
			       })
			})
		</script>
		<div class='spromoter-bottom-line' 
			data-product-id='".$product_data['id']."'
			data-url='".$product_data['url']."' 
			data-lang='".$product_data['lang']."'
			                                ></div>";
	}
}