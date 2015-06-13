<?php
/**
 * GeoBench Admin Functions
 *
 * @package GeoBench/Admin/Functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Is GeoBench admin?
 *
 * Returns true if the current screen a GeoBench admin or GeoBench component admin page.
 * An instance of WP_Screen must be passed as argument:
 * get_current_screen() can only be invoked after the screen is set or would trigger an error.
 *
 * @param  \WP_Screen $screen
 *
 * @return bool
 */
function gb_is_admin( $screen ) {
	if ( $screen instanceof WP_Screen ) {

		$top_level = $screen->base;
		$prefix    = 'toplevel_page_';
		if ( substr( $top_level, 0, strlen( $prefix ) ) == $prefix ) {
			$screen = substr( $top_level, strlen( $prefix ) );
		}

		if ( 'geobench_settings' == $screen ) {
			return true;
		} elseif ( in_array( $screen->post_type, array( 'map', 'geo' ) ) ) {
			return true;
		} elseif ( in_array( $screen->taxonomy, array( 'map_type', 'geo_type' ) ) ) {
			return true;
		}
	}
	return false;
}
