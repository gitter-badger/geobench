<?php
/**
 * Setup menus in WordPress admin
 *
 * @package GeoBench/Admin
 */

namespace GeoBench\Admin;
use GeoBench\Admin\Settings as Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Admin menus.
 */
class Menus {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
	}

	/**
	 * Add menu items.
	 *
	 * @uses add_menu_page()
	 */
	public function admin_menu() {

		global $menu;
		if ( current_user_can( 'manage_geo_data' ) ) {
			$menu[] = array( '', 'read', 'separator-geobench', '', 'wp-menu-separator geobench' );
		}

		add_menu_page(
			__( 'Settings', 'geobench' ),
			__( 'GeoBench', 'geobench' ),
			'manage_options',
			'geobench_settings',
			array( $this, 'settings_page' ),
			'dashicons-admin-site',
			apply_filters( 'geobench_menu_position', '35.5' )
		);

	}

	/**
	 * Init and output settings pages.
	 *
	 * @uses GeoBench\Admin\Settings
	 */
	public function settings_page() {
		$settings = new Settings();
		$settings->output_page();
	}

}

return new Menus();
