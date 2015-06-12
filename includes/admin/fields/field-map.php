<?php
/**
 * Map field
 *
 * @package GeoBench/Admin/Fields
 */

namespace GeoBench\Admin\Fields;
use GeoBench\Field as Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Map field.
 */
class Map extends Field {

	/**
	 * Construct.
	 *
	 * @param $field
	 */
	public function __construct( $field ) {
		parent::__construct($field);
	}

	/**
	 * Outputs the field markup.
	 */
	protected function markup() {

		$this->get_wrapper( $this->context, 'start' );

		echo '<span class="description">' . wp_kses_post( $this->description ) . '</span>';
		?>
		<label for="geobench_geocode_field">
			<input
				name="geobench_geocode_field"
				id="geobench_geocode_field"
				type="text"
				value=""
				placeholder="<?php __( 'Enter a location', 'geobench' ); ?>"
				/>
		</label>
		<!-- map should go here -->
		<input
			name="<?php echo $this->name; ?>"
			id="<?php echo $this->id; ?>"
			type="hidden"
			value="<?php echo $this->value; ?>"
			<?php echo implode( ' ', $this->attributes ); ?>
			/>
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
