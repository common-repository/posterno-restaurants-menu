<?php
/**
 * Defines a representation of a Posterno entity.
 *
 * @package     addon-requirements-check
 * @copyright   Copyright (c) 2019, Sematico LTD
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1.0
 */

namespace PosternoRequirements;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Check requirements.
 */
class Check {

	/**
	 * Args used to verify requirements.
	 *
	 * @var array $args {
	 *     Requirement arguments.
	 *
	 *     @type string $title Name of the plugin.
	 *     @type string $php   Minimum required PHP version.
	 *     @type string $wp    Minimum required WordPress version.
	 *     @type string $pno   Minimum required Posterno version.
	 *     @type string $file  Path to the main plugin file.
	 *     @type array $i18n   {
	 *         @type string $php PHP version mismatch error message.
	 *         @type string $wp  WP version mismatch error message.
	 *         @type string $pno Posterno version mismatch error message.
	 *     }
	 * }
	 */
	private $args;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args {
	 *     An array of arguments to overwrite the default requirements.
	 *
	 *     @type string $title Name of the plugin.
	 *     @type string $php   Minimum required PHP version.
	 *     @type string $wp    Minimum required WordPress version.
	 *     @type string $pno   Minimum required Posterno version.
	 *     @type string $file  Path to the main plugin file.
	 *     @type array $i18n   {
	 *         @type string $php PHP version mismatch error message.
	 *         @type string $wp  WP version mismatch error message.
	 *         @type string $pno Posterno version mismatch error message.
	 *     }
	 * }
	 */
	public function __construct( $args ) {
		$args = (array) $args;

		$this->args = wp_parse_args(
			$args,
			array(
				'title' => '',
				'php'   => '5.6',
				'wp'    => '4.9.6',
				'pno'   => '0.9.0',
				'wc'    => false,
				'file'  => null,
				'i18n'  => array(),
			)
		);

		$this->args['i18n'] = wp_parse_args(
			$this->args['i18n'],
			array(
				'php' => 'The &#8220;%1$s&#8221; plugin cannot run on PHP versions older than %2$s. Please contact your host and ask them to upgrade.',
				'wp'  => 'The &#8220;%1$s&#8221; plugin cannot run on WordPress versions older than %2$s. Please update your WordPress.',
				'pno' => 'The &#8220;%1$s&#8221; plugin could not be activated because it requires Posterno %2$s. Please update or install Posterno.',
				'wc'  => 'The &#8220;%1$s&#8221; plugin could not be activated because it requires WooCommerce %2$s. Please update or install WooCommerce.',
			)
		);
	}

	/**
	 * Check if the install passes the requirements.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return bool True if the install passes the requirements, false otherwise.
	 */
	public function passes() {
		$passes = $this->php_passes() && $this->wp_passes() && $this->pno_passes() && $this->wc_passes();

		if ( ! $passes ) {
			add_action( 'admin_notices', array( $this, 'deactivate' ) );
		}

		return $passes;
	}

	/**
	 * Deactivates the plugin again.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function deactivate() {
		if ( null !== $this->args['file'] ) {
			deactivate_plugins( plugin_basename( $this->args['file'] ) );
		}
	}

	/**
	 * Checks if the PHP version passes the requirement.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return bool True if the PHP version is high enough, false otherwise.
	 */
	protected function php_passes() {
		if ( self::_php_at_least( $this->args['php'] ) ) {
			return true;
		}

		add_action( 'admin_notices', array( $this, 'php_version_notice' ) );

		return false;
	}

	/**
	 * Compares the current PHP version with the minimum required version.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param string $min_version The minimum required version.
	 * @return bool True if the PHP version is high enough, false otherwise.
	 */
	protected static function _php_at_least( $min_version ) {
		return version_compare( PHP_VERSION, $min_version, '>=' );
	}

