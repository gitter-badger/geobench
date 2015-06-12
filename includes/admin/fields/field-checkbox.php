<?php
/**
 * Checkboxes field
 *
 * @package GeoBench/Admin/Fields
 */

namespace GeoBench\Admin\Fields;
use GeoBench\Field as Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Checkboxes input.
 *
 * @since 1.0.0
 */
class Checkbox extends Field {

	/**
	 * Is this a group of checkboxes?
	 *
	 * @access public
	 *
	 * @var bool
	 */
	public $checkboxgroup;

	/**
	 * Construct.
	 *
	 * @param $field
	 */
	public function __construct( $field ) {
		parent::__construct( $field );
		$this->checkboxgroup = isset( $field['checkboxgroup'] ) ? esc_attr( $field['checkboxgroup'] ) : '';
	}

	/**
	 * Outputs the field markup.
	 */
	protected function markup() {

		if ( ! isset( $value['checkboxgroup'] ) || 'start' == $value['checkboxgroup'] ) {
			$this->get_wrapper( $this->context, 'start' );
		}

		?>
		<fieldset>
			<?php
			if ( ! empty( $this->title ) ) {
				?>
				<legend class="screen-reader-text">
					<span><?php echo $this->title; ?></span>
				</legend>
				<?php
			}
			?>
			<label for="<?php echo $this->id; ?>">
				<input
					name="<?php echo $this->name; ?>"
					id="<?php echo $this->id; ?>"
					type="checkbox"
					value=""
					<?php checked( $this->value, 'yes' ); ?>
					<?php echo implode( ' ', $this->attributes ); ?>
					/>
					<?php echo wp_kses_post( $this->description ); ?>
			</label><?php echo $this->tooltip; ?>
		</fieldset>
		<?php

		if ( ! isset( $value['checkboxgroup'] ) || 'end' == $value['checkboxgroup'] ) {
			$this->get_wrapper( $this->context, 'end' );
		}

	}

	/**
	 * Sanitize field input.
	 *
	 * @param  mixed $value Value to sanitize.
	 *
	 * @return mixed Sanitized value.
	 */
	public function sanitize( $value ) {
		return is_null( $value ) ? 'no' : 'yes';
	}

}
