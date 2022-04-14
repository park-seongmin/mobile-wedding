<?php
/**
 * Breadcrumbs output.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * Return if breadcrumbs are disabled.
 *
 * Check MUST be added here in case we are adding breadcrumbs via child theme displaying it somewhere random.
*/
if ( ! wpex_has_breadcrumbs() ) {
	return;
}

echo wpex_get_breadcrumbs_output();