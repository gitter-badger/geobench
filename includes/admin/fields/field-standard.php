<?php
/**
 * Standard Field
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
 * Standard field.
 */
class Standard extends Field {

	/**
	 * Construct.
	 *
	 * @param $field
	 */
	public function __construct( $field ) {
		parent::__construct( $field );
	}

	/**
	 * Outputs the field markup.
	 */
	protected function markup() {

		$this->get_wrapper( $this->context, 'start' );

		?>
		<input
			name="<?php echo $this->name; ?>"
			id="<?php echo $this->id; ?>"
			type="<?php echo $this->type; ?>"
			style="<?php echo $this->css; ?>"
			value="<?php echo $this->value; ?>"
			class="<?php echo $this->class; ?>"
			placeholder="<?php echo $this->placeholder; ?>"
			<?php echo isset( $this->attributes ) ? implode( ' ', $this->attributes ) : ''; ?>
			/> <?php echo '<span class="description">' . wp_kses_post( $this->description ) . '</span>'; ?>
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
		if ( 'password' === $this->type ) {
			return wp_hash_password( $value );
		}
		return sanitize_text_field( $value );
	}

}
