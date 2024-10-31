<?php
/**
 * Register assets for this addon.
 *
 * @package     posterno-restaurants-menu
 * @copyright   Copyright (c) 2020, Sematico, LTD
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Load the scripts required for the restaurant menu input.
 *
 * @return void
 */
function pno_restaurants_frontend_scripts() {

	if ( ! is_page( pno_get_dashboard_page_id() ) ) {
		return;
	}

	wp_register_script( 'pno-restaurants-input', PNO_RESTAURANTS_PLUGIN_URL . 'dist/js/app.js', array(), PNO_RESTAURANTS_VERSION, false );

	wp_enqueue_script( 'pno-restaurants-input' );

}
add_action( 'wp_enqueue_scripts', 'pno_restaurants_frontend_scripts' );
