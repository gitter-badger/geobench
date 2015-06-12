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
 * Get all GeoBench plugin screen IDs.
 *
 * @return array
 */
function gb_get_screen_ids() {

	$gb_screen_id = sanitize_title( __( 'GeoBench', 'geobench' ) );

	$screen_ids = array(
		'toplevel_page_' . $gb_screen_id,
		$gb_screen_id . '_page_geobench-settings',
		'edit-geo',
		'geo'
	);

	return apply_filters( 'geobench_screen_ids', $screen_ids );
}
