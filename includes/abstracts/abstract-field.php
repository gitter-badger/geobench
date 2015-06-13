<?php
/**
 * GeoBench Fields API
 *
 * @package GeoBench/Abstracts
 */

namespace GeoBench;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Field abstract class.
 *
 * Interface for standardizing input fields handling in GeoBench.
 */
abstract class Field {

	/**
	 * Context.
	 *
	 * Helper argument to alter the field markup according to context where it's printed.
	 *
	 * @access public
	 * @var string Either 'metabox', 'settings' or empty.
	 */
	public $context;

	/**
	 * Field type (or subtype).
	 *
	 * Any registered field type.
	 *
	 * @access public
	 * @var string For example 'text', 'hidden', 'select', 'textarea'...
	 */
	public $type;

	/**
	 * Field name.
	 *
	 * Argument for the field name attribute.
	 *
	 * @access public
	 * @var string
	 */
	public $name;

	/**
	 * Field ID.
	 *
	 * Argument for the field ID.
	 *
	 * @access public
	 * @var string
	 */
	public $id;

	/**
	 * Field label.
	 *
	 * Text to be passed as the field label.
	 *
	 * @access public
	 * @var string
	 */
	public $title;

	/**
	 * CSS classes.
	 *
	 * A string with one or more CSS classes (optional).
	 *
	 * @access public
	 * @var string
	 */
	public $class;

	/**
	 * CSS styles.
	 *
	 * A string with CSS styles (optional).
	 *
	 * @access public
	 * @var string
	 */
	public $css;

	/**
	 * Description.
	 *
	 * Additional text information printed next to the field.
	 *
	 * @access public
	 * @var string
	 */
	public $description;

	/**
	 * Tooltip text.
	 *
	 * If used a tooltip markup will be generated with this text.
	 *
	 * @access public
	 * @var string|bool
	 */
	public $tooltip;

	/**
	 * Attributes.
	 *
	 * Any custom attributes (for example data attributes and their value).
	 *
	 * @access public
	 * @var array|string
	 */
	public $attributes;

	/**
	 * Placeholder.
	 *
	 * A placeholder text, if the input supports it.
	 *
	 * @access public
	 * @var string
	 */
	public $placeholder;

	/**
	 * Options.
	 *
	 * For fields having multiple choices, options can be passed as an associative array.
	 *
	 * @access public
	 * @var array Associative array with option key for array key and label vor array value.
	 */
	public $options;

	/**
	 * Value.
	 *
	 * The saved option from database should be passed for this argument.
	 *
	 * @access public
	 * @var string
	 */
	public $value;

	/**
	 * Default value.
	 *
	 * Upon activation the option can be saved with this value.
	 *
	 * @access public
	 * @var string
	 */
	public $default;

	/**
	 * Construct the field.
	 *
	 * Escapes and sets every field property.
	 *
	 * @param array $field Field data.
	 */
	public function __construct( $field ) {

		// Field properties
		$this->title        = isset( $field['title'] )       ? esc_attr( $field['title'] ) : '';
		$this->description  = isset( $field['description'] ) ? esc_attr( $field['description'] ) : '';
		$this->type         = isset( $field['type'] )        ? esc_attr( $field['type'] ) : '';
		$this->context      = isset( $field['context'] )     ? esc_attr( $field['context'] ) : '';
		$this->id           = isset( $field['id'] )          ? esc_attr( $field['id'] ) : '';
		$this->name         = isset( $field['name'] )        ? esc_attr( $field['name'] ) : '';
		$this->class        = isset( $field['class'] )       ? esc_attr( $field['class'] ) : '';
		$this->css          = isset( $field['css'] )         ? esc_attr( $field['css'] ) : '';
		$this->placeholder  = isset( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : '';
		$this->options      = isset( $field['options'] )     ? array_map( 'esc_attr', (array) $field['options'] ) : '';
		$this->value        = isset( $field['value'] )       ? $this->esc_maybe_array( $field['value'] ) : '';
		$this->default      = isset( $field['default'] )     ? $this->esc_maybe_array( $field['default'] ) : '';

		// Custom Attributes
		$this->attributes = $custom_attributes = array();
		if ( isset( $field['attributes'] ) ) {
			if ( ! empty( $field['attributes'] ) && is_array( $field['attributes'] ) ) {
				foreach ( $field['attributes'] as $attribute_name => $attribute_value ) {
					$custom_attributes[] = esc_attr( $attribute_name ) . '="' . esc_attr( $attribute_value ) . '"';
				}
				$this->attributes = $custom_attributes;
			}
		}

		// Tooltip markup
		$this->tooltip = '';
		if ( ! empty( $this->tooltip ) ) {
			if ( in_array( $this->type, array( 'checkbox' ) ) ) {
				$this->tooltip = '<p class="description">' . $this->tooltip . '</p>';
			} else {
				$this->tooltip = '<img class="help_tip" data-tip="' . esc_attr( $this->tooltip ) . '" src="' . GB()->plugin_url() . '/assets/images/help.png" height="16" width="16" />';
			}
		}


	}

	/**
	 * Escape array (maybe).
	 *
	 * Helper function to escape values that could be arrays.
	 *
	 * @param  array|string|bool $maybe_array
	 *
	 * @return array|string|bool Escaped value.
	 */
	private function esc_maybe_array( $maybe_array ) {
		if ( is_null( $maybe_array ) ) {
			return '';
		} elseif ( is_array( $maybe_array ) ) {
			return array_map( 'esc_attr', $maybe_array );
		} else {
			return is_bool( $maybe_array ) ? $maybe_array : esc_attr( $maybe_array );
		}
	}

	/**
	 * Get field markup.
	 *
	 * @return string Returns the field HTML.
	 */
	public function get_field() {
		ob_start();
		$this->markup();
		return ob_get_clean();
	}

	/**
	 * Print the field markup.
	 *
	 * Outputs the field HTML.
	 */
	public function print_field() {
		echo $this->get_field();
	}

	/**
	 * Field Wrapper.
	 *
	 * Helper function to alter the markup wrapping the input according to context.
	 *
	 * @param  string $context  Either 'settings', 'metabox' or empty for no wrapping markup.
	 * @param  string $position Either 'start' or 'end' for opening or closing tags.
	 *
	 * @return string HTML markup.
	 */
	protected function get_wrapper( $context = '', $position = '' ) {

		// Field is displayed on a settings page
		if ( $context == 'settings' ) {
			if ( $position == 'start' ) {
				echo $this->tooltip;
			}
		// Field is displayed inside a metabox
		} elseif ( $context == 'metabox' ) {

			if ( $position == 'start' ) {


			} elseif( $position == 'end' ) {


			}

		}

	}

	/**
	 * Outputs the field markup.
	 */
	abstract protected function markup();

	/**
	 * Sanitize field input.
	 *
	 * @param  mixed $value Value to sanitize.
	 *
	 * @return mixed Sanitized value.
	 */
	abstract public function sanitize( $value );

}
