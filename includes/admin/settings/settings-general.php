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
			'api' => array(
				'title' => __( 'General options', 'geobench' ),
				'description' => __( 'This is a description' )
			),
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

		$fields['api'] = array(
			array(
				'title'         => __( 'GeoBench API', 'geobench' ),
				'description'   => __( 'Enable REST API', 'geobench' ),
				'id'            => 'geobench_api_enable',
				'name'          => 'geobench_' . $this->id . '[api][enable]',
				'default'       => 'yes',
				'type'          => 'checkbox'
			)
		);

		// Default content groups
		$content_groups = apply_filters( 'geobench_get_content_groups', array(
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
		$content_group_types = apply_filters( 'geobench_get_content_group_subtypes', array(
			'posts' => $post_content_types
		) );

		foreach ( $content_group_types as $content_group => $options ) {
			if ( isset( $content_groups[ $content_group ] ) ) {
				$fields['content_integration'][] = array(
					'id'          => 'geobench_content_integration_' . $content_group,
					'name'        => 'geobench_' . $this->id . '[content_integration][' . $content_group . ']',
					'title'       => $content_groups[ $content_group ],
					'description' => sprintf( __( 'Select which content among %s should have geo data.', 'geobench' ), strtolower( $content_groups[ $content_group ] ) ),
					'type'        => 'select',
					'multiselect' => 'multiselect',
					'classes'     => 'geobench-enhanced-select-tags',
					'options'     => $options,
					'default'     => 'post'
				);
			}
		}

		return apply_filters( 'geobench_add_' . $this->id . '_fields', $fields );
	}

}

return new General_Settings();
