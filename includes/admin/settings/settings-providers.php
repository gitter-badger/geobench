<?php
/**
 * GeoBench General Settings
 *
 * @package GeoBench/Admin/Settings
 */

namespace GeoBench\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * General Settings.
 *
 * Renders the general settings page.
 */
class Providers_Settings extends Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id = 'providers_settings';
		$this->label = __( 'Service Providers', 'geobench' );
		$this->description = __( 'Configure your service providers here...', 'geobench' );
		parent::__construct();
	}

	/**
	 * Add sections.
	 *
	 * @return array
	 */
	public function add_sections() {
		return apply_filters( 'geobench_add_' . $this->id .'_sections', array(
			'maps' => array(
				'title' => __( 'Map services', 'geobench' ),
				'description' => __( 'This is a description' )
			)
		) );
	}

	/**
	 * Add fields.
	 *
	 * @return array
	 */
	public function add_fields() {

		$fields = array();

		$fields['maps'] = array(
			array()
		);

		return apply_filters( 'geobench_add_providers_settings_fields', $fields );
	}

	/**
	 * @param array $setting
	 *
	 * @return array
	 */
	public function validate( $setting ) {
		return $setting;
	}

}

return new Providers_Settings();
