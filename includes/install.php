<?php
/**
 * GeoBench Installation
 *
 * Class methods handle plugin activation and deactivation.
 *
 * @package GeoBench/Classes
 */

namespace GeoBench;
use GeoBench\Admin\Settings as Settings;
use GeoBench\Admin\Settings_Page as Settings_Page;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * GeoBench Install static class.
 *
 * What happens in WordPress when GeoBench is installed and then activated or deactivated.
 */
class Install {

    /**
     * Hook in tabs.
     */
    public static function init() {
	    add_filter( 'plugin_action_links_' . GB_PLUGIN_BASENAME, array( __CLASS__, 'plugin_action_links' ) );
        add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
    }

    /**
     * Fired upon plugin activation.
     */
    public static function activate() {
	    self::set_roles_and_caps( 'add' );
        self::create_options();
        self::create_tables();
	    self::create_terms();
        do_action( 'geobench_activated' );
    }

    /**
     * Fired upon plugin deactivation.
     */
    public static function deactivate() {
	    self::set_roles_and_caps( 'remove' );
        do_action( 'geobench_deactivated' );
    }

    /**
     * Set default options.
     *
     * Loop through settings and add options.
     *
     * @uses GeoBench\Admin\Settings
     * @uses add_option()
     *
     * @access private
     */
    public static function create_options() {

	    include_once 'admin/admin-settings.php';
		$geobench_settings = new Settings;
	    $settings_pages = $geobench_settings::get_settings();

		$default = '';
	    foreach ( $settings_pages as $settings_page => $settings ) {

		    $group = 'geobench_' . $settings_page;

			if ( isset( $settings['sections'] ) ) {

				if ( $settings['sections'] && is_array( $settings['sections'] ) ) {

					foreach ( $settings['sections'] as $section_id => $section ) {

						if ( isset( $section['fields'] ) ) {

							if ( $section['fields'] && is_array( $section['fields'] ) ) {

								foreach ( $section['fields'] as $key => $field ) {

									$saved_value   = isset( $field['value']   ) ? $field['value']   : '';
									$default_value = isset( $field['default'] ) ? $field['default'] : '';

									if ( is_int( $key ) ) {
										$default[$section_id] = $saved_value ? $saved_value : $default_value;
									} else {
										$default[$section_id][$key] = $saved_value ? $saved_value : $default_value;
									}

								} // loop fields for saved values, fallback to default values

							} // are fields non empty?

						} // are there any fields?

					} // loop sections

				} // are sections non empty?

			} // are there settings sections?

		    add_option( $group, $default, '', true );
		    $default = '';

		} // loop settings

    }

	/**
	 * Create GeoBench tables.
	 *
	 * @uses dbDelta()
	 *
	 * @access private
	 */
	private static function create_tables() {
		global $wpdb;
		$wpdb->hide_errors();
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( self::get_schema() );
	}

    /**
     * GeoBench schema.
     *
     * @access private
     *
     * @return string
     */
    private static function get_schema() {

        global $wpdb;

	    $collate = '';
	    if ( $wpdb->has_cap( 'collation' ) ) {
		    if ( ! empty( $wpdb->charset ) ) {
			    $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
		    }
		    if ( ! empty( $wpdb->collate ) ) {
			    $collate .= " COLLATE $wpdb->collate";
		    }
	    }

	    // Tables prefix
	    $prefix = $wpdb->prefix . 'geo_';
	    // Table names
	    $coordinates    = $prefix . 'coordinates';
	    $data           = $prefix . 'data';
	    $relationships  = $prefix . 'relationships';

		return "

CREATE TABLE {$coordinates} (
  geo_id BIGINT(20) UNSIGNED NOT NULL default 0,
  lat DECIMAL(9,6) NOT NULL default 0,
  lng DECIMAL(9,6) NOT NULL default 0,
  PRIMARY KEY (geo_id, lat, lng),
  UNIQUE INDEX geo_id (geo_id),
  INDEX latlng (lat ASC, lng ASC)
) {$collate};

CREATE TABLE {$data} (
  geo_id BIGINT(20) UNSIGNED NOT NULL default 0,
  geo_type VARCHAR(20) NOT NULL default '',
  store VARCHAR(20) NULL,
  status VARCHAR(20) NULL,
  date_created DATETIME NOT NULL default '0000-00-00 00:00:00',
  date_modified DATETIME NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (geo_id, geo_type, store),
  UNIQUE INDEX geo_id (geo_id ASC),
  INDEX status (status ASC),
  INDEX date_created (date_created ASC),
  INDEX date_modified (date_modified ASC)
) {$collate};

CREATE TABLE {$relationships} (
  geo_id BIGINT(20) UNSIGNED NOT NULL default 0,
  object_id BIGINT(20) UNSIGNED NOT NULL default 0,
  object_type VARCHAR(20) NOT NULL,
  PRIMARY KEY (geo_id, object_id, object_type),
  UNIQUE INDEX geo_id (geo_id ASC),
  INDEX object (object_id ASC, object_type ASC),
  INDEX object_type (object_type ASC)
) {$collate};
		";

    }

