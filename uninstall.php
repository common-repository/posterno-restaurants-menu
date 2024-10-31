<?php
/**
 * Uninstall addon
 *
 * @package     posterno-restaurants-menu
 * @copyright   Copyright (c) 2020, Sematico LTD
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'pno_restaurants_version' );
delete_option( 'pno_restaurants_version_upgraded_from' );
