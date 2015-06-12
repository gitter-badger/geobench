<?php
/**
 * Geo object factory
 *
 * Creates the right geo object according to geo object type.
 *
 * @since 1.0.0
 *
 * @package GeoBench/Maps
 */

namespace GeoBench;

/**
 * Geo Factory class.
 *
 * The GeoBench geo factory creating the right geo object.
 *
 * @since 1.0.0
 */
class Map_Factory {

	/**
	 * Get map types.
	 *
	 * @return array
	 */
	public function get_map_types() {
		return (array) apply_filters( 'geobench_get_map_types', array(
			'google' => __( 'Google Maps', 'geobench' )
		) );
	}

	/**
	 * Get map class instance.
	 *
	 * Determines the type of map and returns an instance of its class.
	 *
	 * @param  $map
	 * @param  $args
	 *
	 * @return null|bool|Map
	 */
	public function get_map( $map = false, $args = array() ) {
		$map_object = $this->get_map_object( $map );
		if ( ! $map_object ) {
			return false;
		}
		$map_class = $this->get_map_class( $map_object, $args );
		if ( class_exists( $map_class ) ) {
			return new $map_class( $map, $args );
		}
		return null;
	}

	/**
	 * Get map object.
	 *
	 * Returns the map object as WordPress post object.
	 *
	 * @param  int|object|Map|\WP_Post $map
	 *
	 * @return bool|object|\WP_Post
	 */
	private function get_map_object( $map ) {
		if ( false === $map ) {
			$map = $GLOBALS['post'];
		} elseif ( is_numeric( $map ) ) {
			$map = get_post( $map );
		} elseif ( $map instanceof Map ) {
			$map = get_post( $map->id );
		} elseif ( ! ( $map instanceof \WP_Post ) ) {
			$map = false;
		}
		return $map;
	}

	/**
	 * Get geometry class.
	 *
	 * @param  $map
	 * @param  $args
	 *
	 * @return mixed|void
	 */
	private function get_map_class( $map, $args ) {

		$id         = absint( $map->ID );
		$post_type  = $map->post_type;
		$map_type   = false;

		if ( 'map' === $post_type && $map->type && ! $args['map_type'] ) {
			$map_type = $map->type;
		} elseif ( 'map' === $post_type ) {
			if ( isset( $args['map_type'] ) ) {
				$map_type = $args['map_type'];
			} else {
				$terms     = get_the_terms( $id, 'map_type' );
				$map_type  = ! empty( $terms ) ? sanitize_title( current( $terms )->name ) : false;
			}
		}

		$class_name = $this->make_map_class_name( $map_type );
		// Filter class_name so that the class can be overridden if extended.
		return apply_filters( 'geobench_map_class', $class_name, $map_type, $post_type, $id );
	}

	/**
	 * Make map class from map type label.
	 *
	 * Map <type> is expected to instantiate from class `GeoBench\Maps\<type>`.
	 *
	 * @param  string $map_type
	 *
	 * @return bool|string
	 */
	private function make_map_class_name( $map_type ) {
		return $map_type ? 'GeoBench\Maps\\' . implode( '_', array_map( 'ucfirst', explode( '-', $map_type ) ) ) : false;
	}

}
