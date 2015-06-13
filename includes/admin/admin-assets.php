<?php
/**
 * Load assets in administration.
 *
 * @package GeoBench/Admin
 */

namespace GeoBench\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * GeoBench assets for admin pages.
 */
class Assets {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	/**
	 * Register and enqueue styles.
	 */
	public function admin_styles() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		/**
		 * Register styles
		 */

		// Select2
		wp_register_style(
			'geobench-select2',
			GB()->plugin_url() . '/assets/css/vendor/select2' . $suffix . '.css',
			'',	'4.0.0'
		);

		// GeoBench admin scripts
		wp_register_style(
			'geobench-admin',
			GB()->plugin_url() . '/assets/css/geobench-admin' . $suffix . '.css',
			array(
				'geobench-select2'
			), GB_VERSION
		);

		/**
		 * Enqueue styles
		 */

		if ( gb_is_admin( get_current_screen() ) ) {
			wp_enqueue_style( 'geobench-admin' );
		}

	}

	/**
	 * Register and enqueue scripts.
	 */
	public function admin_scripts() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		/**
		 * Register scripts
		 */

		// jQuery TipTip
		wp_register_script( 'geobench-jquery-tiptip', GB()->plugin_url() . '/assets/js/vendor/jquery.tipTip' . $suffix . '.js', array(
			'jquery'
		), '1.3.0', true );
		// Select2
		wp_register_script( 'geobench-select2', GB()->plugin_url() . '/assets/js/vendor/select2' . $suffix . '.js', array(
			'jquery'
		), '4.0.0' );
		// General admin pages
		wp_register_script( 'geobench-admin', GB()->plugin_url() . '/assets/js/geobench-admin' . $suffix . '.js', array(
			'jquery',
			'jquery-ui-sortable',
			'jquery-ui-core',
			'geobench-jquery-tiptip',
			'geobench-select2'
		), GB_VERSION, true );

		/**
		 * Enqueue scripts
		 */

		if ( gb_is_admin( get_current_screen() ) ) {
			wp_enqueue_script( 'geobench-admin' );
		}

	}

}

return new Assets();
