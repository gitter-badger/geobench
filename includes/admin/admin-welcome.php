<?php
/**
 * GeoBench Welcome page
 *
 * Shows a feature overview of GeoBench and other information.
 *
 *
 *
 * @package GeoBench/Admin
 */

namespace GeoBench\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * GeoBench Welcome page class.
 *
 * Displays plugin's feature overview and other information.
 *
 *
 */
class Welcome {

	/**
	 * Hook in tabs.
	 *
	 *
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menus' ) );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_init', array( $this, 'welcome' ) );
	}

	/**
	 * Add admin menus/screens.
	 *
	 *
	 */
	public function admin_menus() {
		if ( empty( $_GET['page'] ) ) {
			return;
		}
		$welcome_page_name = __( 'About GeoBench', 'geobench' );
		$welcome_page_title = __( 'Welcome to GeoBench', 'geobench' );
		switch ( $_GET['page'] ) {
			case 'geobench-about' :
				$page = add_dashboard_page( $welcome_page_title, $welcome_page_name, 'manage_options', 'geobench-about', array(
					$this,
					'about_screen'
				) );
				add_action( 'admin_print_styles-' . $page, array( $this, 'welcome_css' ) );
				break;
			default :
				break;
		}
	}

	/**
	 * Welcome screen styles.
	 *
	 * Injects CSS stylesheet for the welcome admin page.
	 *
	 *
	 */
	public function welcome_css() {
		wp_enqueue_style( 'gb-welcome', GB()->plugin_url() . '/assets/styles/welcome.css', array(), GB_VERSION );
	}

	/**
	 * Add styles just for the welcome page, and remove dashboard page links.
	 *
	 *
	 */
	public function admin_head() {
		remove_submenu_page( 'index.php', 'geobench-about' );
	}

	/**
	 * Output the about screen.
	 *
	 *
	 */
	public function about_screen() {

	}

	/**
	 * Sends user to the welcome page on first activation.
	 *
	 *
	 */
	public function welcome() {

		// Bail if no activation redirect transient is set
		if ( ! get_transient( '_geobench_activation_redirect' ) ) {
			return;
		}

		// Delete the redirect transient
		delete_transient( '_geobench_activation_redirect' );

		// Bail if we are waiting to install or update via the interface update/install links
		if ( Notices::has_notice( 'install' ) || Notices::has_notice( 'update' ) ) {
			return;
		}

		// Bail if activating from network, or bulk, or within an iFrame
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) || defined( 'IFRAME_REQUEST' ) ) {
			return;
		}

		if ( ( isset( $_GET['action'] ) && 'upgrade-plugin' == $_GET['action'] ) || ( ! empty( $_GET['page'] ) && $_GET['page'] === 'geobench-about' ) ) {
			return;
		}

		wp_redirect( admin_url( 'index.php?page=geobench-about' ) );
		exit;

	}

}

new Welcome();
