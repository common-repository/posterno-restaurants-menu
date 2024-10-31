<?php
/**
 * Plugin Name:     Posterno Restaurants Menu
 * Plugin URI:      https://posterno.com/extensions/restaurants-menu
 * Description:     Allows listings owners to create a restaurant menu for their listings.
 * Author:          Posterno
 * Author URI:      https://posterno.com
 * Text Domain:     posterno-restaurants-menu
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * Posterno Restaurants Menu is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Posterno Restaurants Menu is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Posterno Restaurants Menu. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package posterno-restaurants-menu
 * @author Sematico LTD
 */

namespace Posterno\Restaurants;

defined( 'ABSPATH' ) || exit;

if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require dirname( __FILE__ ) . '/vendor/autoload.php';
}

/**
 * Activate the plugin only when requirements are met.
 */
add_action(
	'plugins_loaded',
	function() {

		$requirements_check = new \PosternoRequirements\Check(
			array(
				'title' => 'Posterno Restaurants Menu',
				'file'  => __FILE__,
				'pno'   => '1.2.8',
			)
		);

		if ( $requirements_check->passes() ) {

			$addon = Plugin::instance( __FILE__ );
			add_action( 'plugins_loaded', array( $addon, 'textdomain' ), 11 );

		}
		unset( $requirements_check );

	}
);

/**
 * Install addon's required data on plugin activation.
 */
register_activation_hook(
	__FILE__,
	function() {

		$requirements_check = new \PosternoRequirements\Check(
			array(
				'title' => 'Posterno Restaurants Menu',
				'file'  => __FILE__,
				'pno'   => '1.2.8',
			)
		);

		if ( $requirements_check->passes() ) {
			$addon = Plugin::instance( __FILE__ );
			$addon->install();
		}
		unset( $requirements_check );

	}
);
