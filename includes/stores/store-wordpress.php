<?php
/**
 * Interface to local WordPress database.
 *
 * To perform geo queries within the local instance of $wpdb.
 *
 * @since 1.0.0
 *
 * @package GeoBench/Databases
 */

namespace GeoBench\Stores;
use GeoBench\Store as Store;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WordPress Database class
 *
 * Database adapter to work with geometries in the local WordPress database.
 * Note: class name is not camel-case 'WordPress' on purpose.
 * Class names match the stores type string value which is always lowercase.
 *
 * @since 1.0.0
 */
class Wordpress extends Store {

	/**
	 * Define Database properties.
	 */
	public function __construct() {
		$this->label = __( 'WordPress', 'geobench' );
		$this->type = 'wordpress';
		$this->supports = array( 'coordinates' );
		$this->version = GB_DB_VERSION;
	}

	/**
	 * Settings fields.
	 *
	 * @return array
	 */
	public function settings_fields() {
		return array(
			array(
				'title'         => $this->label,
				'description'   => __( 'Using the WordPress database does not require further configuration.', 'geobench' ),
				'type'          => 'section',
				'position'      => 'start',
				'id' 	        => 'wordpress_settings'
			),
			array(
				'type'          => 'section',
				'position'      => 'end',
				'id' 	        => 'wordpress_settings'
			)
		);
	}


	/**
	 * Save Geometry
	 *
	 * Saves geometry to local database.
	 *
	 * @param  string $geometry_type
	 * @param  mixed  $geometry_data
	 *
	 * @return bool|int Returns false if unsuccessful or the ID of the newly created geometry.
	 */
	public function save( $geometry_type, $geometry_data ) {

		if ( in_array( $geometry_type, $this->supports ) && ! empty( $geometry_data ) ) {

			global $wpdb;

			if ( 'coordinates' === $geometry_type ) {

				if ( isset( $geometry_data['lat'] ) && isset( $geometry_data['lng'] ) && isset( $geometry_data['geo_id'] ) ) {

					$table = $wpdb->prefix . 'geo_coordinates';
					$wpdb->insert(
						$table,
						array(
							'id'    => absint( $geometry_data['geo_id'] ),
							'lat'   => floatval( $geometry_data['lat'] ),
							'lng'   => floatval( $geometry_data['lng'] )
						),
						array( '%d', '%f', '%f' )
					);

					return true;
				}

			}

		}

		return false;
	}

	/**
	 * Get Geometry
	 *
	 * Queries the local database for a specific geometry.
	 *
	 * @param  int|array $geometry_id   ONeSet to -1 to return all geometries of the specified type.
	 * @param  string    $geometry_type Geometry type.
	 *
	 * @return bool|array|object
	 */
	public function get( $geometry_type, $geometry_id ) {

		if ( in_array( $geometry_type, $this->supports ) && is_int( $geometry_id ) ) {

			global $wpdb;

			if ( 'coordinates' === $geometry_type ) {

				$table  = $wpdb->prefix . 'geo_coordinates';

				if ( $geometry_id === -1 ) {

					return $wpdb->get_results(
						"SELECT * FROM {$table}"
					);

				} else {

					if ( is_numeric( $geometry_id ) ) {
						$geometry_id  = absint( $geometry_id );
						return $wpdb->get_row( $wpdb->prepare(
							"SELECT * FROM {$table} WHERE id = %d", $geometry_id
						) );
					} elseif( is_array( $geometry_id ) ) {
						$geometry_ids = array_map( 'absint', $geometry_id );
						return $wpdb->get_row(
							"SELECT * FROM {$table} WHERE id IN {$geometry_ids}"
						);
					}

					return false;
				}

			}

		}

		return false;
	}

	/**
	 * Update Geometry
	 *
	 * Updates geometry in local database.
	 *
	 * @param  string $geometry_type
	 * @param  int    $geometry_id
	 * @param  mixed  $geometry_data
	 *
	 * @return bool|int Returns false if unsuccessful or the ID of the newly created geometry.
	 */
	public function update( $geometry_type, $geometry_id, $geometry_data ) {

		if ( in_array( $geometry_type, $this->supports ) && ! empty( $geometry_data ) ) {

			global $wpdb;

			if ( 'coordinates' === $geometry_type ) {

				if ( isset( $geometry_data['lat'] ) && isset( $geometry_data['lng'] ) || is_int( $geometry_id ) ) {

					$table = $wpdb->prefix . 'geo_coordinates';
					$wpdb->replace(
						$table,
						array(
							'id'    => absint( $geometry_id ),
							'lat'   => floatval( $geometry_data['lat'] ),
							'lng'   => floatval( $geometry_data['lng'] ),
						),
						array( '%d', '%f', '%f' )
					);

					return true;
				}

			}

		}

		return false;
	}

	/**
	 * Delete Geometry
	 *
	 * Deletes geometry from local database.
	 *
	 * @param  string    $geometry_type
	 * @param  int|array $geometry_id
	 *
	 * @return bool Was query executed or not.
	 */
	public function delete( $geometry_type, $geometry_id ) {

		if ( ! empty ( $geometry_id ) && in_array( $geometry_type, $this->supports ) ) {

			global $wpdb;

			if ( 'coordinates' === $geometry_type ) {

				$table = $wpdb->prefix . 'geo_coordinates';
				$wpdb->delete( $table, array( 'id' => $geometry_id ), array( '%d' ) );

				return true;
			}

		}

		return false;
	}

}
