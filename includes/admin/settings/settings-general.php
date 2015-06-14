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
		$this->description = __( 'General settings.', 'geobench' );
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
				'description' => __(
					'GeoBench can attach geo data to other content.
					 You can choose here which WordPress items should integrate with GeoBench.',
					'geobench' )
			)
		) );
	}

	/**
	 * Add fields.
	 *
	 * @uses get_option() to retrieve any saved values.
	 *
	 * @return array
	 */
	public function add_fields() {

		$fields = '';

		if ( $sections = $this->sections ) :

			$page_id = $this->id;
			$values = get_option( 'geobench_' . $page_id );

			foreach ( $sections as $section => $content ) :

				if ( 'content_integration' == $section ) :

					// Default post types
					$post_types = get_post_types( array( 'publicly_queryable' => true ) );
					unset( $post_types['attachment'] );
					$post_content_types = array();
					foreach ( $post_types as $post_type ) {
						$post_type_object = get_post_type_object( $post_type );
						if ( ! isset( $post_type_object->labels->name ) ) {
							continue;
						}
						$post_content_types[$post_type] = $post_type_object->labels->name;
					}

					// Default content groups
					$content_groups = apply_filters( 'geobench_content_integration', array(
						'posts' => array(
							'label' => __( 'Posts', 'geobench' ),
							'types' => $post_content_types
						)
					) );

					foreach ( $content_groups as $content_group => $args ) {
						if ( isset( $args['label'] ) && isset( $args['types'] ) ) {
							if ( is_array( $args['types'] ) ) {
								$fields[$section][$content_group] = array(
									'type'        => 'select',
									'multiselect' => 'multiselect',
									'name'        => 'geobench_'. $page_id . '[' . $section . '][' . $content_group . ']',
									'id'          => 'geobench-field-content-integration-' . $content_group,
									'class'       => 'geobench-field geobench-enhanced-select geobench-field-content-integration',
									'title'       => $args['label'],
									'description' => sprintf( __( 'Select which type of %s should carry geo data.', 'geobench' ), strtolower( $content_group ) ),
									'options'     => $args['types'],
									'default'     => $content_group == 'posts' ? 'post' : '',
									'value'       => isset( $values[$section][$content_group] ) ? $values[$section][$content_group] : ''
								);
							}
						}
					}

				endif;

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
