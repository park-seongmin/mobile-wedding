<?php
/**
 * Site topbar functions.
 *
 * @package TotalTheme
 * @subpackage Functions
 * @version 5.3.1
 *
 * @todo fix inconsistencies with naming convention topbar_ vs top_bar_
 */

defined( 'ABSPATH' ) || exit;

/*-------------------------------------------------------------------------------*/
/* [ Table of contents ]
/*-------------------------------------------------------------------------------*

	# General
	# Social

/*-------------------------------------------------------------------------------*/
/* [ General ]
/*-------------------------------------------------------------------------------*/

/**
 * Check if topbar is enabled.
 *
 * @since 4.0
 */
function wpex_has_topbar( $post_id = '' ) {

	// Bypass customizer check for elementor location.
	if ( wpex_elementor_location_exists( 'topbar' ) ) {
		$check = true;
	}

	// Check customizer setting.
	else {
		$check = get_theme_mod( 'top_bar', true );
	}

	// Get current post ID.
	$post_id = $post_id ? $post_id : wpex_get_current_post_id();

	// Check meta.
	if ( $post_id && $meta = get_post_meta( $post_id, 'wpex_disable_top_bar', true ) ) {

		// Return false if disabled via post meta.
		if ( 'on' === $meta ) {
			$check = false;
		}

		// Return true if enabled via post meta.
		elseif ( 'enable' === $meta ) {
			$check = true;
		}

	}

	$check = apply_filters( 'wpex_is_top_bar_enabled', $check ); // @todo deprecate.

	/**
	 * Filters whether the topbar is enabled or not.
	 *
	 * @param boolean $check
	 */
	$check = (bool) apply_filters( 'wpex_has_topbar', $check );

	return $check;

}

/**
 * Topbar style.
 *
 * @since 2.0.0
 */
function wpex_topbar_style() {
	$style = ( $style = get_theme_mod( 'top_bar_style' ) ) ? $style : 'one';
	return apply_filters( 'wpex_top_bar_style', $style );
}

/**
 * Breakpoint where the top bar is split into a left/right layout.
 *
 * @since 5.0
 */
function wpex_topbar_split_breakpoint() {
	$bk = get_theme_mod( 'topbar_split_breakpoint' );
	if ( empty( $bk ) || ! array_key_exists( $bk, wpex_utl_breakpoints() ) ) {
		$bk = 'md';
	}
	return apply_filters( 'wpex_topbar_split_breakpoint', $bk );
}

/**
 * Check if topbar is in full-width mode
 *
 * @since 5.0
 */
function wpex_topbar_is_fullwidth() {
	if ( 'full-width' === wpex_site_layout() && get_theme_mod( 'top_bar_fullwidth' ) ) {
		return true;
	}
}

/**
 * Topbar wrap class.
 *
 * @since 5.0
 */
function wpex_topbar_wrap_class() {
	echo 'class="' . esc_attr( wpex_topbar_classes() ) . '"';
}

/**
 * Topbar classes.
 *
 * @since 2.0.0
 * @todo deprecate since this technically is for the wrapper classes since 5.0 (see above)
 *		 must return a string in deprecated version as this function was used previously as a string.
 */
function wpex_topbar_classes() {

	$classes = array(
		'wpex-text-sm',
	);

	if ( apply_filters( 'wpex_has_sticky_topbar', get_theme_mod( 'top_bar_sticky' ) )
		&& ! wpex_vc_is_inline()
	) {
		$classes[] = 'wpex-top-bar-sticky';
		$classes[] = 'wpex-bg-white';
	}

	if ( get_theme_mod( 'top_bar_bottom_border', true ) ) {
		$classes[] = 'wpex-border-b';
		$classes[] = 'wpex-border-main';
		$classes[] = 'wpex-border-solid';
	}

	if ( $visibility = get_theme_mod( 'top_bar_visibility' ) ) {
		$classes[] = $visibility;
	}

	if ( wpex_topbar_is_fullwidth() ) {
		$classes[] = 'wpex-full-width';
		$classes[] = 'wpex-px-30';
	}

	if ( 'three' === wpex_topbar_style() ) {
		$classes[] = 'textcenter';
	}

	/**
	 * Filters the topbar wrap class.
	 *
	 * @param array $class
	 */
	$classes = (array) apply_filters( 'wpex_topbar_wrap_class', $classes ); // new filter in 5.0

	return implode( ' ', apply_filters( 'wpex_get_topbar_classes', $classes ) ); // @todo deprecate this filter

}

