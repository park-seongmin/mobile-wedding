<?php
namespace TotalTheme\Customizer\Controls;
use \WP_Customize_Control;

defined( 'ABSPATH' ) || exit;

/**
 * Customizer Sortable Control.
 *
 * @package TotalTheme
 * @subpackage Customizer
 * @version 5.3.1
 */
class Sortable extends WP_Customize_Control {

	/**
	 * The control type.
	 *
	 * @access public
	 * @var string
	 */
	public $type = 'wpex-sortable';

	/**
	 * Enque scripts.
	 */
	public function enqueue() {
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-sortable' );
	}

	/**
	 * The control template.
	 */
	public function render_content() { ?>

		<div class="wpex-sortable">

			<label>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php if ( '' != $this->description ) { ?>
					<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
				<?php } ?>
			</label>

			<?php
			// Get values and choices
			$choices = $this->choices;
			$values  = $this->value();
			// Turn values into array
			if ( ! is_array( $values ) ) {
				$values = explode( ',', $values );
			} ?>
			<ul id="<?php echo esc_attr( $this->id ); ?>_sortable">
				<?php
				// Loop through values
				foreach ( $values as $val ) :
					// Get label
					$label = isset( $choices[$val] ) ? $choices[$val] : '';
					if ( $label ) : ?>
						<li data-value="<?php echo esc_attr( $val ); ?>" class="wpex-sortable-li">
							<?php echo esc_html( $label ); ?>
							<span class="wpex-hide-sortee ticon ticon-toggle-on"></span>
						</li>
					<?php
					// End if label check
					endif;
					// Remove item from choices array - so only disabled items are left
					unset( $choices[$val] );
				// End val loop
				endforeach;
				// Loop through disabled items (disabled items have been removed alredy from choices)
				foreach ( $choices as $val => $label ) { ?>
					<li data-value="<?php echo esc_attr( $val ); ?>" class="wpex-sortable-li wpex-hide">
						<?php echo esc_html( $label ); ?>
						<span class="wpex-hide-sortee ticon ticon-toggle-on ticon-rotate-180"></span>
					</li>
				<?php } ?>
			</ul>
		</div><!-- .wpex-sortable -->

		<div class="clear:both"></div>

		<?php
		// Return values as comma seperated string for input
		if ( is_array( $values ) ) {
			$values = array_keys( $values );
			$values = implode( ',', $values );
		}

			echo '<input id="' . esc_attr( $this->id ) . '_input" type="hidden" name="' . esc_attr( $this->id ) . '" value="' . esc_attr( $values ) . '" ' . $this->get_link() . '>';

		?>

		<script>
		jQuery(document).ready( function($) {
			"use strict";

			// Define variables
			var sortableUl = $( '#<?php echo esc_attr( $this->id ); ?>_sortable' );

			// Create sortable
			sortableUl.sortable();
			sortableUl.disableSelection();

			// Update values on sortstop
			sortableUl.on( "sortstop", function( event, ui ) {
				wpexUpdateSortableVal();
			} );

			// Toggle classes
			sortableUl.find( 'li' ).each( function() {
				$( this ).find( '.wpex-hide-sortee' ).click( function() {
					$( this ).toggleClass( 'ticon-rotate-180' ).parents( 'li:eq(0)' ).toggleClass( 'wpex-hide' );
				} );
			});

			// Update Sortable when hidding/showing items
			$( '#<?php echo esc_attr( $this->id ); ?>_sortable span.wpex-hide-sortee' ).click( function() {
				wpexUpdateSortableVal();
			} );

			// Used to update the sortable input value
			function wpexUpdateSortableVal() {
				var values = [];
				sortableUl.find( 'li' ).each( function() {
					if ( ! $( this ).hasClass( 'wpex-hide' ) ) {
						values.push( $( this ).attr( 'data-value' ) );
					}
				} );
				$( '#<?php echo esc_attr( $this->id ); ?>_input' ).val( values ).trigger( 'change' );
			}

		} );
		</script>

		<?php
	}
}