	/**
	 * Create terms.
	 *
	 * Creates default terms for GeoBench taxonomies.
	 *
	 * @uses wp_insert_term()
	 */
	public static function create_terms() {
		$taxonomies = array(
			'geo_type' => array(
				'coordinates',
			),
			'map_type' => array(
				'google'
			)
		);
		foreach ( $taxonomies as $taxonomy => $terms ) {
			foreach ( $terms as $term ) {
				if ( ! get_term_by( 'slug', sanitize_title( $term ), $taxonomy ) ) {
					wp_insert_term( $term, $taxonomy );
				}
			}
		}
	}


	/**
	 * Create roles and capabilities.
	 *
	 * @uses \WP_Roles()
	 *
	 * @param string $action Either 'remove' or 'add'.
	 */
	public static function set_roles_and_caps( $action ) {

		if ( ! in_array( $action, array( 'remove', 'add' ) ) ) {
			return;
		}

		global $wp_roles;

		if ( ! class_exists( '\WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new \WP_Roles();
		}

		$capabilities = self::get_core_capabilities();
		foreach ( $capabilities as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				if ( $action == 'add' ) {
					$wp_roles->add_cap( 'administrator', $cap );
					$wp_roles->add_cap( 'editor', $cap );
				} elseif ( $action == 'remove' ) {
					$wp_roles->remove_cap( 'administrator', $cap );
					$wp_roles->remove_cap( 'editor', $cap );
				}
			}
		}

	}

	/**
	 * Get capabilities for GeoBench.
	 *
	 * @return array
	 */
	private static function get_core_capabilities() {

		$capabilities = array();
		$capabilities['core'] = array(
			'manage_geo_data'
		);

		$capability_types = array( 'geo', 'map' );
		foreach ( $capability_types as $capability_type ) {
			$capabilities[$capability_type] = array(
				// Post type
				"edit_{$capability_type}",
				"read_{$capability_type}",
				"delete_{$capability_type}",
				"edit_{$capability_type}s",
				"edit_others_{$capability_type}s",
				"publish_{$capability_type}s",
				"read_private_{$capability_type}s",
				"delete_{$capability_type}s",
				"delete_private_{$capability_type}s",
				"delete_published_{$capability_type}s",
				"delete_others_{$capability_type}s",
				"edit_private_{$capability_type}s",
				"edit_published_{$capability_type}s",
				// Terms
				"manage_{$capability_type}_terms",
				"edit_{$capability_type}_terms",
				"delete_{$capability_type}_terms",
				"assign_{$capability_type}_terms"
			);
		}

		return $capabilities;
	}

    /**
     * Show action links on the plugin screen.
     *
     * @param  mixed $links Plugin Action links.
     *
     * @return array
     */
    public static function plugin_action_links( $links ) {
        $action_links = array(
            'settings' => '<a href="' . admin_url( 'admin.php?page=geobench-settings' ) . '" title="' . esc_attr( __( 'View GeoBench Settings', 'geobench' ) ) . '">' . __( 'Settings', 'geobench' ) . '</a>',
        );
        return array_merge( $action_links, $links );
    }

    /**
     * Show row meta on the plugin screen.
     *
     * @param  mixed $links Plugin Row Meta.
     * @param  mixed $file  Plugin Base file.
     *
     * @return array
     */
    public static function plugin_row_meta( $links, $file ) {
        if ( $file == GB_PLUGIN_BASENAME ) {
            $row_meta = array(
                // nothing yet
            );
            return array_merge( $links, $row_meta );
        }
        return (array) $links;
    }

}

Install::init();
