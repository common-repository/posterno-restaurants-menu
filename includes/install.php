<?php
/**
 * Addon installation.
 *
 * @package     posterno-restaurants-menu
 * @copyright   Copyright (c) 2020, Sematico, LTD
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Runs on plugin install.
 *
 * @param boolean $network_wide whether the plugin is being activated network wide or not.
 * @return void
 */
function pno_restaurants_install( $network_wide = false ) {
	global $wpdb;
	if ( is_multisite() && $network_wide ) {
		foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs LIMIT 100" ) as $blog_id ) {
			switch_to_blog( $blog_id );
			pno_restaurants_run_install();
			restore_current_blog();
		}
	} else {
		pno_restaurants_run_install();
	}
}

/**
 * Run the installation process of the plugin.
 *
 * @return void
 */
function pno_restaurants_run_install() {

	// Add Upgraded From Option.
	$current_version = get_option( 'pno_restaurants_version' );

	if ( $current_version ) {
		update_option( 'pno_restaurants_version_upgraded_from', $current_version );
	}

	// Update current version.
	update_option( 'pno_restaurants_version', PNO_RESTAURANTS_VERSION );

}
