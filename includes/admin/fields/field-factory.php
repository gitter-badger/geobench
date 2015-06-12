<?php
/**
 * Field Factory
 *
 * @package GeoBench\Admin
 */

namespace GeoBench\Admin;
use GeoBench\Admin\Fields as Fields;
use GeoBench\Field as Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Field Factory
 */
class Field_Factory {

	/**
	 * Get field.
	 *
	 * Returns a field markup as a string.
	 *
	 * @param array  $args       Field data.
	 * @param string $field_type (Optional) Force field type.
	 * @param bool   $standard   (Optional) If field type can't be found, returns standard field.
	 *
	 * @return bool|Field|null
	 */
	public function get_field( $args, $field_type = '', $standard = true ) {
		$field = $this->get_field_object( $args, $field_type, $standard );
		if ( $field instanceof Field ) {
			return $field->get_field();
		}
		return false;
	}

	/**
	 * Print field.
	 *
	 * Echoes a field markup.
	 *
	 * @param array  $args       Field data.
	 * @param string $field_type (Optional) Force field type.
	 * @param bool   $standard   (Optional) If field type can't be found, returns standard field.
	 */
	public function the_field( $args, $field_type = '', $standard = true ) {
		$field = $this->get_field_object( $args, $field_type, $standard );
		if ( $field instanceof Field ) {
			$field->print_field();
		}
	}

	/**
	 * Sanitize a field input.
	 *
	 * @param string $field_type The type of field.
	 * @param mixed  $value      Value to sanitize.
	 *
	 * @return mixed|null
	 */
	public function sanitize_field( $field_type, $value ) {
		$field_class = $this->make_field_class_name( $field_type );
		if ( class_exists( $field_class ) ) {
			$field = new $field_class;
			if ( $field instanceof Field ) {
				return $field->sanitize( $value );
			}
		}
		return null;
	}

	/**
	 * Get field object.
	 *
	 * Returns a field object matching the type specified in arguments.
	 *
	 * @param array  $args       Field data.
	 * @param string $field_type Force field type.
	 * @param bool   $standard   If field type can't be found, returns standard field.
	 *
	 * @return bool|Field|null
	 */
	public function get_field_object( $args, $field_type, $standard ) {

		if ( ! empty( $field_type ) ) {
			$field = $this->make_field_class_name( $field_type );
		} elseif ( isset( $args['type'] ) ) {
			if ( ! empty ( $args['type'] ) ) {
				$field = $this->make_field_class_name( $args['type'] );
			} else {
				return null;
			}
		} else {
			return null;
		}

		if ( class_exists( $field ) ) {
			$field_object = new $field( $args );
			if ( $field_object instanceof Field ) {
				return $field_object;
			}
			return false;
		}

		if ( $standard === true ) {
			return new Fields\Standard( $args );
		}

		return false;
	}

	/**
	 * Get the field class from field name.
	 *
	 * Returns the field class name as a string.
	 *
	 * @access  private
	 *
	 * @param  $field_type
	 *
	 * @return bool|string
	 */
	private function make_field_class_name( $field_type ) {
		return $field_type ? 'GeoBench\\Admin\\Fields\\' . implode( '_', array_map( 'ucfirst', explode( '-', $field_type ) ) ) : false;
	}


}
