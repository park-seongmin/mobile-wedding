<?php
namespace TotalTheme\Customizer\Controls;
use \WP_Customize_Control;

defined( 'ABSPATH' ) || exit;

/**
 * Custom Columns Control
 *
 * @package TotalTheme
 * @subpackage Customizer
 * @version 5.3.1
 */
class Grid_Columns extends WP_Customize_Control {

	/**
	 * The control type.
	 *
	 * @access public
	 * @var string
	 */
	public $type = 'wpex-columns';

	/**
	 * Render the content
	 *
	 * @access public
	 */
	public function render_content() {

		$field_val = $this->value();

		$medias = array(
			'd'  => array(
				'label' => esc_html__( 'Desktop', 'total' ),
			),
			'tl' => array(
				'label' => esc_html__( 'Tablet Landscape (max-width: 1024px)', 'total' ),
			),
			'tp' => array(
				'label' => esc_html__( 'Tablet Portrait (max-width: 959px)', 'total' ),
			),
			'pl' => array(
				'label' => esc_html__( 'Phone Landscape (max-width: 767px)', 'total' ),
			),
			'pp' => array(
				'label' => esc_html__( 'Phone Portrait (max-width: 479px)', 'total' ),
			),
		);

		// Setup default values
		$defaults = array();
		foreach ( $medias as $key => $val ) {
			$defaults[$key] = '';
		}

		// If field val isn't an array then it's a single desktop column setting
		if ( ! is_array( $field_val ) ) {
			$field_val = array(
				'd' => $field_val,
			);
		}

		// Parse field
		$field_val = wp_parse_args( $field_val, $defaults ); ?>

		<label><span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span></label>

		<?php if ( ! empty( $this->description ) ) : ?>

			<span class="description customize-control-description">
				<?php echo wp_strip_all_tags( $this->description ); ?>
			</span>

		<?php endif; ?>

		<ul class="wpex-customizer-columns-field">

			<?php
			// Loop through medias and display fields
			foreach ( $medias as $key => $val ) :

				$is_extra = 'd' !== $key; ?>

				<?php if ( $is_extra ) { ?>

					<li class="wpex-extra wpex-hidden">

					<label for="<?php echo esc_attr( $this->id ); ?>_<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $val['label'] ); ?></label>

				<?php } else { ?>

					<li>

					<?php } ?>

					<select class="wpex-cols-select" name="<?php echo esc_attr( $this->id ); ?>_<?php echo esc_attr( $key ); ?>" data-name="<?php echo esc_attr( $key ); ?>"><?php $this->show_options( $field_val[$key], $is_extra ); ?></select>

				</li>

			<?php endforeach; ?>

		</ul>

		<a href="#" class="wpex-toggle-settings" role="button" aria-expanded="false"><?php esc_html_e( 'Toggle responsive options', 'total' ); ?></a>

	<?php }

	/**
	 * Displays select field
	 *
	 * @access public
	 */
	public function show_options( $selected, $is_extra = false ) {

		if ( ! empty( $this->choices ) && ! $is_extra ) {
			$columns = $this->choices;
		} else {
			$columns = wpex_grid_columns();
			$columns = array_combine( $columns, $columns );
		}

		if ( $is_extra ) {
			echo '<option value ' . selected( $selected, '', false ) . '>' . esc_html__( 'Inherit' , 'total' ) . '</option>';
		}

		foreach ( $columns as $column => $label ) {

			echo '<option value="' . esc_attr( $column ) . '" ' . selected( $selected, $column, false ) . '>' . esc_html( $label ) . '</option>';

		}

	}

}