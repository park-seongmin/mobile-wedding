<?php
/**
 * Image Overlay: Slide Up Title White Overlay.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 5.1.1
 */

defined( 'ABSPATH' ) || exit;

// Only used for inside position.
if ( 'inside_link' !== $position ) {
	return;
}

if ( 'staff' === get_post_type() ) {
	$content = get_post_meta( get_the_ID(), 'wpex_staff_position', true );
} else {
	$content = isset( $args['post_title'] ) ? $args['post_title'] : get_the_title();
}

?>

<div class="overlay-slideup-title overlay-hide theme-overlay wpex-absolute wpex-inset-0 wpex-transition-all wpex-duration-<?php echo wpex_overlay_speed( 'slideup-title-white' ); ?> wpex-overflow-hidden wpex-flex wpex-items-center wpex-justify-center">

	<div class="overlay-bg wpex-bg-<?php echo wpex_overlay_bg( 'slideup-title-white', 'white' ); ?> wpex-absolute wpex-inset-0 wpex-opacity-<?php echo wpex_overlay_opacity( 'slideup-title-white', '80' ); ?>"></div>

	<div class="overlay-content overlay-transform wpex-relative wpex-text-md wpex-text-black wpex-text-center wpex-font-semibold wpex-transition-all wpex-duration-300 wpex-px-20 wpex-translate-y-100"><?php echo apply_filters( 'wpex_overlay_content_slideup-title-white',  '<span class="title">' . wp_kses_post( $content ) . '</span>' ); ?></div>

</div>