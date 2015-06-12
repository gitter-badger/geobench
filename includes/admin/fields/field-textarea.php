<?php
/**
 * Textarea Field
 *
 * For standard text inputs and subtypes (e.g. number, password, email...).
 *
 * @package GeoBench/Admin/Fields
 */

namespace GeoBench\Admin\Fields;
use GeoBench\Field as Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Textarea field.
 */
class Textarea extends Field {

	/**
	 * Construct.
	 *
	 * @param $field
	 */
	public function __construct( $field ) {
		parent::__construct( $field );
		$this->value = isset( $field['value'] ) ? esc_textarea( $field['value'] ) : '';
		$this->default = isset( $field['default'] ) ? esc_textarea( $field['default'] ) : '';
	}

	/**
	 * Outputs the field markup.
	 */
	protected function markup() {

		$this->get_wrapper( $this->context, 'start' );

		echo isset( $this->description ) ? '<p style="margin-top:0">' . wp_kses_post( $this->description ) . '</p>' : '';
		?>
		<textarea
			name="<?php echo $this->name; ?>"
			id="<?php echo $this->id; ?>"
			style="<?php echo $this->css; ?>"
			class="<?php echo $this->class; ?>"
			placeholder="<?php echo $this->placeholder; ?>"
			<?php echo implode( ' ', $this->attributes ); ?>
			>
			<?php echo $this->value;  ?>
		</textarea>
		<?php

		$this->get_wrapper( $this->context, 'end' );

	}

	/**
	 * Sanitize field input.
	 *
	 * @param  mixed $value Value to sanitize.
	 *
	 * @return mixed Sanitized value.
	 */
	public function sanitize( $value ) {
		return sanitize_text_field( $value );
	}

}
