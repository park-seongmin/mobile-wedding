<?php
/**
 * Disable canonical redirect on the homepage when using pagination via VC modules
 * or when using the blog template on the homepage
 *
 * @package TotalTheme
 * @version 5.0
 */

defined( 'ABSPATH' ) || exit;

function wpex_singular_pagination_fix( $redirect_url ) {
	if ( is_paged() && is_singular() ) {
		$redirect_url = false;
	}
	return $redirect_url;
}
add_filter( 'redirect_canonical', 'wpex_singular_pagination_fix' );