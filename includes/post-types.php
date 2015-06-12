<?php
/**
 * Post Types
 *
 * Registers post types for GeoBench.
 *
 * @since 1.0.0
 *
 * @package GeoBench/Classes
 */

namespace GeoBench;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GeoBench Post Types class.
 *
 * @since 1.0.0
 */
class Post_Types {

	/**
	 * Hook in methods.
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
		add_action( 'init', array( __CLASS__, 'support_jetpack_omnisearch' ) );
	}

	/**
	 * Register core taxonomies.
	 *
	 * @since 1.0.0
	 */
	public static function register_taxonomies() {

		do_action( 'geobench_register_taxonomy' );

		if ( ! taxonomy_exists( 'geo_type' ) ) {

			register_taxonomy(
				'geo_type',
				'geo',
				array(
					'hierarchical'      => false,
					'show_ui'           => false,
					'show_in_nav_menus' => false,
					'query_var'         => is_admin(),
					'rewrite'           => false,
					'public'            => false
				)
			);

		}

		if ( ! taxonomy_exists( 'map_type' ) ) {

			register_taxonomy(
				'map_type',
				'map',
				array(
					'hierarchical'      => false,
					'show_ui'           => false,
					'show_in_nav_menus' => false,
					'query_var'         => is_admin(),
					'rewrite'           => false,
					'public'            => false
				)
			);

		}

	}

	/**
	 * Register GeoBench post types.
	 *
	 * @since 1.0.0
	 */
	public static function register_post_types() {

		do_action( 'geobench_register_post_type' );

		if ( ! post_type_exists( 'geo' ) ) {

			register_post_type( 'geo', apply_filters( 'geobench_register_post_type_geo', array(
						'labels'              => array(
							'name'               => __( 'Geo Objects', 'geobench' ),
							'singular_name'      => __( 'Geo Object', 'geobench' ),
							'menu_name'          => _x( 'Geo Objects', 'Admin menu name', 'geobench' ),
							'add_new'            => __( 'Add Geo Object', 'geobench' ),
							'add_new_item'       => __( 'Add New Geo Object', 'geobench' ),
							'edit'               => __( 'Edit', 'geobench' ),
							'edit_item'          => __( 'Edit Geo Object', 'geobench' ),
							'new_item'           => __( 'New Geo Object', 'geobench' ),
							'view'               => __( 'View Geo Object', 'geobench' ),
							'view_item'          => __( 'View Geo Object', 'geobench' ),
							'search_items'       => __( 'Search Geo Objects', 'geobench' ),
							'not_found'          => __( 'No Geo Objects found', 'geobench' ),
							'not_found_in_trash' => __( 'No Geo Objects found in trash', 'geobench' ),
							'parent'             => __( 'Parent Geo Object', 'geobench' )
						),
						'description'         => __( 'This is where you can view and edit geometries.', 'geobench' ),
						'public'              => false,
						'show_ui'             => true,
						'show_in_menu'        => false,
						'show_in_nav_menus'   => false,
						'capability_type'     => 'geo',
						'map_meta_cap'        => true,
						'publicly_queryable'  => false,
						'exclude_from_search' => true,
						'hierarchical'        => false,
						'rewrite'             => false,
						'query_var'           => false,
						'supports'            => array( 'title', 'custom-fields' ),
						'has_archive'         => false
					)
				)
			);
		}

		if ( ! post_type_exists( 'map' ) ) {

			register_post_type( 'map', apply_filters( 'geobench_register_post_type_map', array(
						'labels'              => array(
							'name'               => __( 'Maps', 'geobench' ),
							'singular_name'      => __( 'Map', 'geobench' ),
							'menu_name'          => _x( 'Maps', 'Admin menu name', 'geobench' ),
							'add_new'            => __( 'Add Map', 'geobench' ),
							'add_new_item'       => __( 'Add New Map', 'geobench' ),
							'edit'               => __( 'Edit', 'geobench' ),
							'edit_item'          => __( 'Edit Map', 'geobench' ),
							'new_item'           => __( 'New Map', 'geobench' ),
							'view'               => __( 'View Map', 'geobench' ),
							'view_item'          => __( 'View Map', 'geobench' ),
							'search_items'       => __( 'Search Maps', 'geobench' ),
							'not_found'          => __( 'No Maps found', 'geobench' ),
							'not_found_in_trash' => __( 'No Maps found in trash', 'geobench' ),
							'parent'             => __( 'Parent Map', 'geobench' )
						),
						'description'         => __( 'This is where you can view and edit maps.', 'geobench' ),
						'public'              => false,
						'show_ui'             => true,
						'show_in_menu'        => true,
						'show_in_nav_menus'   => false,
						'capability_type'     => 'map',
						'map_meta_cap'        => true,
						'publicly_queryable'  => false,
						'exclude_from_search' => true,
						'hierarchical'        => false,
						'rewrite'             => false,
						'query_var'           => false,
						'supports'            => array( 'title', 'custom-fields' ),
						'has_archive'         => false,
						'menu_icon'           => 'dashicons-location-alt',
						'menu_position'       => apply_filters( 'geobench_map_post_type_menu_position', 35 )
					)
				)
			);
		}

	}

	/**
	 * Add Map support to Jetpack plugin Omnisearch.
	 */
	public static function support_jetpack_omnisearch() {
		if ( class_exists( 'Jetpack_Omnisearch_Posts' ) ) {
			new Jetpack_Omnisearch_Posts( 'map' );
		}
	}

}

Post_Types::init();
