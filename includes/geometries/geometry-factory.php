<?php
/**
 * Geo object factory
 *
 * Creates the right geo object according to geo object type.
 *
 * @since 1.0.0
 *
 * @package GeoBench/Geometries
 */

namespace GeoBench;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Geo Factory class.
 *
 * The GeoBench geo factory creating the right geo object.
 *
 * @since 1.0.0
 */
class Geometry_Factory {

	/**
	 * Get geometry types.
	 *
	 * @return array
	 */
	public function get_geometry_types() {
		return (array) apply_filters( 'geobench_get_geometry_types', array(
			'coordinates' => __( 'Coordinates', 'geobench' )
		) );
	}

	/**
	 * Get geometry class instance.
	 *
	 * Determines the type of geometry and returns an instance of its class.
	 *
	 * @param  $geometry
	 * @param  $args
	 *
	 * @return null|bool|Geometry
	 */
	public function get_geometry( $geometry = false, $args = array() ) {
		$geometry_object = $this->get_geometry_object( $geometry );
		if ( ! $geometry_object ) {
			return false;
		}
		$geometry_class = $this->get_geometry_class( $geometry_object, $args );
		if ( class_exists( $geometry_class ) ) {
			return new $geometry_class( $geometry, $args );
		}
		return null;
	}

	/**
	 * Get geometry object.
	 *
	 * Returns the geometry object as WordPress post object.
	 *
	 * @param  int|object|Geometry|\WP_Post $geometry
	 *
	 * @return bool|object|\WP_Post
	 */
	private function get_geometry_object( $geometry ) {
		$geometry_object = false;
		if ( false === $geometry ) {
			$geometry_object = $GLOBALS['post'];
		} elseif ( is_numeric( $geometry ) ) {
			$geometry_object = get_post( $geometry );
		} elseif ( $geometry instanceof Geometry ) {
			$geometry_object = get_post( $geometry->id );
		} elseif ( ! ( $geometry instanceof \WP_Post ) ) {
			$geometry_object = false;
		}
		return $geometry_object;
	}

	/**
	 * Get geometry class.
	 *
	 * @param  $geometry
	 * @param  $args
	 *
	 * @return mixed|void
	 */
	private function get_geometry_class( $geometry, $args ) {

		$id             = absint( $geometry->ID );
		$post_type      = $geometry->post_type;
		$geometry_type  = false;

		if ( 'geo' === $post_type && ! $geometry->type && ! $args['geo_type'] ) {
			$geometry_type = $geometry->type;
		} elseif ( 'geo' === $post_type ) {
			if ( isset( $args['geo_type'] ) ) {
				$geometry_type = $args['geo_type'];
			} else {
				$terms          = get_the_terms( $id, 'geo_type' );
				$geometry_type  = ! empty( $terms ) ? sanitize_title( current( $terms )->name ) : false;
			}
		}

		$class_name = $this->make_geometry_class_name( $geometry_type );
		// Filter class_name so that the class can be overridden if extended.
		return apply_filters( 'geobench_geometry_class', $class_name, $geometry_type, $post_type, $id );
	}

	/**
	 * Get class name from geometry type label.
	 *
	 * Geometry <type> is expected to instantiate from class `GeoBench\Geometries\<Type>`.
	 *
	 * @param  string $geometry_type
	 *
	 * @return bool|string
	 */
	private function make_geometry_class_name( $geometry_type ) {
		return $geometry_type ? 'Geometries\\' . implode( '_', array_map( 'ucfirst', explode( '-', $geometry_type ) ) ) : false;
	}

}
