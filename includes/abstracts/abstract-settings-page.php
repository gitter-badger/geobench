<?php
/**
 * Settings Page
 *
 * @package  GeoBench/Admin/Settings
 */

namespace GeoBench\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Settings page.
 *
 * Abstract class for settings pages.
 */
abstract class Settings_Page {

	/**
	 * Settings page ID.
	 *
	 * @access protected
	 * @var string
	 */
	public $id = '';

	/**
	 * Settings page name label.
	 *
	 * @access protected
	 * @var string
	 */
	public $label = '';

	/**
	 * Settings page description.
	 *
	 * @access protected
	 * @var string
	 */
	public $description = '';

	/**
	 * Settings page sections.
	 *
	 * @access public
	 * @var array Associative array with section id (key) and section name (value)
	 */
	public $sections;

	/**
	 * Settings page fields.
	 *
	 * @access public
	 * @var array
	 */
	public $fields;

	/**
	 * Page Constructor.
	 */
	public function __construct() {
		add_filter( 'geobench_get_settings', array( $this, 'add_settings' ) );
	}

	/**
	 * Add this page to settings.
	 *
	 * @param  array $settings
	 *
	 * @return array
	 */
	public function add_settings( $settings ) {
		$settings[$this->id] = $this;
		return apply_filters( 'geobench_add_settings_' . $this->id  , $settings );
	}

	/**
	 * Get page settings.
	 *
	 * @return array
	 */
	public function get_settings() {
		$settings = array();
		$settings[$this->id] = array(
			'label' => $this->label,
			'description' => $this->description
		);
		if ( $this->sections && is_array( $this->sections ) ) {
			foreach ( $this->sections as $id => $content ) {
				$settings[$this->id]['sections'][$id] = array(
					'title'         => $content['title'],
					'description'   => $content['description'],
					'callback'      => array( $this, 'add_settings_section_callback' ),
					'fields'        => isset( $this->fields[$id] ) ? $this->fields[$id] : ''
				);
			}
		}
		return apply_filters( 'geobench_get_settings_' . $this->id , $settings );
	}

	/**
	 * Add sections for this page.
	 *
	 * @return array
	 */
	abstract public function add_sections();

	/**
	 * Get settings fields.
	 *
	 * @return array
	 */
	abstract public function add_fields();

	/**
	 * Default basic callback for page sections.
	 *
	 * @param  array $section
	 *
	 * @return string
	 */
	public function add_settings_section_callback( $section ) {
		$callback = isset( $section['callback'][0] ) ? $section['callback'][0] : '';
		$sections = isset( $callback->sections ) ? $callback->sections : '';
		$description = isset( $sections[$section['id']]['description'] ) ? $sections[$section['id']]['description'] : '';
		$default = $description ? '<p>' . $description . '</p>' : '';
		echo apply_filters( 'geobench_' . $this->id . '_sections_callback', $default );
	}

	/**
	 * Register setting callback.
	 *
	 * Callback function for sanitizing and validating options before they are updated.
	 *
	 * @param  array $setting
	 *
	 * @return array
	 */
	abstract public function validate( $setting );

}