/**
 * Return topbar class.
 *
 * @since 5.0
 */
function wpex_topbar_class() {

	$topbar_style = wpex_topbar_style();
	$split_bk = wpex_topbar_split_breakpoint();
	$alignment = wpex_get_mod( 'topbar_alignment', 'center', true );

	$classes = array();

	if ( ! wpex_topbar_is_fullwidth() ) {
		$classes[] = 'container';
	}

	$classes[] = 'wpex-relative';
	$classes[] = 'wpex-py-15';

	if ( in_array( $topbar_style, array( 'one', 'two' ) ) ) {
		$classes[] = 'wpex-' . $split_bk . '-flex';
		$classes[] = 'wpex-justify-between';
		$classes[] = 'wpex-items-center';
		$classes[] = 'wpex-text-' . sanitize_html_class( $alignment );
		$classes[] = 'wpex-' . $split_bk . '-text-initial';
	}

	if ( 'one' === $topbar_style && ! wpex_has_topbar_content() ) {
		$classes[] = 'wpex-flex-row-reverse';
	}

	if ( 'two' === $topbar_style && ! wpex_has_topbar_social() ) {
		$classes[] = 'wpex-flex-row-reverse';
	}

	if ( 'three' === $topbar_style ) {
		$classes[] = 'wpex-text-' . sanitize_html_class( $alignment );
	}

	/**
	 * Filters the topbar class
	 *
	 * @param array $class
	 */
	$classes = (array) apply_filters( 'wpex_topbar_class', $classes );

	if ( $classes ) {
		echo 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';
	}

}

/**
 * Check if the topbar has content.
 *
 * @since 5.0
 */
function wpex_has_topbar_content() {
	if ( has_nav_menu( 'topbar_menu' ) || wpex_topbar_content() ) {
		return true;
	}
	return false;
}

/**
 * Get topbar aside content.
 *
 * @since 4.0
 */
function wpex_topbar_content( $deprecated = '' ) {

	// Get topbar content from Customizer.
	$content = wpex_get_translated_theme_mod( 'top_bar_content', '[topbar_item icon="phone" text="1-800-987-654" link="tel:1-800-987-654"][topbar_item icon="envelope" text="admin@totalwptheme.com" link="mailto:admin@totalwptheme.com"][topbar_item type="login" icon="user" icon_logged_in="sign-out" text="User Login" text_logged_in="Log Out" logout_text="Logout"]' );

	// Apply filters before converting to text.
	$content = apply_filters( 'wpex_top_bar_content', $content );

	// Check if content is a page ID and get page content.
	if ( is_numeric( $content ) ) {
		$post_id = wpex_parse_obj_id( $content, 'page' );
		$post    = get_post( $post_id );
		if ( $post && ! is_wp_error( $post ) ) {
			$content = wpex_parse_vc_content( $post->post_content );
		}
	}

	// Apply filters and return content.
	return $content;

}

/**
 * Topbar content class.
 *
 * @since 5.0
 */
function wpex_topbar_content_class() {
	if ( $classes = wpex_topbar_content_classes() ) {
		echo 'class="' . esc_attr( $classes ) . '"';
	}
}

/**
 * Topbar content classes.
 *
 * @since 2.0.0
 */