	/**
	 * Displays the PHP version notice.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function php_version_notice() {
		/**
		 * Filters the notice for outdated PHP versions.
		 *
		 * @since 1.1.0
		 *
		 * @param string $message The error message.
		 * @param string $title   The plugin name.
		 * @param string $php     The WordPress version.
		 */
		$message = apply_filters( 'pno_requirements_check_php_notice', $this->args['i18n']['php'], $this->args['title'], $this->args['php'] );
		?>
		<div class="error">
			<p><?php printf( $message, esc_html( $this->args['title'] ), $this->args['php'] ); ?></p>
		</div>
		<?php
	}

	/**
	 * Check if the WordPress version passes the requirement.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return bool True if the WordPress version is high enough, false otherwise.
	 */
	protected function wp_passes() {
		if ( self::_wp_at_least( $this->args['wp'] ) ) {
			return true;
		}

		add_action( 'admin_notices', array( $this, 'wp_version_notice' ) );

		return false;
	}

	/**
	 * Verify Posterno requirements.
	 *
	 * @return bool
	 */
	protected function pno_passes() {
		if ( self::_pno_at_least( $this->args['pno'] ) ) {
			return true;
		}

		add_action( 'admin_notices', array( $this, 'pno_version_notice' ) );

		return false;

	}

	/**
	 * Verify Posterno version with the required version.
	 *
	 * @param string $min_version required min version.
	 * @return boolean
	 */
	protected static function _pno_at_least( $min_version ) {
		return defined( 'PNO_VERSION' ) && version_compare( PNO_VERSION, $min_version, '>=' );
	}

	/**
	 * Show the Posterno version notice.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function pno_version_notice() {
		/**
		 * Filters the notice for outdated WordPress versions.
		 *
		 * @param string $message The error message.
		 * @param string $title   The plugin name.
		 * @param string $pno     The Posterno version.
		*/
		$message = apply_filters( 'pno_requirements_check_posterno_notice', $this->args['i18n']['pno'], $this->args['title'], $this->args['pno'] );
		?>
		<div class="error">
			<p><?php printf( $message, esc_html( $this->args['title'] ), $this->args['pno'] ); ?></p>
		</div>
		<?php
	}

	/**
	 * Verify WooCommerce requirements.
	 *
	 * @return bool
	 */
	protected function wc_passes() {
		if ( $this->args['wc'] === false ) {
			return true;
		}

		if ( self::_wc_at_least( $this->args['wc'] ) ) {
			return true;
		}

		add_action( 'admin_notices', array( $this, 'wc_version_notice' ) );

		return false;

	}

	/**
	 * Verify WooCommerce version with the required version.
	 *
	 * @param string $min_version required min version.
	 * @return boolean
	 */
	protected static function _wc_at_least( $min_version ) {
		if ( class_exists( 'WooCommerce' ) ) {
			global $woocommerce;
			if ( version_compare( $woocommerce->version, $min_version, '>=' ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Show the WooCommerce version notice.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function wc_version_notice() {
		/**
		 * Filters the notice for outdated WooCommerce versions.
		 *
		 * @param string $message The error message.
		 * @param string $title   The plugin name.
		 * @param string $pno     The WooCommerce version.
		*/
		$message = apply_filters( 'wc_requirements_check_posterno_notice', $this->args['i18n']['wc'], $this->args['title'], $this->args['wc'] );
		?>
		<div class="error">
			<p><?php printf( $message, esc_html( $this->args['title'] ), $this->args['wc'] ); ?></p>
		</div>
		<?php
	}

	/**
	 * Compare the current WordPress version with the minimum required version.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param string $min_version Minimum required WordPress version.
	 * @return bool True if the WordPress version is high enough, false otherwise.
	 */
	protected static function _wp_at_least( $min_version ) {
		return version_compare( get_bloginfo( 'version' ), $min_version, '>=' );
	}

	/**
	 * Show the WordPress version notice.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function wp_version_notice() {
		/**
		 * Filters the notice for outdated WordPress versions.
		 *
		 * @since 1.1.0
		 *
		 * @param string $message The error message.
		 * @param string $title   The plugin name.
		 * @param string $php     The WordPress version.
		*/
		$message = apply_filters( 'pno_requirements_check_wordpress_notice', $this->args['i18n']['wp'], $this->args['title'], $this->args['wp'] );
		?>
		<div class="error">
			<p><?php printf( $message, esc_html( $this->args['title'] ), $this->args['wp'] ); ?></p>
		</div>
		<?php
	}

}
