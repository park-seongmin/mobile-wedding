<?php
/**
 * Footer Bottom Helper Functions
 *
 * @package TotalTheme
 * @subpackage Functions
 * @version 5.1.3
 */

defined( 'ABSPATH' ) || exit;

/**
 * Check if footer bottom is enabled
 *
 * @since 4.1
 */
function wpex_has_footer_bottom( $post_id = '' ) {
	if ( ! $post_id ) {
		$post_id = wpex_get_current_post_id();
	}

	if ( wpex_elementor_location_exists( 'footer_bottom' ) ) {
		$check = true;
	} elseif ( wpex_has_custom_footer() || ! empty( $_GET[ 'wpex_inline_footer_template_editor' ] ) ) {
		$check = get_theme_mod( 'footer_builder_footer_bottom', false ); //@todo rename to be same as default.
	} else {
		$check = get_theme_mod( 'footer_bottom', true );
	}

	if ( $post_id ) {
		$meta = get_post_meta( $post_id, 'wpex_footer_bottom', true );
		if ( 'on' === $meta ) {
			$check = true;
		} elseif ( 'off' === $meta ) {
			$check = false;
		}
	}

	/**
	 * Filters whether the footer bottom should display or
	 *
	 * @param bool $check
	 * @param int $post_id
	 */
	$check = (bool) apply_filters( 'wpex_has_footer_bottom', $check, $post_id );

	return $check;
}

/**
 * Footer bottom class
 *
 * @since 4.9.9.5
 */
function wpex_footer_bottom_class() {
	$class = array(
		'wpex-bg-gray-900',
		'wpex-py-20',
		'wpex-text-gray-500',
		'wpex-text-sm',
	);

	$align = get_theme_mod( 'bottom_footer_text_align' ); // @todo rename to footer_bottom_text_align

	if ( $align && in_array( $align, array( 'left', 'center', 'right' ) ) ) {
		$class[] = 'wpex-text-' . sanitize_html_class( $align );
	} else {
		$class[] = 'wpex-text-center wpex-md-text-left';
	}

	/**
	 * Filters the footer bottom element class.
	 *
	 * @param array $class
	 */
	$class = (array) apply_filters( 'wpex_footer_bottom_classes', $class );

	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/**
 * Footer Menu Class.
 *
 * @since 5.0
 */
function wpex_footer_bottom_menu_class() {
	$classes = array(
		'wpex-mt-10',
	);

	$align = get_theme_mod( 'bottom_footer_text_align' );

	if ( ! $align || ! in_array( $align, array( 'left', 'center', 'right' ) ) ) {
		$classes[] = 'wpex-md-mt-0';
	}

	/**
	 * Filters the footer bottom menu element class.
	 *
	 * @param array $class
	 */
	$classes = (array) apply_filters( 'wpex_footer_bottom_menu_class', $classes );

	if ( $classes ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $classes ) ) ) . '"';
	}
}