<?php
/**
 * Hover Button Overlay.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 5.3
 */

defined( 'ABSPATH' ) || exit;

$overlay_position = apply_filters( 'wpex_hover_button_overlay_position', 'outside_link' );

// Only used for outside_link position.
if ( $overlay_position !== $position ) {
	return;
}

// Define vars.
$link = $text = '';

// Button args.
$button_args = array();

// Button class.
$button_class = 'overlay-hover-button-link wpex-text-md theme-button minimal-border white';

// Outside position settings.
if ( 'outside_link' == $overlay_position ) {

	// Lightbox.
	$lightbox_link  = ! empty( $args['lightbox_link'] ) ? $args['lightbox_link'] : '';
	$lightbox_data  = ! empty( $args['lightbox_data'] ) ? $args['lightbox_data'] : '';
	$lightbox_data  = ( is_array( $lightbox_data ) ) ? ' ' . implode( ' ', $lightbox_data ) : $lightbox_data;
	$lightbox_class = ! empty( $args['lightbox_class'] ) ? $args['lightbox_class'] : 'wpex-lightbox';

	if ( 'wpex-lightbox-group-item' === $lightbox_class ) {
		$lightbox_class = 'wpex-lightbox';
	}

	// Link.
	if ( ! $lightbox_link ) {
		$link = isset( $args['post_permalink'] ) ? $args['post_permalink'] : wpex_get_permalink();
	} else {
		$link = $lightbox_link;
	}

	// Custom link.
	$link = ! empty( $args['overlay_link'] ) ? $args['overlay_link'] : $link;
	$link = apply_filters( 'wpex_hover_button_overlay_link', $link );

	// Link target.
	$target = ! empty( $args['link_target'] ) ? $args['link_target'] : '';
	$target = apply_filters( 'wpex_button_overlay_target', $target ); // @todo rename to wpex_hover_button_overlay_target.

	// Update button classes.
	if ( $lightbox_link ) {
		$button_class .= ' ' . $lightbox_class;
	}

	// Update button args.
	$button_args['href'] = $link;
	$button_args['target'] = $target;
	$button_args['data'] = $lightbox_data;

}

// Text.
$text = ! empty( $args['overlay_button_text'] ) ? $args['overlay_button_text'] : esc_html__( 'View Post', 'total' );
$text = ( 'post_title' === $text ) ? get_the_title() : $text;
$text = apply_filters( 'wpex_hover_button_overlay_text', $text );

// Button args.
$button_tag = ( 'outside_link' == $overlay_position ) ? 'a' : 'span';
$button_args['class'] = $button_class;

// Get animation speed.
$speed = wpex_overlay_speed( 'hover-button' );

?>

<div class="overlay-hover-button overlay-hide theme-overlay wpex-absolute wpex-inset-0 wpex-transition-all wpex-duration-<?php echo intval( $speed ); ?> wpex-flex wpex-items-center wpex-justify-center">
	<span class="overlay-bg wpex-bg-<?php echo wpex_overlay_bg( 'hover-button' ); ?> wpex-block wpex-absolute wpex-inset-0 wpex-opacity-<?php echo wpex_overlay_opacity( 'hover-button' ); ?>"></span>
	<div class="overlay-content overlay-scale wpex-relative wpex-font-semibold wpex-transition-transform wpex-duration-<?php echo intval( $speed ); ?> wpex-p-20 wpex-clr"><?php echo wpex_parse_html( $button_tag, $button_args, do_shortcode( wp_kses_post( $text ) ) ); ?></div>
</div>