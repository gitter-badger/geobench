<?php
/**
 * Load assets in administration.
 *
 * @since 1.0.0
 *
 * @package GeoBench/Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * GeoBench assets for admin pages.
 *
 * @since 1.0.0
 */
class GB_Admin_Assets {

	/**
	 * Hook in tabs.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	/**
	 * Enqueue styles
	 *
	 * @since 1.0.0
	 */
	public function admin_styles() {
		$screen = get_current_screen();
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		if ( in_array( $screen->id, gb_get_screen_ids() ) ) {
			wp_enqueue_style( 'gb-admin', GB()->plugin_url() . '/assets/styles/admin' . $suffix . '.css', array(), GB_VERSION );
		}
	}
	/**
	 * Enqueue scripts
	 *
	 * @since 1.0.0
	 */
	public function admin_scripts() {

		$screen = get_current_screen();
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		/**
		 * Register scripts
		 */

		// General admin pages
		wp_register_script( 'geobench-admin', GB()->plugin_url() . '/assets/js/admin/admin' . $suffix . '.js', array(
			'jquery',
			'jquery-ui-sortable',
			'jquery-ui-core',
			'jquery-tiptip',
			'select2'
		), GB_VERSION );
		// Settings pages
		wp_register_script( 'geobench-admin-settings', GB()->plugin_url() . '/assets/scripts/admin/settings' . $suffix . '.js', array(
			'jquery',
			'jquery-ui-sortable',
			'select2',
			'gb-admin'
		), GB()->version, true );
		// Meta box: Geo post type
		wp_register_script( 'geobench-admin-meta-boxes', GB()->plugin_url() . '/assets/js/admin/meta-boxes-geo' . $suffix . '.js', array(
			'jquery',
			'jquery-ui-sortable',
			'gb-admin'
		), GB_VERSION );
		// jQuery TipTip
		wp_register_script( 'jquery-tiptip', GB()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array(
			'jquery'
		), '1.3.0', true );
		// Select2
		wp_register_script( 'select2', GB()->plugin_url() . '/assets/js/select2/select2' . $suffix . '.js', array(
			'jquery'
		), '4.0.0' );

		/**
		 * Enqueue scripts
		 */

		if ( in_array( $screen->id, gb_get_screen_ids() ) ) {
			wp_enqueue_script( 'geobench-admin-settings' );
		}
		if ( in_array( $screen->id, array( 'geo', 'edit-geo', 'map', 'edit-map' ) ) ) {
			wp_enqueue_script( 'geobench-admin-meta-boxes' );
		}

	}

}

return new GB_Admin_Assets();
