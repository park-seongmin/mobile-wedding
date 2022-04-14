<?php
/**
 * Remove Actions.
 *
 * @package TotalTheme
 * @subpackage Hooks
 * @version 5.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * Helper function to remove all actions.
 */
function wpex_remove_actions() {
	$hooks = wpex_theme_hooks();
	foreach ( $hooks as $section => $array ) {
		if ( ! empty( $array['hooks'] ) && is_array( $array['hooks'] ) ) {
			foreach ( $array['hooks'] as $hook ) {
				remove_all_actions( $hook, false );
			}
		}
	}
}

/**
 * Remove default theme actions.
 */
function wpex_maybe_modify_theme_actions() {

	if ( is_page_template( 'templates/landing-page.php' ) ) {
		wpex_remove_actions(); return;
	}

}
add_action( 'template_redirect', 'wpex_maybe_modify_theme_actions' );