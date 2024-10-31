<?php
/**
 * Register functions for this addon.
 *
 * @package     posterno-restaurants-menu
 * @copyright   Copyright (c) 2020, Sematico, LTD
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

use Posterno\Restaurants\Plugin;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Displays the content of the restaurant field on the frontend.
 *
 * @param array $value value of the field.
 * @param array $field field config.
 * @return string
 */
function pno_display_field_restaurant_value( $value, $field ) {

	if ( ! empty( $value ) ) {

		ob_start();

		Plugin::instance()->templates
			->set_template_data(
				array(
					'value' => $value,
					'field' => $field,
				)
			)
			->get_template_part( 'restaurant-field-output' );

		return ob_get_clean();

	}

	return false;

}
