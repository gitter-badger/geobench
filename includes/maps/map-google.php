<?php
/**
 * Google Maps provider.
 *
 * @class GB_GoogleMaps
 * @package GeoBench/Providers
 */

namespace GeoBench\Maps;
use GeoBench\Map as Map;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class GB_GoogleMaps
 *
 * @since 1.0.0
 */
class Google extends Map {

	/**
	 * Construct.
	 *
	 * @param Map|int|object $map
	 */
	public function __construct( $map ) {
		$this->type = 'google';
		$this->version = '3.2.0';
		parent::__construct( $map );
	}

	public function output() {

	}

}
