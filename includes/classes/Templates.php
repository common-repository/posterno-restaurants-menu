<?php
/**
 * Setup the templates loader for this addon.
 *
 * @package     posterno-restaurants-menu
 * @copyright   Copyright (c) 2020, Sematico LTD
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

namespace Posterno\Restaurants;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Dynamic templates loader for the addon.
 */
class Templates extends \PNO_Templates {

	/**
	 * Directory name where templates should be found into the theme.
	 *
	 * @var string
	 */
	protected $theme_template_directory = 'posterno/restaurant';

	/**
	 * Current plugin's root directory.
	 *
	 * @var string
	 */
	protected $plugin_directory = PNO_RESTAURANTS_PLUGIN_DIR;

}