function wpex_topbar_content_classes() {
	$topbar_style = wpex_topbar_style();
	$split_bk     = wpex_topbar_split_breakpoint();

	$classes = array();

	if ( wpex_topbar_content() ) {
		$classes[] = 'has-content';
	}

	switch ( $topbar_style ) {
		case 'one':
			$classes[] = 'top-bar-left';
			break;
		case 'two':
			$classes[] = 'top-bar-right';
			if ( wpex_has_topbar_social() ) {
				$classes[] = 'wpex-mt-10';
				$classes[] = 'wpex-' . $split_bk . '-mt-0';
			}
			break;
		case 'three':
			$classes[] = 'top-bar-centered';
			break;
	}

	$classes[] = 'wpex-clr';

	/**
	 * Filters the topbar content element class.
	 *
	 * @param array $class
	 */
	$classes = (array) apply_filters( 'wpex_top_bar_classes', $classes );

	return implode( ' ', $classes );

}

/*-------------------------------------------------------------------------------*/
/* [ Social ]
/*-------------------------------------------------------------------------------*/

/**
 * Check if the topbar social is enabled.
 *
 * @since 5.0
 */
function wpex_has_topbar_social() {

	if ( wpex_topbar_social_alt_content() ) {
		return true;
	}

	if ( false === wp_validate_boolean( get_theme_mod( 'top_bar_social', true ) ) ) {
		return false;
	}

	if ( empty( wpex_get_topbar_social_profiles() ) ) {
		return false;
	}

	if ( empty( wpex_topbar_social_options() ) ) {
		return false;
	}

	return true; // enabled by default.

}

/**
 * Top bar social class.
 *
 * @since 5.0
 */
function wpex_topbar_social_class() {
	$topbar_style = wpex_topbar_style();
	$split_bk     = wpex_topbar_split_breakpoint();

	$classes = array();

	switch ( $topbar_style ) {
		case 'one':
			$classes[] = 'top-bar-right';
			if ( wpex_topbar_content() ) {
				$classes[] = 'wpex-mt-10';
				$classes[] = 'wpex-' . $split_bk . '-mt-0';
			}
			break;
		case 'two':
			$classes[] = 'top-bar-left';
			break;
		case 'three':
			$classes[] = 'top-bar-centered';
			if ( has_nav_menu( 'topbar_menu' ) || wpex_topbar_content() ) {
				$classes[] = 'wpex-mt-10'; // extra spacing for centered top bar when there is content
			}
			break;
	}

	if ( empty( wpex_topbar_social_alt_content() ) ) {
		$social_style = ( $social_style = get_theme_mod( 'top_bar_social_style' ) ) ? $social_style : 'none';
		$classes[] = 'social-style-' . $social_style;
	}

	/**
	 * Filters the topbar social element class.
	 *
	 * @param array $class
	 */
	$classes = (array) apply_filters( 'wpex_topbar_social_class', $classes );

	if ( $classes ) {
		echo 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';
	}

}

/**
 * Return topbar social alt content.
 *
 * @since 4.0
 */
function wpex_topbar_social_alt_content( $deprecated = '' ) {

	$content = trim( wpex_get_translated_theme_mod( 'top_bar_social_alt' ) );

	if ( is_numeric( $content ) ) {
		$post_id = wpex_parse_obj_id( $content, 'page' );
		$post    = get_post( $post_id );
		if ( $post && ! is_wp_error( $post ) ) {
			$content = trim( $post->post_content );
		}
	}

	/**
	 * Filters the topbar social alt content.
	 *
	 * @param string $content.
	 */
	$content = apply_filters( 'wpex_topbar_social_alt_content', $content );

	return $content;

}

/**
 * Return active social style.
 *
 * @since 5.0
 */
function wpex_topbar_social_style() {
	$style = ( $style = get_theme_mod( 'top_bar_social_style' ) ) ? $style : 'none';

	if ( 'colored-icons' == $style  || 'images' == $style ) {
		$style = 'flat-color-rounded'; // deprecate the old image style icons since version 4.9
	}

	/**
	 * Filters the topbar social style.
	 *
	 * @param string $style.
	 */
	$style = (string) apply_filters( 'wpex_topbar_social_style', $style );

	return $style;
}

