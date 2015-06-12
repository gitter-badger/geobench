<?php
/**
 * Mapping service provider adapter
 *
 * Adapter class to handle different map data services.
 *
 * @package GeoBench/Abstracts
 */

namespace GeoBench;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * GeoBench Map.
 *
 * Abstract class for mapping and geo-location service maps.
 */
abstract class Map {

	/**
	 * Map id.
	 *
	 * @access public
	 * @var int
	 */
	public $id = 0;

	/**
	 * Map type.
	 *
	 * @access public
	 * @var string
	 */
	public $type = '';

	/**
	 * The map post object.
	 *
	 * Holds WordPress post data for the map object.
	 *
	 * @access public
	 * @var \WP_Post $post
	 */
	public $post = null;

	/**
	 * Map Service Provider API key.
	 *
	 * @access protected
	 * @var array
	 */
	protected $api = null;

	/**
	 * Map Service API version.
	 *
	 * @access public
	 * @var string
	 */
	public $version = '';

	/**
	 * Geometry constructor.
	 *
	 * Gets the post object and sets the ID for the loaded geo object.
	 *
	 * @param int|Map|object $map Geo ID, post object, or instance of this class.
	 */
	public function __construct( $map ) {
		if ( is_numeric( $map ) ) {
			$this->id   = absint( $map );
			$this->post = get_post( $this->id );
		} elseif ( $map instanceof Map ) {
			$this->id   = absint( $map->id );
			$this->post = $map->post;
		} elseif ( isset( $map->ID ) ) {
			$this->id   = absint( $map->ID );
			$this->post = $map;
		}
		if ( $this->id > 0 && $this->type ) {
			$api = \get_option( 'geobench_maps' );
			$this->api = isset( $api['map_types'][$this->type]['api'] ) ? $api['map_types'][$this->type]['api'] : null;
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
	 * Output map.
	 */
	abstract public function output();

}
