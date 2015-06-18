<?php
/**
 * Plugin Name: GeoBench
 * Plugin URI:  https://github.com/geobench/geobench
 * Description: Toolkit for Maps
 * Version:     0.1.0
 * Author:      Team GeoBench
 * Author URI:  https://geoben.ch/
 * License:     GPLv2+
 * Text Domain: geobench
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2015 Team Geobench (email : hey@geoben.ch)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * GeoBench requires PHP 5.4.0 minimum.
 * WordPress supports 5.2.4 and recommends 5.4.0.
 * @see https://make.wordpress.org/plugins/2015/06/05/policy-on-php-versions/
 */
if ( version_compare( PHP_VERSION, '5.4.0', '<') ) {
	add_action( 'admin_notices',
		function() {
	        echo '<div class="error"><p>'.
	             sprintf( __( "GeoBench requires PHP 5.4 or above to function properly. Detected PHP version on your server is %s. Please upgrade PHP to activate GeoBench or remove the plugin.", 'geobench' ), phpversion() ? phpversion() : '`undefined`' ) .
	             '</p></div>';
	        if ( isset( $_GET['activate'] ) ) {
		        unset( $_GET['activate'] );
	        }
        }
    );
    return;
}

if ( ! class_exists( 'GeoBench' ) ) :

	/**
	 * GeoBench.
	 *
	 * @package GeoBench
	 * @link https://geobench.io/
	 * @version 1.0.0
	 */
	final class GeoBench {

		/**
		 * GeoBench plugin version.
		 *
		 * @access public
		 * @var string GeoBench current version.
		 */
		public $version = '0.1.0';

		/**
		 * GeoBench database version.
		 *
		 * @access public
		 * @var string GeoBench latest supported database version.
		 */
		public $database_version = '1.0.0';

		/**
		 * Instance of this class.
		 *
		 * @access protected
		 * @var GeoBench The single instance of the class
		 */
		protected static $_instance = null;

		/**
		 * Instance of Store Factory class.
		 *
		 * To get the right type of store.
		 *
		 * @access public
		 * @var GeoBench\Store_Factory
		 */
		public $store_factory = null;

		/**
		 * Instance of Geometry Factory class.
		 *
		 * To get the right type of geometry.
		 *
		 * @access public
		 * @var GeoBench\Geometry_Factory
		 */
		public $geometry_factory = null;

		/**
		 * Instance of Map Factory class.
		 *
		 * To get the right type of map.
		 *
		 * @access public
		 * @var GeoBench\Map_Factory
		 */
		public $map_factory = null;

		/**
		 * Instance of Field Factory class.
		 *
		 * To get the right type of field.
		 * Note: This is instantiated only in admin.
		 *
		 * @access public
		 * @var GeoBench\Admin\Field_Factory|null
		 */
		public $field_factory = null;

		/**
		 * Main GeoBench Instance.
		 *
		 * Ensures only one instance of GeoBench is loaded or can be loaded.
		 *
		 * @return GeoBench
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Cloning is forbidden.
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cloning the main instance of GeoBench is forbidden.', 'geobench' ), '1.0.0' );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of GeoBench is forbidden.', 'geobench' ), '1.0.0' );
		}

		/**
		 * GeoBench Constructor.
		 */
		public function __construct() {
			$this->define_constants();
			$this->includes();
			$this->init_hooks();
			do_action( 'geobench_loaded' );
		}

		/**
		 * Hook into WordPress actions and filters.
		 *
		 * @access private
		 */
		private function init_hooks() {
			register_activation_hook( __FILE__, array( 'GeoBench\\Install', 'activate' ) );
			register_deactivation_hook( __FILE__, array( 'GeoBench\\Install', 'deactivate' ) );
			add_action( 'init', array( $this, 'init' ), 0 );
			if ( $this->is_request( 'admin' ) ) {
				add_action( 'admin_init', array( $this, 'register_settings' ) );
			}
		}

		/**
		 * Register GeoBench settings.
		 *
		 * @uses GeoBench\Admin\Settings to handle WordPress Settings API.
		 */
		public function register_settings() {
			include_once 'includes/admin/admin-settings.php';
			$settings = new GeoBench\Admin\Settings;
			$settings->register_settings( $settings::get_settings() );
		}

		/**
		 * Define GeoBench Constants.
		 *
		 * @access private
		 */
		private function define_constants() {
			$this->define( 'GB_PLUGIN_FILE', __FILE__ );
			$this->define( 'GB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			$this->define( 'GB_VERSION', $this->version );
			$this->define( 'GB_DB_VERSION', $this->database_version );
		}

		/**
		 * Define constants if not already set.
		 *
		 * Helper function to define constants in GeoBench.
		 *
		 * @access private
		 *
		 * @param string $name
		 * @param string|bool $value
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * What type of request is this?
		 *
		 * Helper function to determine the type of WordPress requests.
		 *
		 * @access private
		 *
		 * @param string $type WordPress request: 'ajax', 'cron', 'frontend' or 'admin'.
		 *
		 * @return bool
		 */
		private function is_request( $type ) {
			switch ( $type ) {
				case 'admin' :
					return is_admin();
				case 'ajax' :
					return defined( 'DOING_AJAX' );
				case 'cron' :
					return defined( 'DOING_CRON' );
				case 'frontend' :
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
				default :
					return false;
					break;
			}
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @uses GeoBench->is_request() to include classes and functions conditionally.
		 */
		public function includes() {

			// Shared functions
			include_once 'includes/functions.php';

			// Installation
			include_once 'includes/install.php';

			// Register post types and taxonomies
			include_once 'includes/post-types.php';

			// Abstracts
			include_once 'includes/abstracts/abstract-store.php';
			include_once 'includes/abstracts/abstract-geolocation.php';
			include_once 'includes/abstracts/abstract-geometry.php';
			include_once 'includes/abstracts/abstract-map.php';

			// Factories
			include_once 'includes/stores/store-factory.php';
			include_once 'includes/geometries/geometry-factory.php';
			include_once 'includes/maps/map-factory.php';

			// Core objects implementations
			include_once 'includes/stores/store-wordpress.php';
			include_once 'includes/geolocation/geolocation-gps.php';
			include_once 'includes/geometries/geometry-coordinates.php';
			include_once 'includes/maps/map-google.php';

			// Admin interface
			if ( $this->is_request( 'admin' ) ) {
				// Admin functions
				include_once 'includes/admin/admin-functions.php';
				// Admin classes
				include_once 'includes/admin/admin-post-types.php';
				// Admin classes only needed during non-ajax requests
				if ( ! $this->is_request( 'ajax' ) ) {
					$this->load_fields_api();
					include_once 'includes/admin/admin-settings.php';
					include_once 'includes/admin/admin-menus.php';
					include_once 'includes/admin/admin-welcome.php';
					include_once 'includes/admin/admin-notices.php';
					include_once 'includes/admin/admin-assets.php';
				}
			}

			// Frontend classes
			if ( $this->is_request( 'frontend' ) ) {

				// We'll get there in a moment or two...

			}

			// WordPress REST API
			// @todo Probably the API request needs own check wrap for conditional include.
			// if ( $this->is_request( 'json' ) {

				include_once 'includes/api.php';

			// }

		}

		/**
		 * Load fields.
		 *
		 * Includes input fields for settings and meta-boxes.
		 */
		public function load_fields_api() {
			// Abstract model
			include_once 'includes/abstracts/abstract-field.php';
			// Core fields
			include_once 'includes/admin/fields/field-standard.php';
			include_once 'includes/admin/fields/field-checkbox.php';
			include_once 'includes/admin/fields/field-map.php';
			include_once 'includes/admin/fields/field-radio.php';
			include_once 'includes/admin/fields/field-select.php';
			include_once 'includes/admin/fields/field-textarea.php';
			// Extend
			do_action( 'geobench_load_fields' );
			// Field factory
			include_once 'includes/admin/fields/field-factory.php';
		}

		/**
		 * Init GeoBench when WordPress initialises.
		 */
		public function init() {

			// Before init action
			do_action( 'before_geobench_init' );

			// Set up localisation
			$this->load_plugin_textdomain();

			// Open factories
			$this->store_factory     = new GeoBench\Store_Factory();
			$this->geometry_factory  = new GeoBench\Geometry_Factory();
			$this->map_factory       = new GeoBench\Map_Factory();
			if ( $this->is_request( 'admin' ) ) {
				$this->load_fields_api();
				$this->field_factory = new GeoBench\Admin\Field_Factory();
			}

			// Upon init action
			do_action( 'geobench_init' );

		}

		/**
		 * Load l10n files.
		 *
		 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
		 *
		 * Admin Locales are found in:
		 * 		- WP_LANG_DIR/geobench/geobench-admin-LOCALE.mo
		 * 		- WP_LANG_DIR/plugins/geobench-admin-LOCALE.mo
		 *
		 * Frontend/global Locales found in:
		 * 		- WP_LANG_DIR/geobench/geobench-LOCALE.mo
		 * 	 	- geobench/i18n/languages/geobench-LOCALE.mo (which if not found falls back to:)
		 * 	 	- WP_LANG_DIR/plugins/geobench-LOCALE.mo
		 *
		 */
		public function load_plugin_textdomain() {

			$locale = apply_filters( 'plugin_locale', get_locale(), 'geobench' );

			if ( $this->is_request( 'admin' ) ) {
				load_textdomain( 'geobench', WP_LANG_DIR . '/geobench/geobench-admin-' . $locale . '.mo' );
				load_textdomain( 'geobench', WP_LANG_DIR . '/plugins/geobench-admin-' . $locale . '.mo' );
			}

			load_textdomain( 'geobench', WP_LANG_DIR . '/geobench/geobench-' . $locale . '.mo' );
			load_plugin_textdomain( 'geobench', false, plugin_basename( dirname( __FILE__ ) ) . '/i18n/languages' );

		}

		/**
		 * Get the plugin url.
		 *
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Get the template path.
		 *
		 * @return string
		 */
		public function template_path() {
			return apply_filters( 'geobench_template_path', 'geobench/' );
		}

		/**
		 * Get Ajax URL.
		 *
		 * @return string
		 */
		public function ajax_url() {
			return admin_url( 'admin-ajax.php', 'relative' );
		}

	}

else :

	add_action( 'admin_notices',
		function() {
			echo '<div class="error"><p>'.
			     sprintf( __( "Plugin conflict: %s has been declared already by another plugin or theme and GeoBench cannot run properly. Try deactivating other plugins and try again.", 'geobench' ), '`class Geobench`' ) .
			     '</p></div>';
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}
	);
	return;

endif;

if ( ! function_exists( 'GB' ) ) :

	/**
	 * GeoBench instance.
	 *
	 * Returns the main instance of GeoBench to prevent the need to use globals.
	 *
	 * @return  GeoBench
	 */
	function GB() {
		return GeoBench::instance();
	}

	// Instantiate
	$GLOBALS['geobench'] = GB();

else :

	add_action( 'admin_notices',
		function() {
			echo '<div class="error"><p>'.
			     sprintf( __( "Plugin conflict: %s has been declared already by another plugin or theme and GeoBench cannot run properly. Try deactivating other plugins and try again.", 'geobench' ), '`function GB()`' ) .
			     '</p></div>';
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}
	);
	return;

endif;