/**
 * Get topbar social profiles.
 *
 * @since 5.0
 */
function wpex_get_topbar_social_profiles() {
	$profiles = get_theme_mod( 'top_bar_social_profiles' );
	if ( $profiles && is_array( $profiles ) ) {
		$profiles = array_filter( $profiles );
	}
	return $profiles;
}

/**
 * Output topbar social list.
 *
 * @since 5.0
 */
function wpex_topbar_social_list() {
	$profiles = wpex_get_topbar_social_profiles();
	$social_options = wpex_topbar_social_options();

	if ( empty( $profiles ) || empty( $social_options ) ) {
		return;
	}

	$social_style = wpex_topbar_social_style();
	$link_target = get_theme_mod( 'top_bar_social_target', 'blank' );

	// Define gap between links.
	$gap = get_theme_mod( 'top_bar_social_gap' );

	if ( ! $gap ) {
		if ( 'none' === $social_style || 'default' === $social_style || empty( $social_style ) ) {
			$gap = 10;
		} else {
			$gap = 5;
		}
	}

	// Begin output.
	$output = '<ul id="top-bar-social-list" class="wpex-inline-block wpex-list-none wpex-align-bottom wpex-m-0 wpex-last-mr-0">';

	// Loop through social options.
	$links = '';
	foreach ( $social_options as $key => $val ) {

		// Get URL from the theme mods.
		$url = isset( $profiles[$key] ) ? $profiles[$key] : '';

		// URL is required.
		if ( ! $url ) {
			continue;
		}

		// Sanitize key.
		$key = esc_html( $key );

		// Sanitize email and remove link target.
		if ( 'email' === $key ) {
			$sanitize_email = sanitize_email( $url );
			if ( is_email( $url ) ) {
				$link_target = '';
				$sanitize_email = antispambot( $sanitize_email );
				$url = 'mailto:' . $sanitize_email;
			} elseif( strpos( $url, 'mailto' ) !== false ) {
				$link_target = '';
			}
		}

		// Sanitize phone number.
		if ( 'phone' === $key
			&& false === strpos( $url, 'tel:' )
			&& false === strpos( $url, 'callto:' )
		) {
			$url = 'tel:' . $url;
		}

		// Image style (deprecated in 4.9).
		if ( 'images' === $social_style ) {

			$img_url = wpex_asset_url( '/images/social' );
			$img_url = apply_filters_deprecated( 'top_bar_social_img_url', array( $img_url ), '4.9', 'wpex_topbar_social_images_url' );
			$img_url = apply_filters( 'wpex_topbar_social_images_url', wpex_asset_url( '/images/social' ) );

			$content = '<img src="' . esc_url( $img_url . '/' . $key . '.png' ) . '" alt="' . esc_attr( $val['label'] ) . '" class="wpex-align-middle">';

		// Newer icon font social.
		} else {
			$content = '<span class="' . esc_attr( $val['icon_class'] ) . '" aria-hidden="true"></span><span class="screen-reader-text">' . esc_attr( $val['label'] ) . '</span>';
		}

		// Li classes.
		$li_class = 'wpex-inline-block';

		if ( $gap_escaped = absint( $gap ) ) {
			$li_class .= ' wpex-mr-' . absint( $gap );
		}

		// Generate link HTML based on attributes and content.
		$links .= '<li class="' . esc_attr( $li_class ) . '">';

			$link_attrs = apply_filters( 'wpex_topbar_social_link_attrs', array(
				'href'   => $url,
				'title'  => $val['label'],
				'target' => $link_target,
				'class'  => 'wpex-' . $key . ' ' . wpex_get_social_button_class( $social_style ),
			), $key );

		 	$links .= wpex_parse_html( 'a', $link_attrs, $content );

		 $links .= '</li>';

	} // endforeach.

	$output .= apply_filters( 'wpex_topbar_social_links_output', $links );

	$output .= '</ul>';

	$output_escaped = $output;

	echo $output_escaped; // @codingStandardsIgnoreLine

}