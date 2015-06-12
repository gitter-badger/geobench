<?php
/**
 * Geometry
 *
 * @package GeoBench/Abstracts
 */

namespace GeoBench;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GeoBench Geometry.
 */
abstract class Geometry {

	/**
	 * Geometry id.
	 *
	 * Matches 'geo' post type id.
	 *
	 * @access public
	 * @var int $id
	 */
	public $id = 0;

	/**
	 * Geometry type.
	 *
	 * Matches with the corresponding 'geo_type' taxonomy term and class name.
	 *
	 * @access public
	 * @var string
	 */
	public $type = '';

	/**
	 * The geo post object.
	 *
	 * WordPress $post data for the matching queried 'geo' post type.
	 *
	 * @access public
	 * @var \WP_Post|null
	 */
	public $post = null;

	/**
	 * Data stores used by the geometry.
	 *
	 * @var Store|null
	 */
	protected $store = null;

	/**
	 * Geometry constructor.
	 *
	 * Gets the post object and sets the ID for the loaded geo object.
	 *
	 * @param int|Geometry|object $geo Geo ID, post object, or instance of this class.
	 */
	public function __construct( $geo ) {
		if ( is_numeric( $geo ) ) {
			$this->id   = absint( $geo );
			$this->post = get_post( $this->id );
		} elseif ( $geo instanceof Geometry ) {
			$this->id   = absint( $geo->id );
			$this->post = $geo->post;
		} elseif ( isset( $geo->ID ) ) {
			$this->id   = absint( $geo->ID );
			$this->post = $geo;
		}
		if ( $this->id > 0 && is_string( $this->type ) ) {
			// Unless has been set, tries to determine the stores by descriptor record or by default option.
			$this->store    = ! $this->store instanceof Store ? ( ( $store = $this->get_store() ) instanceof Store ? $store : $this->get_default_store() ) : $this->store;
		}
	}

	/**
	 * __isset function.
	 *
	 * @param mixed $key
	 *
	 * @return bool
	 */
	public function __isset( $key ) {
		return metadata_exists( 'post', $this->id, '_' . $key );
	}

	/**
	 * __get function.
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function __get( $key ) {
		$value = get_post_meta( $this->id, '_' . $key, true );
		if ( ! empty( $value ) ) {
			$this->$key = $value;
		}
		return $value;
	}

	/**
	 * Get the geometry post data.
	 *
	 * @return \WP_POST Post object.
	 */
	public function get_post_data() {
		return $this->post;
	}

	/**
	 * Get the geometry object title.
	 *
	 * Returns the matching post title.
	 *
	 * @return string
	 */
	public function get_title() {
		return apply_filters( 'geobench_geo_title', $this->post ? $this->post->post_title : '', $this );
	}

	/**
	 * Get default data stores.
	 *
	 * Returns an instance of the default data stores for this geometry.
	 *
	 * @return Store|null
	 */
	private function get_default_store() {
		$geometries = \get_option( 'geobench_geometries' );
		if ( $this->type ) {
			$store = isset( $geometries[$this->type]['stores'] ) ? $geometries[$this->type]['stores'] : '';
			$store_class = 'GeoBench\Stores\\' . $store;
			if ( class_exists( $store_class ) ) {
				return new $store_class;
			}
		}
		return null;
	}

	/**
	 * Get data store.
	 *
	 * Returns the data store associated to this geometry.
	 *
	 * @return Store|null
	 */
	protected function get_store() {

		global $wpdb;
		$id = absint( $this->id );
		$table = $wpdb->prefix . 'geo_data';
		$store = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM {$table}, WHERE geo_id = %d", $id
		), ARRAY_A );

		return $store ? $store : null;
	}

	/**
	 * Get store type.
	 *
	 * @return bool|string
	 */
	public function get_store_type() {
		if ( $this->store instanceof Store ) {
			return $this->store->type;
		}
		return false;
	}

	/**
	 * Save geometry data.
	 *
	 * @param bool|mixed|null
	 *
	 * @return bool|mixed|null
	 */
	public function save( $args ) {

		if ( ! $this->id || ! $this->type || ! $this->post || ! $this->store instanceof Store ) {
			return null;
		}

		global $wpdb;
		$wpdb->insert(
			$wpdb->prefix . 'geo_data',
			array(
				'geo_id'        => absint( $this->id ),
				'geo_type'      => $this->type,
				'store'         => $this->store->type,
				'status'        => $this->post->post_status,
				'date_created'  => $this->post->post_modified_gmt,
				'date_modified' => $this->post->post_date_gmt
			),
			array( '%d', '%s', '%s', '%s', '%s', '%s' )
		);

		return $this->store->save( $this->type, $this->id, $args );
	}

	/**
	 * Get geometry data.
	 *
	 * @return  bool|mixed|null
	 */
	public function get() {
		if ( $this->id > 0 && $this->type && $this->store instanceof Store ) {
			return $this->store->get( $this->type, $this->id );
		}
		return null;
	}

	/**
	 * Update geometry data.
	 *
	 * @param  array $args
	 *
	 * @return bool|mixed|null
	 */
	public function update( $args ) {

		if ( ! $this->id || ! $this->type || ! $this->post || ! $this->store instanceof Store ) {
			return null;
		}

		global $wpdb;
		$wpdb->update(
			$wpdb->prefix . 'geo_data',
			array(
				'geo_type'      => $this->type,
				'store'         => $this->store->type,
				'status'        => $this->post->post_status,
				'date_created'  => $this->post->post_modified_gmt,
				'date_modified' => $this->post->post_date_gmt
			),
			array( 'id' => absint( $this->id ) ),
			array( '%s', '%s', '%s', '%s', '%s' ),
			array( '%d' )
		);

		return $this->store->update( $this->type, $this->id, $args );
	}

	/**
	 * Delete geometry data.
	 *
	 * @return bool|mixed|null
	 */
	public function delete() {

		if ( ! $this->id || ! $this->type || ! $this->post || ! $this->store instanceof Store ) {
			return null;
		}

		global $wpdb;
		$wpdb->delete(
			$wpdb->prefix . 'geo_data',
			array( 'id' => absint( $this->id ) ),
			array( '%d')
		);

		return $this->store->delete( $this->type, $this->id );
	}

	/**
	 * Get connected data.
	 *
	 * Retrieves WordPress objects connected to geometry.
	 *
	 * @return array Associative array of WordPress objects with object types as keys and ids as values.
	 */
	public function get_connected() {

		global $wpdb;

		$id = absint( $this->id );
		$table = $wpdb->prefix . 'geo_relationships';
		$results = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM {$table}, WHERE geo_id = %d", $id
		), ARRAY_A );

		$connected = array();
		if ( $results ) {
			foreach ( $results as $result  ) {
				$connected[$result['object_type']][] = $result['object_id'];
			}
		}

		return $connected;
	}

}
