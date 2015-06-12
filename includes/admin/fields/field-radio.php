<?php
/**
 * Radio Field
 *
 * @package GeoBench/Admin/Fields
 */

namespace GeoBench\Admin\Fields;
use GeoBench\Field as Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Radio field.
 */
class Radio extends Field {

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

		?>
		<fieldset>
			<?php echo isset( $this->description ) ? '<p style="margin-top:0">' . wp_kses_post( $this->description ) . '</p>' : ''; ?>
			<ul>
				<?php

				foreach ( $this->options as $key => $val ) :

					?>
					<li>
						<label>
							<input
								name="<?php echo $this->name; ?>"
								value="<?php echo $key; ?>"
								type="radio"
								style="<?php echo $this->css; ?>"
								class="<?php echo $this->class; ?>"
								<?php echo implode( ' ', $this->attributes ); ?>
								<?php checked( $key, $this->value ); ?>
								/> <?php echo $val ?>
						</label>
					</li>
					<?php

				endforeach;

				?>
			</ul>
		</fieldset>
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
