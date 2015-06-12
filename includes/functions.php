<?php
/**
 * GeoBench functions.
 *
 * General core functions available on both the front-end and admin.
 *
 * @package GeoBench/Functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'is_ajax' ) ) {

	/**
	 * Is this an ajax request?
	 *
	 * Returns true when the page is loaded via ajax.
	 *
	 * @return bool
	 */
	function is_ajax() {
		return defined( 'DOING_AJAX' );
	}

}

if ( ! function_exists( 'gb_get_geometry' ) ) {

	/**
	 * Main function for returning geometries.
	 *
	 * @param  mixed $geo  Post object or post ID of the geometry object.
	 * @param  array $args (default: array()) Contains all arguments to be used to get this geometry.
	 *
	 * @return object Instance of GeoBench\Geometries\Geometry implementation.
	 */
	function gb_get_geometry( $geo = false, $args = array() ) {
		return GB()->geometry_factory->get_geometry( $geo, $args );

	}

}

if ( ! function_exists( 'gb_get_geometry_types' ) ) {

	/**
	 * Returns a list of the available geometry types.
	 *
	 * @return array Associative array with type for keys and name for values.
	 */
	function gb_get_geometry_types() {
		return GB()->geometry_factory->get_geometry_types();
	}

}

if ( function_exists( 'gb_which_supports' ) ) {

	/**
	 * Get supported geometries and stores combinations.
	 *
	 * Utility to get which stores are supported by which geometries and vice versa.
	 *
	 * @param  string $types Either 'stores' or 'geometries'.
	 *
	 * @return array|bool
	 */
	function gb_which_supports( $types ) {

		if ( ! $types ) {
			return false;
		}

		$stores     = GB()->store_factory->get_store_types();
		$geometries = GB()->geometry_factory->get_geometry_types();

		if ( $types == 'stores' ) {

			foreach ( $geometries as $geometry_type => $geometry_name ) {
				foreach ( $stores as $store_type => $store_name ) {
					$store = GB()->store_factory->get_store( $store_type );
					if ( $store instanceof \GeoBench\Store ) {
						if ( in_array( $geometry_type, $store->supports ) ) {
							$supported[$geometry_type][$store_type] = $store_name;
						}
					}
				}
			}

		} elseif ( $types == 'geometries' ) {

			foreach ( $stores as $store_type => $store_name ) {
				$store = GB()->store_factory->get_store( $store_type );
				if ( $store instanceof \GeoBench\Store ) {
					foreach ( $geometries as $geometry_type => $geometry_name ) {
						if ( in_array( $geometry_type, $store->supports ) ) {
							$supported[$store_type][$geometry_type] = $geometry_name;
						}
					}
				}
			}

		}

		return isset( $supported ) ? $supported : false;
	}

}
