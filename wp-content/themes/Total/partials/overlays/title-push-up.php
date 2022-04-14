<?php
defined( 'ABSPATH' ) || exit;

if ( 'inside_link' !== $position ) {
	return;
}

$title = isset( $args['post_title'] ) ? $args['post_title'] : get_the_title();

if ( ! $title ) {
	return;
}

?>

<div class="overlay-title-push-up theme-overlay wpex-bg-<?php echo wpex_overlay_bg( 'title-push-up' ); ?> wpex-text-white wpex-text-center wpex-absolute wpex-bottom-0 wpex-translate-y-100 wpex-inset-x-0 wpex-py-15 wpex-px-20 wpex-w-100 wpex-text-md wpex-transition-all wpex-duration-<?php echo wpex_overlay_speed( 'title-push-up' ); ?>"><?php
	echo apply_filters( 'wpex_overlay_content_title-push-up', '<span class="title">' . esc_html( $title ) . '</span>' );
?></div>