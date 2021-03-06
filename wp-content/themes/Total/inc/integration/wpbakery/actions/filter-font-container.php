<?php
/**
 * Alter the WPBakery font container setting
 *
 * @package TotalTheme
 * @subpackage WPBakery
 * @version 5.0
 *
 * @todo conver to a Class so it can be loaded via autoloading.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Adds a span setting to the allowed tags
 *
 * @since 4.0
 */
function wpex_vc_font_container_tags( $tags ) {
	$tags['span'] = 'span';
	return $tags;
}
add_filter( 'vc_font_container_get_allowed_tags', 'wpex_vc_font_container_tags' );

/**
 * Adds Total Google fonts to the font_container param
 *
 * @since 4.0
 * @todo Remove?
 */
function wpex_vc_font_container_fonts( $fonts ) {

	// Add blank option
	$new_fonts[''] = esc_html__( 'Default', 'total' );

	// Merge arrays
	$fonts = array_merge( $new_fonts, $fonts );

	// Get Google fonts
	$google_fonts = wpex_google_fonts_array();
	$google_fonts = array_combine( $google_fonts, $google_fonts );

	// Merge fonts
	$fonts = array_merge( $fonts, $google_fonts );

	// Return fonts
	return $fonts;

}
add_filter( 'vc_font_container_get_fonts_filter', 'wpex_vc_font_container_fonts' );