<?php
/**
 * Register filters for this addon.
 *
 * @package     posterno-restaurants-menu
 * @copyright   Copyright (c) 2020, Sematico, LTD
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

use Carbon_Fields\Field;

/**
 * Register a new field type.
 */
add_filter(
	'pno_registered_field_types',
	function( $types ) {

		$types['restaurant'] = esc_html__( 'Restaurant menu', 'posterno-restaurants-menu' );

		return $types;

	}
);

/**
 * Tell Posterno that a custom field definition is available for the restaurant field type.
 */
add_filter(
	'pno_custom_listings_carbon_field_type',
	function( $pass, $type ) {

		if ( $type === 'restaurant' ) {
			return true;
		}

		return $pass;

	},
	10,
	2
);

/**
 * Prepare the custom field configuration when a restaurant field is created.
 */
add_filter(
	'pno_custom_listings_carbon_field_type_definition',
	function( $definition, $type, $field ) {

		if ( $type === 'restaurant' ) {
			$definition = Field::make( 'complex', $field->getObjectMetaKey(), $field->getTitle() )
			->set_datastore( new \PNO\Datastores\SerializeComplexField() )
			->add_fields(
				array(
					Field::make( 'text', 'group_title', esc_html__( 'Menu group title', 'posterno-restaurants-menu' ) )->set_help_text( esc_html__( 'Example: lunch', 'posterno-restaurants-menu' ) ),
					Field::make( 'complex', 'menu_items', esc_html__( 'Menu items', 'posterno-restaurants-menu' ) )
					->add_fields(
						array(
							Field::make( 'text', 'item_name', esc_html__( 'Menu item name', 'posterno-restaurants-menu' ) )->set_width( 50 ),
							Field::make( 'text', 'item_price', esc_html__( 'Menu item price', 'posterno-restaurants-menu' ) )->set_width( 50 ),
							Field::make( 'text', 'item_description', esc_html__( 'Menu item description', 'posterno-restaurants-menu' ) ),
						)
					),
				)
			);
		}

		return $definition;

	},
	10,
	3
);

/**
 * Filter: register the new action for the manage listings table.
 */
add_filter(
	'pno_listings_actions',
	function( $actions ) {

		$actions['restaurant'] = array(
			'title'    => esc_html__( 'Restaurant menu', 'posterno-restaurants-menu' ),
			'priority' => 3,
		);

		return $actions;

	}
);
