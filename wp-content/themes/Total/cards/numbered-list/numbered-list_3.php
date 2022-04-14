<?php
defined( 'ABSPATH' ) || exit;

$output = '';

// Number
$output .= $this->get_number( array(
	'class'        => 'wpex-text-gray-900 wpex-text-6xl wpex-font-light',
	'prepend_zero' => true,
) );

// Title
$output .= $this->get_title( array(
	'class' => 'wpex-heading wpex-text-lg',
) );

// Excerpt
$output .= $this->get_excerpt( array(
	'class' => 'wpex-mt-5 wpex-text-gray-600',
) );

// More Link
$output .= $this->get_more_link( array(
	'class'  => 'wpex-mt-15 wpex-font-semibold',
	'text'   => esc_html__( 'Learn more', 'total' ),
	'suffix' => ' &rarr;',
) );

return $output;