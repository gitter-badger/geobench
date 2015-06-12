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

/**
 * Geo Factory class.
 *
 * The GeoBench geo factory creating the right geo object.
 *
 * @since 1.0.0
 */
class Store_Factory {

	/**
	 * Get store types.
	 *
	 * @return array
	 */
	public function get_store_types() {
		return (array) apply_filters( 'geobench_get_store_types', array(
			'wordpress' => __( 'WordPress', 'geobench' )
		) );
	}

	/**
	 * Get geometry class instance.
	 *
	 * Determines the type of geometry and returns an instance of its class.
	 *
	 * @param string|Store|Geometry $store
	 *
	 * @return null|bool|Store
	 */
	public function get_store( $store ) {

		if ( $store instanceof Store ) {
			$store = $store->type;
		} elseif( $store instanceof Geometry ) {
			$store = $store->get_store_type();
		} elseif( ! is_string( $store ) ) {
			return false;
		}

		$store_class = $this->make_store_class_name( $store );;
		if ( class_exists( $store_class ) ) {
			return new $store_class();
		}

		return null;
	}

	/**
	 * Get class name from store object type.
	 *
	 * Geometry objects are expected to instantiate from a class `GeoBench\Stores\<Type>`.
	 *
	 * @param  string $store_type
	 *
	 * @return bool|string
	 */
	private function make_store_class_name( $store_type ) {
		return $store_type ? '\GeoBench\Stores\\' . implode( '_', array_map( 'ucfirst', explode( '-', $store_type ) ) ) : false;
	}

}
