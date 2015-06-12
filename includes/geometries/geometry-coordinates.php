<?php
/**
 * Coordinates
 *
 * @package GeoBench\Geometries
 */

namespace GeoBench;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Coordinates geometry.
 */
class Coordinates extends Geometry {

	/**
	 * Latitude.
	 *
	 * @access public
	 * @var float
	 */
	public $lat = 0.000000;

	/**
	 * Longitude.
	 *
	 * @access public
	 * @var float
	 */
	public $lng = 0.000000;

	/**
	 * Construct.
	 *
	 * @param Geometry|int|object $geo
	 */
	public function __construct( $geo ) {
		$this->type = 'coordinates';
		parent::__construct( $geo );
		if ( $this->store ) {
			$geo_data = $this->get();
			$this->lat = isset( $geo_data->lat ) ? $geo_data->lat : $this->lat;
			$this->lng = isset( $geo_data->lng ) ? $geo_data->lng : $this->lng;
		}
	}

}
