<?php
/**
 * GeoBench General Settings
 *
 * @package GeoBench/Admin/Settings
 */

namespace GeoBench\Admin\Settings;
use GeoBench\Admin\Settings_Page as Settings_Page;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * General Settings.
 *
 * Renders the general settings page.
 *
 */
class General_Settings extends Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id = 'general_settings';
		$this->label = __( 'General', 'geobench' );
		$this->description = __( 'General settings are made of this...', 'geobench' );
		$this->sections = $this->add_sections();
		$this->fields = $this->add_fields();
		parent::__construct();
	}

	/**
	 * Add sections.
	 *
	 * @return array
	 */
	public function add_sections() {
		return apply_filters( 'geobench_add_' . $this->id .'_sections', array(
			'content_integration' => array(
				'title' =>  __( 'Content integration', 'geobench' ),
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

		if ( $sections = $this->sections ) :

			$page_id = $this->id;
			$values = get_option( 'geobench_' . $page_id );

			foreach ( $sections as $section => $content ) :

				if( 'content_integration' == $section ) {

					// Default content groups
					$content_groups = apply_filters( 'geobench_integration_content_groups', array(
						'posts' => __( 'Posts', 'geobench' )
					) );

					// Default post types
					$post_types = get_post_types( array( 'publicly_queryable' => true ) );
					unset( $post_types['attachment'] );
					$post_content_types = array();
					foreach ( $post_types as $post_type ) {
						$post_type_object = get_post_type_object( $post_type );
						if ( ! isset( $post_type_object->labels->name ) ) {
							continue;
						}
						$post_content_types[ $post_type ] = $post_type_object->labels->name;
					}

					// Default content subtypes per group (default post types)
					$content_group_types = apply_filters( 'geobench_integration_content_group_subtypes', array(
						'posts' => $post_content_types
					), $content_groups );

					foreach ( $content_group_types as $content_group => $options ) {
						if ( isset( $content_groups[ $content_group ] ) ) {
							$fields[$section][] = array(
								'type'        => 'select',
								'multiselect' => 'multiselect',
								'name'        => 'geobench_'. $page_id . '[' . $section . '][' . $content_group . ']',
								'id'          => 'geobench-field-content-integration-' . $content_group,
								'class'       => 'geobench-field geobench-enhanced-select geobench-field-content-integration',
								'title'       => $content_groups[ $content_group ],
								'description' => sprintf( __( 'Select which type of %s should carry geo data.', 'geobench' ), strtolower( $content_groups[ $content_group ] ) ),
								'options'     => $options,
								'default'     => $content_group == 'posts' ? 'post' : '',
								'value'       => isset( $values[$section][$content_group] ) ? $values[$section][$content_group] : false
							);
						}
					}

				}

			endforeach;
		endif;

		return apply_filters( 'geobench_add_' . $this->id . '_fields', $fields );
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
	public function validate( $setting ) {

		$sanitized = array();
		foreach ( $setting as $option => $value ) :

			if ( 'content_integration ' == $option ) {
				if ( $value && is_array( $value ) ) {
					foreach( $value as $subgroup => $subtypes ) {
						$sanitized[$option][$subgroup] = array_map( 'sanitize_key', $subtypes );
					}
				}
			}

		endforeach;

		return apply_filters( 'geobench_validate_' . $this->id, $sanitized );
	}

}

return new General_Settings();
