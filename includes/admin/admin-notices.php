<?php
/**
 * Display notices in admin pages.
 *
 * @package GeoBench/Admin
 */

namespace GeoBench\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin notices.
 *
 * Notifications for the administration pages.
 */
class Notices {

	/**
	 * Array of notices.
	 *
	 * Notices are formatted as an associative array: name => callback.
	 *
	 * @access private
	 * @var array
	 */
	private $notices = array(
		'install'   => 'install_notice',
		'update'    => 'update_notice',
	);

	/**
	 * Admin notices constructor.
	 */
	public function __construct() {
		add_action( 'wp_loaded', array( $this, 'hide_notices' ) );
		add_action( 'admin_print_styles', array( $this, 'add_notices' ) );
	}

	/**
	 * Show a notice.
	 *
	 * @param string $name
	 */
	public static function add_notice( $name ) {
		$notices = array_unique( array_merge( \get_option( 'geobench_admin_notices', array() ), array( $name ) ) );
		update_option( 'geobench_admin_notices', $notices );
	}

	/**
	 * Remove a notice.
	 *
	 * Remove a notice from being displayed.
	 *
	 * @param string $name
	 */
	public static function remove_notice( $name ) {
		$notices = array_diff( \get_option( 'geobench_admin_notices', array() ), array( $name ) );
		update_option( 'geobench_admin_notices', $notices );
	}

	/**
	 * Check if notice is shown.
	 *
	 * Checks for a notice for being currently shown.
	 *
	 * @param  string $name
	 *
	 * @return boolean
	 */
	public static function has_notice( $name ) {
		return in_array( $name, \get_option( 'geobench_admin_notices', array() ) );
	}

	/**
	 * Hide notices.
	 *
	 * Hide a notice if the GET variable is set.
	 */
	public function hide_notices() {
		if ( isset( $_GET['geobench-hide-notice'] ) ) {
			$hide_notice = sanitize_text_field( $_GET['geobech-hide-notice'] );
			self::remove_notice( $hide_notice );
			do_action( 'geobench_hide_' . $hide_notice . '_notice' );
		}
	}

	/**
	 * When install is hidden, trigger a redirect.
	 */
	public function hide_install_notice() {
		// Welcome page
		if ( ! self::has_notice( 'update' ) ) {
			delete_transient( '_geobench_activation_redirect' );
			wp_redirect( admin_url( 'index.php?page=geobench-about&geobench-updated=true' ) );
			exit;
		}
	}

	/**
	 * Add notices.
	 *
	 * Print notices html, scripts and styles styles if needed.
	 *
	 * @uses \get_option()
	 * @uses
	 */
	public function add_notices() {
		$notices = \get_option( 'geobench_admin_notices', array() );
		foreach ( $notices as $notice ) {
			add_action( 'admin_notices', array( $this, $this->notices[ $notice ] ) );
		}
		//wp_enqueue_style( 'geobench-activation', plugins_url(  '/assets/css/activation.css', GB_PLUGIN_FILE ) );
		//wp_enqueue_script( 'geobench-admin-notices' );
	}

	/**
	 * New installation notice.
	 *
	 * If the plugin has just installed, show a notice leading to the settings page.
	 */
	public function install_notice() {

	}

	/**
	 * Update to new version notice.
	 *
	 * If we need to update, include a message with the update button.
	 */
	public function update_notice() {

	}

}

new Notices();
