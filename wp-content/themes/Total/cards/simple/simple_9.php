<?php
defined( 'ABSPATH' ) || exit;

$output = '';

// Calculate svg size based on title font size.
$svg_font_size = 'wpex-text-lg';

if ( ! empty( $this->args['title_font_size'] ) ) {
	$svg_font_size = wpex_sanitize_utl_font_size( $this->args['title_font_size'] );
}

// Card output.
$output .= '<div class="wpex-card-inner wpex-flex wpex-gap-15">';

	// Icon.
	$output .= '<div class="wpex-card-svg ' . esc_attr( $svg_font_size ) . ' wpex-text-accent"><svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 24 24" width="1em" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8-8-8z"/></svg></div>';

	// Details.
	$output .= '<div class="wpex-card-details">';

		// Title
		$output .= '<div class="wpex-text-accent">' . $this->get_title( array(
			'class' => 'wpex-heading wpex-inherit-color-important wpex-text-lg wpex-mb-10',
		) ) . '</div>';

		// Excerpt
		$output .= $this->get_excerpt();

	$output .= '</div>';

$output .= '</div>';

return $output;