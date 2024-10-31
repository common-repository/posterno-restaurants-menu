<?php
/**
 * Register actions for this addon.
 *
 * @package     posterno-restaurants-menu
 * @copyright   Copyright (c) 2020, Sematico, LTD
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

use Posterno\Restaurants\Helper;
use Posterno\Restaurants\Plugin;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Displays the content of the custom link within the dashboard.
 */
add_action(
	'pno_listings_dashboard_table_action_restaurant',
	function( $listing_id ) {

		Plugin::instance()->templates
			->set_template_data(
				array(
					'listing_id' => $listing_id,
				)
			)
			->get_template_part( 'dashboard-action-link' );

	}
);

/**
 * Displays the content of the "restaurant-menu" dashboard page.
 */
add_action(
	'pno_dashboard_tab_content_restaurant-menu',
	function() {

		Plugin::instance()->templates
			->get_template_part( 'restaurant-setup-page' );

	}
);

/**
 * Detect when the menus editor has been submitted and store it into the database.
 */
add_action(
	'init',
	function() {

		if ( ! isset( $_POST['save_restaurant_menus_nonce'] ) || ! wp_verify_nonce( $_POST['save_restaurant_menus_nonce'], 'saving_restaurant_menus_list' ) || ! is_user_logged_in() ) {
			return;
		}

		$listing_id = isset( $_GET['listing_id'] ) && ! empty( $_GET['listing_id'] ) ? absint( $_GET['listing_id'] ) : false;

		if ( ! Helper::can_user_setup_food_menu( get_current_user_id(), $listing_id ) ) {
			return;
		}

		$meta_key = Helper::get_restaurant_field_meta_key();

		if ( ! $meta_key ) {
			return;
		}

		$menus_to_save = array();

		$menu_groups = isset( $_POST['restaurant_menus'] ) && ! empty( $_POST['restaurant_menus'] ) ? json_decode( stripslashes( $_POST['restaurant_menus'] ), true ) : false;

		if ( ! empty( $menu_groups ) && is_array( $menu_groups ) ) {
			foreach ( $menu_groups as $menu_index => $menu ) {

				$group_name = isset( $menu['group_name'] ) ? sanitize_text_field( $menu['group_name'] ) : false;

				$existing_menu_items = Helper::get_food_items_data_for_form( $listing_id, $menu_index );

				$menus_to_save[] = array(
					'group_title' => $group_name,
					'menu_items'  => is_array( $existing_menu_items ) && ! empty( $existing_menu_items['fooditems'] ) ? pno_clean( $existing_menu_items['fooditems'] ) : array(),
				);

			}
		}

		if ( empty( $menus_to_save ) ) {
			carbon_set_post_meta( $listing_id, $meta_key, array() );
		} else {
			carbon_set_post_meta( $listing_id, $meta_key, $menus_to_save );
		}

		$redirect = add_query_arg(
			array(
				'listing_id' => $listing_id,
				'action'     => 'saved',
			),
			Helper::get_menu_setup_link( $listing_id )
		);

		wp_safe_redirect( $redirect );
		exit;

	}
);

/**
 * Detect when menu items are submitted through the form and store them into the database.
 */
add_action(
	'init',
	function() {

		if ( ! isset( $_POST['food_items_submission_nonce'] ) || ! wp_verify_nonce( $_POST['food_items_submission_nonce'], 'submitting_food_items' ) ) {
			return;
		}

		$listing_id = isset( $_GET['listing_id'] ) && ! empty( $_GET['listing_id'] ) ? absint( $_GET['listing_id'] ) : false;

		if ( ! Helper::can_user_setup_food_menu( get_current_user_id(), $listing_id ) ) {
			return;
		}

		$meta_key = Helper::get_restaurant_field_meta_key();

		if ( ! $meta_key ) {
			return;
		}

		$submitted_menu_items = isset( $_POST['restaurant_items'] ) && ! empty( $_POST['restaurant_items'] ) ? pno_clean( $_POST['restaurant_items'] ) : false;

		if ( is_array( $submitted_menu_items ) ) {

			$final_data = array();

			foreach ( $submitted_menu_items as $menu_group_index => $items ) {

				$group_name = key( $items );
				$items      = pno_clean( json_decode( stripslashes( $items[ $group_name ] ), true ) );

				$final_data[] = array(
					'group_title' => $group_name,
					'menu_items'  => $items,
				);

			}

			carbon_set_post_meta( $listing_id, $meta_key, $final_data );

		}

		$redirect = add_query_arg(
			array(
				'listing_id' => $listing_id,
				'action'     => 'saved',
			),
			Helper::get_menu_setup_link( $listing_id )
		);

		wp_safe_redirect( $redirect );
		exit;

	}
);
