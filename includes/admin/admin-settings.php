<?php
/**
 * GeoBench Admin Settings Class
 *
 * Orchestrate GeoBench options inside the WordPress Settings API.
 *
 * @package GeoBench/Admin/Settings
 */

namespace GeoBench\Admin;
use GeoBench\Field as Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Admin Settings class.
 *
 * Static class to handle GeoBench options with the WordPress Settings API.
 */
class Settings {

	/**
	 * Settings pages.
	 *
	 * @access private
	 * @var array
	 */
	private static $settings = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Settings page abstract
		include_once dirname( __FILE__ ) . '/../abstracts/abstract-settings-page.php';
		// Default settings pages
		include_once 'settings/settings-general.php';
		include_once 'settings/settings-providers.php';
		// Extend
		do_action( 'geobench_load_settings_pages' );
		// Get settings
		self::$settings = apply_filters( 'geobench_get_settings', array() );
	}

	/**
	 * Get settings pages.
	 *
	 * @return array
	 */
	public static function get_settings() {
		$settings = array();
		if ( self::$settings and is_array( self::$settings ) ) {
			foreach( self::$settings as $id => $object ) {
				if ( $object instanceof Settings_Page ) {
					$settings_page = $object->get_settings();
					if ( isset( $settings_page[$id] ) ) {
						$settings[$id] = $settings_page[$id];
					}
				}
			}
		}
		return $settings;
	}

	/**
	 * Get a specific settings page object.
	 *
	 * @param  string $id
	 *
	 * @return bool|null|\GeoBench\Admin\Settings_Page
	 */
	public static function get_settings_page( $id ) {
		$settings_page_class = self::make_settings_page_class_name( $id );
		if ( class_exists( $settings_page_class ) ) {
			$settings_page = new $settings_page_class;
			if ( $settings_page instanceof Settings_Page ) {
				return $settings_page;
			}
			return false;
		}
		return null;
	}

	/**
	 * Get settings page class name from id.
	 *
	 * @access private
	 *
	 * @param  $id
	 *
	 * @return bool|string
	 */
	private static function make_settings_page_class_name( $id ) {
		return $id ? __CLASS__ . implode( '_', array_map( 'ucfirst', explode( '-', $id ) ) ) : false;
	}

	/**
	 * Add settings sections and fields.
	 *
	 * Uses WordPress Settings API.
	 *
	 * @param array $settings
	 */
	public static function register_settings( $settings = array() ) {

		$settings = $settings ? $settings : self::get_settings();

		if ( $settings && is_array( $settings ) ) {

			foreach( $settings as $page_id => $settings_page ) {

				if ( isset( $settings_page['sections'] ) ) {

					$sections = $settings_page['sections'];

					if ( $sections && is_array( $sections ) ) {

						foreach ( $sections as $section_id => $section ) {

							add_settings_section(
								$section_id,
								isset( $section['title'] ) ? $section['title'] : '',
								isset( $section['callback'] ) ? $section['callback'] : '',
								'geobench_' . $page_id
							);

							if ( isset( $section['fields'] ) ) {

								$fields = $section['fields'];

								if ( $fields && is_array( $fields ) ) {

									foreach ( $fields as $field ) {

										if ( isset( $field['id'] ) && isset( $field['type'] ) ) {

											$field_object = GB()->field_factory->get_field_object( $field, $field['type'], false );

											add_settings_field(
												$field['id'],
												isset( $field['title'] ) ? $field['title'] : '',
												$field_object && ! is_null( $field_object ) ? array( $field_object, 'print_field' ) : '',
												'geobench_' . $page_id,
												$section_id
											);

										} // is field valid

									} // loop fields

								} // are fields non empty?

							} // are there fields?

							$page = self::get_settings_page( $page_id );

							register_setting(
								'geobench_' . $page_id,
								$section_id,
								$page instanceof Settings_Page ? array( $page, 'register_setting_callback' ) : ''
							);

						} // loop sections

					} // are sections non empty?

				} // are there sections?

			} // loop settings

		} // are there settings?

	}

	/**
	 * Settings page.
	 *
	 * Handles the display of the main GeoBench settings page in admin.
	 *
	 * @param string $current_tab
	 */
	public static function output_page( $current_tab = 'general_settings' ) {

		global $current_tab;

		do_action( 'geobench_settings_start' );

		// Include settings pages
		$settings_pages = self::get_settings();

		// Get current tab/section
		$current_tab = empty( $_GET['tab'] ) ? 'general_settings' : sanitize_title( $_GET['tab'] );

		// Print settings page, tabbed navigation, sections and fields
		?>
		<div class="wrap geobench">
			<form method="<?php echo esc_attr( apply_filters( 'geobench_settings_form_method_tab_' . $current_tab, 'post' ) ); ?>" id="mainform" action="" enctype="multipart/form-data">

				<div class="icon32 icon32-geobench-settings" id="icon-geobench"><br /></div>

				<h2 class="nav-tab-wrapper geobench-nav-tab-wrapper">
					<?php

					// Get tabs for the settings page
					if ( $settings_pages && is_array( $settings_pages ) ) {
						foreach ( $settings_pages as $id => $settings ) {
							$name  = isset( $id ) ? $id : '';
							$label = isset( $settings['label'] ) ? $settings['label'] : '';
							echo '<a href="' . admin_url( 'admin.php?page=geobench_settings&tab=' . $name ) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
						}
					}

					do_action( 'geobench_settings_tabs' );

					?>
				</h2>
				<?php

				settings_errors();

				if ( $settings_pages && is_array( $settings_pages ) ) {
					foreach ( $settings_pages as $section => $contents ) {
						if ( $section === $current_tab ) {
							echo isset( $contents['description'] ) ? '<p>' . $contents['description'] . '</p>' : '';
							settings_fields( 'geobench_' . $current_tab );
							do_settings_sections( 'geobench_' . $current_tab );
						}
					}
				}

				submit_button();

				?>

			</form>
		</div>
		<?php

		do_action( 'geobench_settings_end' );

	}

}
