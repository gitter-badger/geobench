<?php
/**
 * Select Field
 *
 * @package GeoBench/Admin/Fields
 */

namespace GeoBench\Admin\Fields;
use GeoBench\Field as Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Select field.
 */
class Select extends Field {

	/**
	 * Multiselect.
	 *
	 * @var bool
	 */
	public $multiselect = false;

	/**
	 * Construct.
	 *
	 * @param $field
	 */
	public function __construct( $field ) {
		$multiselect = isset( $field['multiselect'] ) ? $field['multiselect'] : '';
		$this->multiselect = $multiselect == 'multiselect' ? true : false;
		parent::__construct( $field );
	}

	/**
	 * Outputs the field markup.
	 */
	protected function markup() {

		$this->get_wrapper( $this->context, 'start' );

		if ( $this->multiselect === true ) {
			echo '<p class="description">' . wp_kses_post( $this->description ) . ' ' . $this->tooltip . '</p><br/>';
		}

		?>
		<select
			name="<?php echo $this->name; ?><?php if ( $this->multiselect === true ) echo '[]'; ?>"
			id="<?php echo $this->id; ?>"
			style="<?php echo $this->css; ?>"
			class="<?php echo $this->class; ?>"
			<?php echo implode( ' ', $this->attributes ); ?>
			<?php echo ( $this->multiselect === true ) ? 'multiple="multiple"' : ''; ?>
			>
			<?php foreach ( $this->options as $key => $val ) : ?>
				<option value="<?php echo esc_attr( $key ); ?>"
					<?php
					if ( is_array( $this->value ) ) {
						selected( in_array( $key, $this->value ), true );
					} else {
						selected( $this->value, $key );
					}
					?>><?php echo $val; ?></option>
			<?php endforeach; ?>
		</select>
		<?php

		if ( $this->multiselect === false ) {
			echo '<span class="description">' . wp_kses_post( $this->description ) . ' ' . $this->tooltip . '</span>';
		}

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
		if ( $this->type == 'multiselect' ) {
			return array_filter( array_map( 'sanitize_text_field', $value ) );
		}
		return sanitize_text_field( $value );
	}

}
