<?php



function spromoter_admin_settings()
{
	add_action('admin_print_styles', 'spromoter_admin_styles');
	add_action('admin_print_scripts', 'spromoter_admin_scripts');
	add_menu_page('SPromoter', 'SPromoter', 'manage_options', 'spromoter-settings-page', 'spromoter_display_admin_page', get_spromoter_small_logo_url());
}