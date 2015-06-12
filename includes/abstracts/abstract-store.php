<?php
/**
 * Store adapter
 *
 * An abstract class for interfacing with geo data stores.
 *
 * @since 1.0.0
 *
 * @package GeoBench/Abstracts
 */

namespace GeoBench;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GeoBench Database Class.
 *
 * An adapter to work with geo data with different data stores.
 */
abstract class Store {

	/**
	 * Database label.
	 *
	 * @access public
	 * @var string The data stores name, eg. 'WordPress database'.
	 */
	public $label = '';

	/**
	 * Database type.
	 *
	 * This should match with the data stores adapter class name.
	 * For example `GeoBench\Stores\Wordpress` matches type 'wordpress'.
	 * If you plan to introduce support for database 'MyDatabase',
	 * use `Mydatabase` for class name and `mydatabase' for type.
	 *
	 * @access public
	 * @var string
	 */
	public $type = '';

	/**
	 * Database version.
	 *
	 * @access public
	 * @var string The database adapter version, eg. '5.5.0'.
	 */
	public $version = '';

	/**
	 * Supported geometries.
	 *
	 * An array with geometries supported by the data stores.
	 *
	 * @access public
	 * @var array For example: `array( 'point', 'polygon' )`.
	 */
	public $supports = array();

	/**
	 * Store connection data.
	 *
	 * May vary according to data stores, but should be organized into an array.
	 *
	 * @access protected
	 * @var array
	 */
	protected $connection = null;

	/**
	 * Supported geometries.
	 *
	 * Returns a list of supported geometries by the store.
	 * Note: only returns available geometries compatible with the store.
	 *
	 * @uses GeoBench\Geometry_Factory
	 *
	 * @return array|bool Associative array with Geometry Type => Name.
	 */
	public function supported_geometries() {
		$supported_geometries = '';
		if ( ! empty( $this->supports ) && is_array( $this->supports ) ) {
			$registered = GB()->geometry_factory->get_geometry_types();
			foreach( $this->supports as $supported_geometry ) {
				if ( isset( $registered[$supported_geometry] ) ) {
					$supported_geometries[] = array(
						$supported_geometry => $registered[$supported_geometry]
					);
				}
			}
		}
		return $supported_geometries ? $supported_geometries : false;
	}

	/**
	 * Settings field config to setup connection.
	 *
	 * Should return an array compatible with GeoBench Settings API.
	 *
	 * @see GeoBench\Admin\settings
	 * @see GeoBench\Field
	 *
	 * @return array
	 */
	abstract public function settings_fields();

	/**
	 * Save data.
	 *
	 * 'Create' function of the CRUD stores interface.
	 *
	 * @param string $geometry_type Type of geo data to save.
	 * @param mixed  $geometry_data Geo data to save, according to type.
	 *
	 * @return mixed|bool Boolean value may be used to indicate success or failure.
	 */
	abstract public function save( $geometry_type, $geometry_data );

	/**
	 * Get data.
	 *
	 * 'Read' function of the CRUD stores interface.
	 *
	 * @see
	 *
	 * @param  string|array $geometry_type Geo data type (string) or types (array) to retrieve.
	 * @param  int|array    $geometry_id   Geo data id (string) or ids (array) to retrieve.
	 *
	 * @return mixed|bool Boolean value may be used to indicate success or failure.
	 */
	abstract public function get( $geometry_type, $geometry_id );

	/**
	 * Update data.
	 *
	 * 'Update' function of the CRUD stores interface.
	 *
	 * @param  string $geometry_type  Type of geo data to update.
	 * @param  int    $geometry_id    Id of geo data to update.
	 * @param  mixed  $geometry_data  Data to update, according to type.
	 *
	 * @return mixed|bool Boolean value may be used to indicate success or failure.
	 */
	abstract public function update( $geometry_type, $geometry_id, $geometry_data );

	/**
	 * Delete data.
	 *
	 * 'Delete' function of the CRUD stores interface.
	 *
	 * @param int|array $geometry_type Geo data to delete according to type (string) or (types).
	 * @param int|array $geometry_id   Id of record to delete or array of ids.
	 *
	 * @return mixed|bool Boolean value may be used to indicate success or failure.
	 */
	abstract public function delete( $geometry_type, $geometry_id );

}
