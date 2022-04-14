<?php
defined( 'ABSPATH' ) || exit;

$output = '';

// Thumbnail
$output .= $this->get_thumbnail( array(
	'class' => 'wpex-mb-10',
) );

// Title
$output .= $this->get_title( array(
	'class' => 'wpex-heading wpex-text-md',
) );

// Position
$output .= $this->get_element( array(
	'content' => wpex_get_staff_member_position(),
	'class'   => 'wpex-card-staff-member-position wpex-text-gray-500',
) );

return $output;