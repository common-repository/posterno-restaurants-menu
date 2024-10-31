<?php
/**
 * The class that loads the whole plugin after requirements have been met.
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
 * Boot the plugin.
 */
class Plugin {

	/**
	 * Instance of the plugin.
	 *
	 * @var Plugin
	 */
	private static $instance;

	/**
	 * Plugin's file.
	 *
	 * @var Templates
	 */
	private $file = '';

	/**
	 * Templates load object
	 *
	 * @var object
	 */
	public $templates;

	/**
	 * Setup the instance.
	 *
	 * @param string $file the plugin's file.
	 * @return Plugin
	 */
	public static function instance( $file = '' ) {

		// Return if already instantiated.
		if ( self::is_instantiated() ) {
			return self::$instance;
		}

		// Setup the singleton.
		self::setup_instance( $file );

		self::$instance->setup_constants();
		self::$instance->setup_files();

		self::$instance->templates = new Templates();

		// Return the instance.
		return self::$instance;

	}

	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 0.1.0
	 * @access protected
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'posterno-restaurants-menu' ), '0.1.0' );
	}
	/**
	 * Disable un-serializing of the class.
	 *
	 * @since 0.1.0
	 * @access protected
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'posterno-restaurants-menu' ), '0.1.0' );
	}

	/**
	 * Return whether the main loading class has been instantiated or not.
	 *
	 * @since 0.1.0
	 *
	 * @return boolean True if instantiated. False if not.
	 */
	private static function is_instantiated() {
		// Return true if instance is correct class.
		if ( ! empty( self::$instance ) && ( self::$instance instanceof Plugin ) ) {
			return true;
		}
		// Return false if not instantiated correctly.
		return false;
	}

	/**
	 * Helper method to setup the instance.
	 *
	 * @param string $file the file of the plugin.
	 * @return void
	 */
	private static function setup_instance( $file = '' ) {
		self::$instance       = new Plugin();
		self::$instance->file = $file;
	}

	/**
	 * Setup helper constants.
	 *
	 * @return void
	 */
	private function setup_constants() {
		// Plugin version.
		if ( ! defined( 'PNO_RESTAURANTS_VERSION' ) ) {
			define( 'PNO_RESTAURANTS_VERSION', '1.0.0' );
		}
		// Plugin Root File.
		if ( ! defined( 'PNO_RESTAURANTS_PLUGIN_FILE' ) ) {
			define( 'PNO_RESTAURANTS_PLUGIN_FILE', $this->file );
		}
		// Plugin Base Name.
		if ( ! defined( 'PNO_RESTAURANTS_PLUGIN_BASE' ) ) {
			define( 'PNO_RESTAURANTS_PLUGIN_BASE', plugin_basename( PNO_RESTAURANTS_PLUGIN_FILE ) );
		}
		// Plugin Folder Path.
		if ( ! defined( 'PNO_RESTAURANTS_PLUGIN_DIR' ) ) {
			define( 'PNO_RESTAURANTS_PLUGIN_DIR', plugin_dir_path( PNO_RESTAURANTS_PLUGIN_FILE ) );
		}
		// Plugin Folder URL.
		if ( ! defined( 'PNO_RESTAURANTS_PLUGIN_URL' ) ) {
			define( 'PNO_RESTAURANTS_PLUGIN_URL', plugin_dir_url( PNO_RESTAURANTS_PLUGIN_FILE ) );
		}
	}

	/**
	 * Load required files.
	 *
	 * @return void
	 */
	public function setup_files() {

		require_once PNO_RESTAURANTS_PLUGIN_DIR . 'includes/filters.php';
		require_once PNO_RESTAURANTS_PLUGIN_DIR . 'includes/functions.php';
		require_once PNO_RESTAURANTS_PLUGIN_DIR . 'includes/assets.php';
		require_once PNO_RESTAURANTS_PLUGIN_DIR . 'includes/actions.php';

		require_once PNO_RESTAURANTS_PLUGIN_DIR . 'includes/install.php';

	}

	/**
	 * Allow translations.
	 *
	 * @return void
	 */
	public function textdomain() {
		load_plugin_textdomain( 'posterno-restaurants-menus', false, PNO_RESTAURANTS_PLUGIN_DIR . '/languages' );
	}

	/**
	 * Run addon installation on activation.
	 *
	 * @return void
	 */
	public function install() {

		// Network wide.
		$network_wide = ! empty( $_GET['networkwide'] )
			? (bool) $_GET['networkwide']
			: false;

		// Call the installer directly during the activation hook.
		pno_restaurants_install( $network_wide );

	}

}
