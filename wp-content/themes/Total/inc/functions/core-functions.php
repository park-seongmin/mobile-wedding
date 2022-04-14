<?php
/**
 * Global core theme functions.
 *
 * @package TotalTheme
 * @subpackage Functions
 * @version 5.3.1
 */

defined( 'ABSPATH' ) || exit;

/*-------------------------------------------------------------------------------*/
/* [ Table of contents ]
/*-------------------------------------------------------------------------------*

	# General
	# Current Post ID
	# Taxonomy & Terms
	# Sliders
	# Images
	# Icons
	# Lightbox
	# WooCommerce
	# Elementor
	# WPBakery
	# Tribe Events
	# PHP Helpers
	# Fallbacks
	# Other

/*-------------------------------------------------------------------------------*/
/* [ General ]
/*-------------------------------------------------------------------------------*/

/**
 * Get Theme Branding.
 *
 * @since 3.3.0
 */
function wpex_get_theme_branding() {
	$branding = WPEX_THEME_BRANDING;
	if ( $branding && 'disabled' !== $branding ) {
		return wp_strip_all_tags( $branding );
	}
}

/**
 * Check current request.
 *
 * @since 5.0
 */
function wpex_is_request( $type ) {
	switch ( $type ) {
		case 'admin':
			return is_admin();
		case 'ajax':
			return wp_doing_ajax();
		case 'frontend':
			return ( ! is_admin() || wp_doing_ajax() );
	}
}

/**
 * Return assets url for loading scripts.
 *
 * @since 3.6.0
 */
if ( ! function_exists( 'wpex_asset_url' ) ) {
	function wpex_asset_url( $file = '' ) {
		return WPEX_THEME_URI . '/assets/' . ltrim( $file, '/' );
	}
}

/**
 * Get theme license.
 *
 * Please purchase a legal copy of the theme and don't just hack this
 * function. First of all if you hack it, you won't get updates because
 * there is added validation on our updates API so it won't work.
 * And second, a lot of time and resources has gone into the development
 * of this awesome theme, purchasing a valid license is the right thing to do.
 *
 * @since 4.5
 */
function wpex_get_theme_license() {
	$license = '';
	if ( is_multisite() && ! is_main_site() ) {
		switch_to_blog( get_network()->site_id );
		$license = get_option( 'active_theme_license' );
		restore_current_blog();
	}
	$license = $license ?: get_option( 'active_theme_license' );
	return wp_strip_all_tags( trim( $license ) );
}

/**
 * Verify active license.
 *
 * @since 4.5.4
 */
function wpex_verify_active_license( $license = '' ) {
	$license = $license ?: wpex_get_theme_license();
	if ( ! $license ) {
		return;
	}
	$args = array(
		'market'  => 'envato',
		'license' => $license,
		'verify'  => 1,
	);
	if ( get_option( 'active_theme_license_dev', false ) ) {
		$args['dev'] = '1';
	}
	$remote_url = add_query_arg( $args, 'https://wpexplorer-themes.com/deactivate-license/' );
	$remote_response = wp_remote_get( $remote_url, array( 'timeout' => 5 ) );
	if ( ! is_wp_error( $remote_response ) ) {
		$result = json_decode( wp_remote_retrieve_body( $remote_response ) );
		// Will only delete license if return is exactly "inactive"
		// this way if there are any server issues it won't de-activate the license
		if ( 'inactive' === $result ) {
			delete_option( 'active_theme_license' );
			delete_option( 'active_theme_license_dev' );
			return false;
		}
	}
	return true;
}

/**
 * Get Accent Color.
 *
 * @since 5.1
 */
function wpex_get_accent_color() {
	$color = wpex_get_custom_accent_color();
	if ( empty( $color ) ) {
		$color = '#1a73e8';
	}
	return $color;
}

/**
 * Get Custom Accent Color.
 *
 * @since 4.4.1
 */
function wpex_get_custom_accent_color() {
	$custom_accent = get_theme_mod( 'accent_color' );
	if ( $custom_accent === '#3b86b0'
		|| $custom_accent === '#4a97c2'
		|| $custom_accent === '#2c87f0'
		|| $custom_accent === '#1a73e8' // 5.1.2
	) {
		return; // Default accent (includes all older accent colors to prevent issues with updates).
	}
	if ( $custom_accent ) {
		return $custom_accent;
	}
}

/**
 * Get Accent Hover Color.
 *
 * @since 4.5
 */
function wpex_get_custom_accent_color_hover() {
	return get_theme_mod( 'accent_color_hover' ) ?: wpex_get_custom_accent_color();
}

/**
 * Get Color Palette.
 *
 * @since 5.1
 */
function wpex_get_color_palette() {

	if ( class_exists( 'WPEX_Color_Palette' ) && is_callable( 'WPEX_Color_Palette', 'get_colors_list' ) ) {
		return WPEX_Color_Palette::get_colors_list();
	}

	$default_colors = array(
		'accent' => array(
			'name'  => esc_html( 'Accent', 'total-theme-core' ),
			'color' => wpex_get_accent_color(),
		),
	);

	return (array) apply_filters( 'wpex_color_palette', $default_colors );

}

/**
 * Helper function for resizing images using the WPEX_Image_Resize class.
 *
 * @since 4.0
 */
function wpex_image_resize( $args ) {
	$new = TotalTheme\Resize_Image::getInstance();
	return $new->process( $args );
}

/**
 * Returns current URL.
 *
 * @since 4.0
 */
function wpex_get_current_url() {
	global $wp;
	if ( $wp ) {
		return home_url( add_query_arg( array(), $wp->request ) );
	}
}

/**
 * Returns theme custom post types.
 *
 * @since 1.3.3
 */
function wpex_theme_post_types() {
	$post_types = array( 'portfolio', 'staff', 'testimonials' );
	$post_types = array_combine( $post_types, $post_types );
	return apply_filters( 'wpex_theme_post_types', $post_types );
}

/**
 * Returns body font size.
 * Used to convert EM values to PX values such as for responsive headings.
 *
 * @since 3.3.0
 */
function wpex_get_body_font_size() {
	$body_typo = get_theme_mod( 'body_typography' );
	$font_size = ! empty( $body_typo['font-size'] ) ? $body_typo['font-size'] : 13;
	if ( is_array( $font_size ) ) {
		$font_size = ! empty( $font_size['d'] ) ?  $font_size['d'] : $font_size[0];
	}
	return apply_filters( 'wpex_get_body_font_size', $font_size );
}

/**
 * Echo the post URL.
 *
 * @since 1.5.4
 */
function wpex_permalink( $post = '' ) {
	echo esc_url( wpex_get_permalink( $post ) );
}

/**
 * Return the post URL.
 *
 * @since 2.0.0
 */
function wpex_get_permalink( $post = '' ) {

	// Get post.
	$post = get_post( $post );

	// Check wpex_post_link custom field for custom link.
	$permalink = wpex_get_post_redirect_link( $post );

	// If there isn't any custom redirect, return the post link.
	if ( ! $permalink ) {
		$permalink = get_permalink( $post );
	}

	/**
	 * Filters the post permalink.
	 *
	 * @param string $permalink The post permalink.
	 */
	$permalink = apply_filters( 'wpex_permalink', $permalink, $post );

	return $permalink;

}

/**
 * Get custom post link.
 *
 * @since 4.1.2
 */
function wpex_get_post_redirect_link( $post = '' ) {
	$post = get_post( $post );
	if ( is_object( $post ) && isset( $post->ID ) ) {
		return get_post_meta( $post->ID, 'wpex_post_link', true );
	}
}

/**
 * Return custom permalink.
 *
 * @since 2.0.0
 * @todo rename to wpex_get_post_redirection() for better consistency.
 */
function wpex_get_custom_permalink() {
	$custom_link = get_post_meta( get_the_ID(), 'wpex_post_link', true );
	if ( $custom_link ) {
		$custom_link = ( 'home_url' === $custom_link ) ? home_url( '/' ) : $custom_link;
		return esc_url( $custom_link );
	}
}

/**
 * Outputs a theme heading.
 *
 * @since 1.3.3
 */
function wpex_heading( $args = array() ) {
	$echo = isset( $args['echo'] ) ? $args['echo'] : true; // fallback.
	if ( $echo ) {
		echo wpex_get_heading( $args );
	} else {
		return wpex_get_heading( $args );
	}
}

/**
 * Outputs a theme heading.
 *
 * @since 4.9
 */
function wpex_get_heading( $args = array() ) {
	$output = '';

	$defaults = array(
		'apply_filters' => '',
		'content'       => '',
		'align'         => get_theme_mod( 'theme_heading_align', null ),
		'tag'           => tag_escape( get_theme_mod( 'theme_heading_tag' ) ) ?: 'div',
		'style'         => esc_html( get_theme_mod( 'theme_heading_style' ) ?: 'border-bottom' ),
		'classes'       => array(),
	);

	// Add custom filters.
	// @todo rename to 'wpex_heading_{}_args';
	if ( ! empty( $args['apply_filters'] ) ) {

		/**
		 * Filters the args for an instance of the wpex_heading.
		 *
		 * @param array $args
		 */
		$args = (array) apply_filters( 'wpex_heading_' . $args[ 'apply_filters' ], $args );
	}

	/**
	 * Filters the theme heading default args.
	 *
	 * @param array $args
	 */
	$args = (array) apply_filters( 'wpex_get_heading_args', $args );

	// Parse args.
	$args = wp_parse_args( $args, $defaults );

	// Style can't be empty if so lets set it to the default.
	if ( empty( $args['style'] ) ) {
		$args['style'] = $defaults['style'];
	}

	// Extract args.
	extract( $args );

	// Return if text is empty.
	if ( ! $content ) {
		return;
	}

	// Add custom classes.
	$add_classes = $classes;
	$classes = array(
		'theme-heading',
	);

	if ( $style ) {
		$classes[] = $style;
	}

	if ( $align && 'border-side' !== $style && ( 'left' === $align || 'center' === $align || 'right' === $align ) ) {
		$classes[] = 'wpex-text-' . sanitize_html_class( $align );
	}

	if ( $add_classes && is_array( $add_classes ) ) {
		$classes = array_merge( $classes, $add_classes );
	}

	// Sanitize tag.
	$tag_escaped = tag_escape( $tag );

	// Open heading tag.
	$output .= '<' . $tag_escaped . ' class="' . esc_attr( implode( ' ', $classes ) ) . '">';

		// Heading inner text.
		$output .= '<span class="text">' . do_shortcode( wp_kses_post( $content ) ) . '</span>';

	// Close heading tag.
	$output .= '</' . $tag_escaped . '>';

	return $output;
}

/**
 * Returns separator used for inline lists.
 *
 * @since 5.0
 */
function wpex_inline_list_sep( $context = '', $before = '', $after = '' ) {
	return apply_filters( 'wpex_inline_list_sep', $before . ', ' . $after, $context );
}

/**
 * Returns hover animation class.
 *
 * @since 2.0.0
 */
function wpex_hover_animation_class( $animation ) {
	return 'hvr hvr-' . sanitize_html_class( $animation );
}

/**
 * Returns visibility class.
 *
 * @since 2.0.0
 */
function wpex_visibility_class( $visibility ) {
	$class = sanitize_html_class( $visibility );
	if ( 'hidden-toggle-element' === $visibility ) {
		$class = 'wpex-toggle-element';
	} elseif ( 'visible-toggle-element' === $visibility ) {
		$class = 'wpex-toggle-element wpex-toggle-element--visible';
	}
	return $class;
}

/**
 * Returns typography style class.
 *
 * @since  2.0.2
 * @return string
 */
function wpex_typography_style_class( $style ) {
	$class = '';
	if ( $style && 'none' !== $style && array_key_exists( $style, wpex_typography_styles() ) ) {
		$class = 'typography-' . sanitize_html_class( $style );
	}
	return $class;
}

/**
 * Converts a dashicon into it's CSS.
 *
 * @since 1.0.0
 */
function wpex_dashicon_css_content( $dashicon = '' ) {
	$css_content = 'f111';
	if ( $dashicon ) {
		$dashicons = wpex_get_dashicons_array();
		if ( isset( $dashicons[$dashicon] ) ) {
			$css_content = $dashicons[$dashicon];
		}
	}
	return $css_content;
}

/**
 * Returns Google Fonts URL if you want to change it to another CDN.
 *
 * @since 3.3.2
 */
function wpex_get_google_fonts_url() {
	return apply_filters( 'wpex_get_google_fonts_url', '//fonts.googleapis.com' );
}

/**
 * Returns array of widget areays.
 *
 * @since 4.9
 */
function wpex_get_breadcrumbs_output() {
	if ( $custom_breadcrumbs = apply_filters( 'wpex_custom_breadcrumbs', null ) ) {
		return wp_kses_post( $custom_breadcrumbs );
	}

	if ( class_exists( 'WPEX_Breadcrumbs' ) ) {
		$breadcrumbs = new WPEX_Breadcrumbs();
		return $breadcrumbs->output;
	}
}

/**
 * Return Image URL from an input (can be a URL or an ID).
 *
 * @since 4.9.8
 */
function wpex_get_image_url( $image ) {
	if ( empty( $image ) ) {
		return ''; // don't return 0, empty string or false values as a URL.
	}

	if ( is_numeric( $image ) ) {
		$image = wp_get_attachment_url( $image );
	}

	return $image ? set_url_scheme( $image ) : '';
}

/**
 * Returns the number of columns for a particular grid.
 */
function wpex_get_array_first_value( $input ) {
	if ( is_array( $input ) ) {
		return reset( $input );
	}
	return $input;
}

/**
 * Returns current query vars.
 */
function wpex_get_query_vars() {
	global $wp_query;
	if ( isset( $wp_query ) ) {
		return $wp_query->query_vars;
	}
}

/**
 * Returns array of widget areas.
 *
 * @since 3.3.3
 */
function wpex_get_widget_areas() {
	global $wp_registered_sidebars;
	$widgets_areas = array();
	if ( ! empty( $wp_registered_sidebars ) ) {
		foreach ( $wp_registered_sidebars as $widget_area ) {
			$name = isset ( $widget_area['name'] ) ? $widget_area['name'] : '';
			$id = isset ( $widget_area['id'] ) ? $widget_area['id'] : '';
			if ( $name && $id ) {
				$widgets_areas[$id] = $name;
			}
		}
	}
	return $widgets_areas;
}

/**
 * Get Post Type Unlimited post type mod value.
 */
function wpex_get_ptu_type_mod( $post_type = '', $name = '' ) {
	if ( WPEX_PTU_ACTIVE ) {
		return TotalTheme\Integration\Post_Types_Unlimited::get_setting_value( $post_type, '_ptu_total_' . $name );
	}
	return false;
}

/**
 * Get Post Type Unlimited tax mod value.
 */
function wpex_get_ptu_tax_mod( $taxonomy = '', $name = '' ) {
	if ( WPEX_PTU_ACTIVE ) {
		return TotalTheme\Integration\Post_Types_Unlimited::get_tax_setting_value( $taxonomy, '_ptu_total_tax_' . $name );
	}
	return false;
}

/**
 * Returns a staff member based on a user.
 *
 * @since 5.1.1
 */
function wpex_get_staff_member_by_user( $user_id = '' ) {
	$relations = get_option( 'wpex_staff_users_relations' );
	if ( ! empty( $relations[$user_id] ) ) {
		return $relations[$user_id];
	}
}

/**
 * Returns a staff member based on a user.
 *
 * @since 5.1.1
 * @see https://developer.wordpress.org/reference/functions/get_avatar_data/
 */
function wpex_process_user_identifier( $id_or_email = '' ) {
	if ( ! $id_or_email ) {
		return;
	}
	// Process the user identifier.
	if ( is_numeric( $id_or_email ) ) {
		return get_user_by( 'id', absint( $id_or_email ) );
	} elseif( is_string( $id_or_email ) ) {
		if ( strpos( $id_or_email, '@md5.gravatar.com' ) ) {
			list( $id_or_email ) = explode( '@', $id_or_email );
		}
		return get_user_by( 'email', $id_or_email );
	} elseif( $id_or_email instanceof WP_User ) {
		// User object.
		return $id_or_email;
	} elseif( $id_or_email instanceof WP_Post ) {
		// Post object.
		return get_user_by( 'id', (int) $id_or_email->post_author );
	} elseif( $id_or_email instanceof WP_Comment ) {
		if ( ! is_avatar_comment_type( get_comment_type( $id_or_email ) ) ) {
			return;
		}
		if ( ! empty( $id_or_email->user_id ) ) {
			return get_user_by( 'id', (int) $id_or_email->user_id );
		}
	}
}

/**
 * Returns your Google reCAPTCHA keys.
 *
 * @since 5.2
 */
function wpex_get_recaptcha_keys( $type = '' ) {
	$keys = array(
		'site'   => '',
		'secret' => '',
	);
	$get_keys = get_option( 'wpex_recaptcha_keys' );
	if ( ! empty( $get_keys['site_key'] ) ) {
		$keys['site'] = $get_keys['site_key'];
	}
	if ( ! empty( $get_keys['secret_key'] ) ) {
		$keys['secret'] = $get_keys['secret_key'];
	}
	if ( $type ) {
		if ( 'site' === $type ) {
			return $keys['site'];
		}
		if ( 'secret' === $type ) {
			return $keys['secret'];
		}
	} else {
		return $keys;
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Current Post ID ]
/*-------------------------------------------------------------------------------*/

/**
 * Alternative to get_the_ID().
 *
 * Fixes issues with posts page and 3rd party plugins that use custom pages for archives
 * such as WooCommerce as well as returns the correct ID when in use in dynamic templates
 * modal windows, etc.
 *
 * @since 4.0
 * @todo cast return to (int)
 */
function wpex_get_current_post_id() {
	$id = '';

	// Check global variable.
	$query_var = get_query_var( 'wpex_current_post_id' );
	if ( ! empty( $query_var ) ) {
		return $query_var;
	}

	// If singular get_the_ID.
	if ( is_singular() ) {
		$id = get_queried_object_id() ?: get_the_ID();
	}

	// Posts page.
	if ( is_home() && $page_for_posts = get_option( 'page_for_posts' ) ) {
		$id = $page_for_posts;
	}

	// Get current shop ID.
	if ( empty( $id ) && wpex_is_woo_shop() ) {
		$id = wpex_parse_obj_id( wc_get_page_id( 'shop' ) );
	}

	// Old filter - deprecated in 5.1.2
	$id = apply_filters( 'wpex_post_id', $id );

	/*
	 * Filters the current post ID.
	 *
	 * @param string|int $id
	 */
	$id = apply_filters( 'wpex_current_post_id', $id );

	return $id;
}

/**
 * Set or update the current post id (wpex_current_post_id).
 *
 * @since 5.1.2
 * @return null
 */
function wpex_set_current_post_id( $post_ID = null ) {
	set_query_var( 'wpex_current_post_id', $post_ID );
}

/*-------------------------------------------------------------------------------*/
/* [ Taxonomy & Terms ]
/*-------------------------------------------------------------------------------*/

/**
 * Get category meta value.
 *
 * @since 5.0
 */
function wpex_get_category_meta( $category = '', $key = '' ) {
	if ( empty( $category ) ) {
		$category = wpex_get_loadmore_data( 'term_id' );
	}

	if ( empty( $category ) && is_category() ) {
		$category = get_queried_object_id();
	}

	if ( ! empty( $category ) ) {

		$mods  = get_option( 'category_' . sanitize_key( $category ) );
		$value = '';

		if ( isset( $mods[$key] ) ) {
			return $mods[$key];
		}

	}
}

/**
 * Get term meta value.
 *
 * @since 5.0
 */
function wpex_get_term_meta( $term_id = '', $key = '', $single = true ) {
	if ( empty( $term_id ) ) {
		$term_id = wpex_get_loadmore_data( 'term_id' );
	}

	if ( empty( $term_id ) && ( is_tax() || is_tag() || is_category() ) ) {
		$term_id = get_queried_object_id();
	}

	if ( ! empty( $term_id ) ) {
		return get_term_meta( $term_id, $key, $single );
	}
}

/**
 * Get term color classname.
 *
 * @since 5.3
 */
function wpex_get_term_color_class( $term = '' ) {
	$class = '';

	if ( ! $term ) {
		$term = wpex_get_post_primary_term();
	}

	$term = get_term( $term );

	if ( ! is_wp_error( $term ) && isset( $term->term_id ) ) {
		$class = 'has-term-' . sanitize_html_class( $term->term_id ) . '-color';
	}

	return $class;
}

/**
 * Get term background color classname.
 *
 * @since 5.3
 */
function wpex_get_term_background_color_class( $term = '' ) {
	$class = '';

	if ( ! $term ) {
		$term = wpex_get_post_primary_term();
	}

	$term = get_term( $term );

	if ( ! is_wp_error( $term ) && isset( $term->term_id ) ) {
		$class = 'has-term-' . sanitize_html_class( $term->term_id ) . '-background-color';
	}

	return $class;
}

/**
 * Get a post's primary term for a given taxonomy.
 *
 * @since 5.0
 */
function wpex_get_post_primary_term( $post = '', $taxonomy = '' ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return;
	}

	$primary_term = null;

	if ( empty( $taxonomy ) ) {
		$taxonomy = wpex_get_post_primary_taxonomy( $post );
	}

	if ( class_exists( 'WPSEO_Primary_Term' ) ) {
		$yoast_term = new WPSEO_Primary_Term( $taxonomy, $post->ID );
		if ( $yoast_term ) {
			$yoast_term = $yoast_term->get_primary_term();
			if ( $yoast_term && term_exists( $yoast_term, $taxonomy ) ) {
				$primary_term = $yoast_term;
			}
		}
	}

	$primary_term = apply_filters( 'wpex_get_post_primary_term', $primary_term, $post, $taxonomy );

	if ( $primary_term ) {

		$primary_term = get_term( $primary_term );

		if ( $primary_term && ! is_wp_error( $primary_term ) ) {

			return $primary_term;

		}

	}
}

/**
 * Get term thumbnail.
 *
 * @since 2.1.0
 */
function wpex_get_term_thumbnail_id( $term_id = '' ) {
	if ( is_callable( array( 'TotalThemeCore\Term_Thumbnails', 'get_term_thumbnail_id' ) ) ) {
		return TotalThemeCore\Term_Thumbnails::get_term_thumbnail_id( $term_id );
	}
}

/**
 * Returns 1st term ID.
 *
 * @since 3.2.0
 */
function wpex_get_first_term_id( $post = '', $taxonomy = 'category', $terms = '' ) {
	if ( ! taxonomy_exists( $taxonomy ) ) {
		return;
	}

	$post = get_post( $post );
	$primary_term = wpex_get_post_primary_term( $post, $taxonomy );

	if ( $primary_term && ! is_wp_error( $primary_term ) ) {
		$terms = array( $primary_term );
	}

	if ( ! $terms ) {
		$terms = get_the_terms( $post, $taxonomy );
	}

	if ( ! is_wp_error( $terms ) && ! empty( $terms[0] ) ) {
		return $terms[0]->term_id;
	}
}

/**
 * Returns 1st term name.
 *
 * @since 3.2.0
 */
function wpex_get_first_term_name( $post = '', $taxonomy = '', $terms = '' ) {
	$post = get_post( $post );

	if ( empty( $taxonomy ) ) {
		$taxonomy = wpex_get_post_primary_taxonomy( $post );
	}

	if ( ! taxonomy_exists( $taxonomy ) ) {
		return;
	}

	$primary_term = wpex_get_post_primary_term( $post, $taxonomy );

	if ( $primary_term && ! is_wp_error( $primary_term ) ) {
		$terms = array( $primary_term );
	}

	if ( ! $terms ) {
		$terms = get_the_terms( $post, $taxonomy );
	}

	if ( ! is_wp_error( $terms ) && ! empty( $terms[0] ) ) {
		return $terms[0]->name;
	}
}

/**
 * Returns 1st taxonomy name.
 *
 * @since 4.9
 */
function wpex_get_first_term( $post = '', $taxonomy = 'category', $terms = '' ) {
	if ( ! taxonomy_exists( $taxonomy ) ) {
		return;
	}

	$post = get_post( $post );

	$primary_term = wpex_get_post_primary_term( $post, $taxonomy );

	if ( $primary_term && ! is_wp_error( $primary_term ) ) {
		$terms = array( $primary_term );
	}

	if ( ! $terms ) {
		$terms = get_the_terms( $post, $taxonomy );
	}

	if ( ! is_wp_error( $terms ) && ! empty( $terms[0] ) ) {
		$attrs = array(
			'class' => 'term-' . absint( $terms[0]->term_id ),
			'title' => esc_attr( $terms[0]->name ),
		);
		return wpex_parse_html( 'span', $attrs, esc_html( $terms[0]->name ) );
	}
}

/**
 * Returns 1st taxonomy of any taxonomy with a link.
 *
 * @since 3.2.0
 */
function wpex_get_first_term_link( $post = '', $taxonomy = 'category', $terms = '', $before = '', $after = '', $instance = '' ) {

	// Allows post to actually be an array of function arguments since Total 4.9.
	if ( is_array( $post ) ) {
		if ( isset( $post['instance'] ) ) {
			$post = apply_filters( 'wpex_get_first_term_link_args', $post, $post['instance'] );
		}
		extract( $post );
		$post = get_post(); // reset the post variable.
	} else {
		$post = get_post( $post );
	}

	if ( ! taxonomy_exists( $taxonomy ) ) {
		return;
	}

	$primary_term = wpex_get_post_primary_term( $post, $taxonomy );

	if ( $primary_term && ! is_wp_error( $primary_term ) ) {
		$terms = array( $primary_term );
	}

	$terms = $terms ?: get_the_terms( $post, $taxonomy );

	if ( ! is_wp_error( $terms ) && ! empty( $terms[0] ) ) {

		$attrs = array(
			'href'  => esc_url( get_term_link( $terms[0], $taxonomy ) ),
			'class' => 'term-' . absint( $terms[0]->term_id ),
			//'title' => esc_attr( $terms[0]->name ), // Removed in 4.9.3 for accessibility reasons.
		);

		return $before . wpex_parse_html( 'a', $attrs, esc_html( $terms[0]->name ) ) . $after;

	}

}

/**
 * Echos 1st taxonomy of any taxonomy with a link.
 *
 * @since 2.0.0
 */
function wpex_first_term_link( $post = '', $taxonomy = 'category' ) {
	echo wpex_get_first_term_link( $post, $taxonomy );
}

/**
 * Returns a list of terms for specific taxonomy.
 *
 * @since 2.1.3
 */
function wpex_get_list_post_terms( $taxonomy = 'category', $show_links = true ) {
	return wpex_list_post_terms( $taxonomy, $show_links, false );
}

/**
 * List terms for specific taxonomy.
 *
 * @since 1.6.3
 */
function wpex_list_post_terms( $taxonomy = 'category', $show_links = true, $echo = true, $sep = '', $before = '', $after = '', $instance = '' ) {

	if ( is_array( $taxonomy ) ) {
		$defaults = array(
			'taxonomy'   => 'category',
			'show_links' => true,
			'echo'       => true,
			'sep'        => '',
			'before'     => '',
			'after'      => '',
			'instance'   => '',
			'class'      => '',
		);
		$args = wp_parse_args( $taxonomy, $defaults );
	} else {
		$args = array(
			'taxonomy'   => $taxonomy,
			'show_links' => $show_links,
			'echo'       => $echo,
			'sep'        => $sep,
			'before'     => $before,
			'after'      => $after,
			'instance'   => $instance,
			'class'      => '',
		);
	}

	if ( $echo ) {
		echo wpex_get_post_terms_list( $args );
	} else {
		return wpex_get_post_terms_list( $args );
	}

}

/**
 * Get a list of terms for a specific taxonomy.
 *
 * @since 4.9
 */
function wpex_get_post_terms_list( $args = array() ) {
	extract( $args );

	if ( ! taxonomy_exists( $taxonomy ) ) {
		return;
	}

	// Get terms
	$list_terms = array();
	$terms = get_the_terms( get_the_ID(), $taxonomy );

	// Return if no terms are found.
	if ( ! $terms ) {
		return;
	}

	// Loop through terms.
	foreach ( $terms as $term ) {

		$attrs = array(
			'class' => array(
				'term-' . absint( $term->term_id )
			),
		);

		if ( $class ) {
			$attrs['class'][] = $class;
		}

		if ( $show_links ) {

			$attrs['href'] = esc_url( get_term_link( $term->term_id, $taxonomy ) );

			$list_terms[] = wpex_parse_html( 'a', $attrs, esc_html( $term->name ) );

		} else {

			$list_terms[] = wpex_parse_html( 'span', $attrs, esc_html( $term->name ) );

		}
	}

	// Turn into comma seperated string.
	if ( $list_terms && is_array( $list_terms ) ) {
		if ( empty( $sep ) ) {
			$sep = apply_filters( 'wpex_list_post_terms_sep', wpex_inline_list_sep( 'post_terms_list' ), $instance );
		}
		$list_terms = implode( $sep, $list_terms );
	}

	// Apply filters (can be used to change the comas to something else).
	$list_terms = apply_filters( 'wpex_list_post_terms', $list_terms, $taxonomy );

	// Add before/after only if we have terms.
	if ( $list_terms ) {
		return $before . $list_terms . $after;
	}
}

/**
 * Returns the primary taxonomy of a given post.
 *
 * @since 5.0
 */
function wpex_get_post_primary_taxonomy( $post = null ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return;
	}

	$taxonomy = wpex_get_post_type_cat_tax( get_post_type( $post ) );

	$taxonomy = apply_filters( 'wpex_get_post_primary_taxonomy', $taxonomy ); // @todo deprecate.

	/**
	 * Filters the post primary taxonomy name.
	 *
	 * @param string $taxonomy
	 */
	$taxonomy = apply_filters( 'wpex_post_primary_taxonomy', $taxonomy );

	return $taxonomy;
}

/**
 * Returns the "category" taxonomy for a given post type.
 *
 * @since 2.0.0
 */
function wpex_get_post_type_cat_tax( $post_type = '' ) {
	if ( ! $post_type ) {
		$post_type = get_post_type();
	}

	switch ( $post_type ) {
		case 'post':
			$taxonomy = 'category';
			break;
		case 'portfolio':
			$taxonomy = 'portfolio_category';
			break;
		case 'staff':
			$taxonomy = 'staff_category';
			break;
		case 'testimonials':
			$taxonomy = 'testimonials_category';
			break;
		case 'product':
			$taxonomy = 'product_cat';
			break;
		case 'tribe_events':
			$taxonomy = 'tribe_events_cat';
			break;
		case 'download':
			$taxonomy = 'download_category';
			break;
		default:
			$taxonomy = '';
			break;
	}

	if ( WPEX_PTU_ACTIVE ) {

		$ptu_check = wpex_get_ptu_type_mod( $post_type, 'main_taxonomy' );

		if ( $ptu_check ) {
			$taxonomy = $ptu_check;
		}

	}

	/**
	 * Filters the post type primary taxonomy name.
	 *
	 * @param string $taxonomy
	 */
	$taxonomy = (string) apply_filters( 'wpex_get_post_type_cat_tax', $taxonomy, $post_type );

	return $taxonomy;
}

/**
 * Retrieve all term data.
 *
 * @since 2.1.0
 */
function wpex_get_term_data() {
	return get_option( 'wpex_term_data' );
}

/*-------------------------------------------------------------------------------*/
/* [ Sliders ]
/*-------------------------------------------------------------------------------*/

/**
 * Returns slider settings.
 *
 * @since 4.4.1
 */
function wpex_get_post_slider_settings( $settings = array() ) {
	$defaults = array(
		'filter_tag'      => 'wpex_slider_data',
		'fade'            => ( 'fade' == get_theme_mod( 'post_slider_animation', 'slide' ) ) ? 'true' : 'false',
		'auto-play'       => ( get_theme_mod( 'post_slider_autoplay', false ) ) ? 'true' : 'false',
		'buttons'         => ( get_theme_mod( 'post_slider_dots', false ) ) ? 'true' : 'false',
		'loop'            => ( get_theme_mod( 'post_slider_loop', true ) ) ? 'true' : 'false',
		'arrows'          => ( get_theme_mod( 'post_slider_arrows', true ) ) ? 'true' : 'false',
		'fade-arrows'     => ( get_theme_mod( 'post_slider_arrows_on_hover', false ) ) ? 'true' : 'false',
		'animation-speed' => intval( get_theme_mod( 'post_slider_animation_speed', 600 ) ),
	);

	if ( get_theme_mod( 'post_slider_thumbnails', apply_filters( 'wpex_post_gallery_slider_has_thumbnails', true ) ) ) {
		$defaults['thumbnails']        = 'true';
		$defaults['thumbnails-height'] = intval( get_theme_mod( 'post_slider_thumbnail_height', '60' ) );
		$defaults['thumbnails-width']  = intval( get_theme_mod( 'post_slider_thumbnail_width', '60' ) );
	}

	$settings = wp_parse_args( $settings, $defaults );

	/**
	 * Filters the post slider settings.
	 *
	 * @param array $settings
	 */
	$settings = apply_filters( $settings['filter_tag'], $settings );

	return $settings;
}

/**
 * Returns data attributes for post sliders.
 *
 * @since 4.3
 */
function wpex_get_slider_data( $settings = array() ) {
	$settings = wpex_get_post_slider_settings( $settings );

	if ( ! $settings ) {
		return;
	}

	unset( $settings['filter_tag'] ); // not needed for loop.

	extract( $settings );

	$data = '';

	foreach ( $settings as $key => $val ) {
		$data .= ' data-' . esc_attr( $key ) . '="' . esc_attr( $val ) . '"';
	}

	return $data;
}

/**
 * Echos data attributes for post sliders.
 *
 * @since 2.0.0
 */
function wpex_slider_data( $args = '' ) {
	echo wpex_get_slider_data( $args );
}

/*-------------------------------------------------------------------------------*/
/* [ Images ]
/*-------------------------------------------------------------------------------*/

/**
 * Echo animation classes for entries.
 *
 * @since 1.1.6
 */
function wpex_entry_image_animation_classes() {
	if ( $classes = wpex_get_entry_image_animation_classes() ) {
		echo ' ' . esc_attr( $classes );
	}
}

/**
 * Returns animation classes for entries.
 *
 * @since 1.1.6
 */
function wpex_get_entry_image_animation_classes() {
	$classes = '';

	$type = get_post_type();

	if ( 'post' === $type ) {
		$type = 'blog';
	}

	$animation = get_theme_mod( $type . '_entry_image_hover_animation' );

	if ( $animation ) {
		$classes = 'wpex-image-hover ' . sanitize_html_class( $animation );
	}

	/**
	 * Filters the entry image animation classes.
	 *
	 * @param string $classes
	 */
	$classes = (string) apply_filters( 'wpex_entry_image_animation_classes', $classes );

	return $classes;
}

/**
 * Returns attachment data.
 *
 * @since 2.0.0
 */
function wpex_get_attachment_data( $attachment = '', $return = 'array' ) {
	if ( ! $attachment || 'none' === $return ) {
		return;
	}

	switch ( $return ) {
		case 'url':
		case 'src':
			return wp_get_attachment_url( $attachment );
			break;
		case 'alt':
			return get_post_meta( $attachment, '_wp_attachment_image_alt', true );
			break;
		case 'title':
			return get_the_title( $attachment );
			break;
		case 'caption':
			return wp_get_attachment_caption( $attachment );
			break;
		case 'description':
			return get_post_field( 'post_content', $attachment );
			break;
		case 'video':
			return wpex_get_attachment_video( $attachment );
			break;
		default:

			$url = wp_get_attachment_url( $attachment );

			return array(
				'url'         => $url,
				'src'         => $url, // fallback
				'alt'         => get_post_meta( $attachment, '_wp_attachment_image_alt', true ),
				'title'       => get_the_title( $attachment ),
				'caption'     => wp_get_attachment_caption( $attachment ),
				'description' => get_post_field( 'post_content', $attachment ),
				'video'       => wpex_get_attachment_video( $attachment ),
			);
			break;
	}
}

/**
 * Returns attachment video.
 *
 * @since 5.0.8
 */
function wpex_get_attachment_video( $attachment = '' ) {
	$video = get_post_meta( $attachment, '_video_url', true );

	if ( $video ) {
		$video = esc_url( $video );
	}

	/**
	 * Filters the attachment video url.
	 *
	 * @param string $video
	 */
	$video = (string) apply_filters( 'wpex_attachment_video', $video, $attachment );

	return $video;
}

/**
 * Checks if a featured image has a caption.
 *
 * @since 2.0.0
 */
function wpex_featured_image_caption( $post = '' ) {
	$post = get_post( $post );
	$caption = wp_get_attachment_caption( get_post_thumbnail_id( $post ) );

	/**
	 * Filters the featured image caption.
	 *
	 * @param string $caption The thumbnail caption returned by wp_get_attachment_caption.
	 * @param int $post_id The post ID.
	 */
	$caption = apply_filters( 'wpex_featured_image_caption', $caption, $post->ID );

	return $caption;
}

/**
 * Return placeholder image.
 *
 * @since 5.0
 */
function wpex_get_placeholder_image() {
	$src = wpex_placeholder_img_src();
	if ( $src ) {
		return '<img src="' . set_url_scheme( esc_url( $src ) ) . '">';
	}
}

/**
 * Return placeholder image src.
 *
 * @since 2.1.0
 */
function wpex_placeholder_img_src() {
	return apply_filters( 'wpex_placeholder_img_src', wpex_asset_url( 'images/placeholder.jpg' ) );
}

/**
 * Blank Image.
 *
 * @since 2.1.0
 */
function wpex_blank_img_src() {
	return esc_url( WPEX_THEME_URI .'/images/slider-pro/blank.png' );
}

/**
 * Returns image hover classnames.
 *
 * @since 2.0.0
 */
function wpex_image_hover_classes( $style = '' ) {
	if ( ! $style ) {
		return;
	}
	$classes = array( 'wpex-image-hover' );
	$classes[] = sanitize_html_class( $style );
	return implode( ' ', $classes );
}

/**
 * Returns image rendering class.
 *
 * @since 2.0.0
 */
function wpex_image_rendering_class( $rendering ) {
	return 'image-rendering-' . sanitize_html_class( $rendering );
}

/**
 * Returns image filter class.
 *
 * @since 2.0.0
 */
function wpex_image_filter_class( $filter ) {
	if ( ! $filter || 'none' === $filter ) {
		return;
	}
	return 'image-filter-' . sanitize_html_class( $filter );
}

/*-------------------------------------------------------------------------------*/
/* [ Icons ]
/*-------------------------------------------------------------------------------*/

/**
 * Echo Theme Icon HTML.
 *
 * @since 4.8
 */
function wpex_theme_icon_html( $icon = '', $extra_class = '' ) {
	echo wpex_get_theme_icon_html( $icon, $extra_class );
}

/**
 * Returns Theme Icon HTML.
 *
 * @since 4.8
 */
if ( ! function_exists( 'wpex_get_theme_icon_html' ) ) {
	function wpex_get_theme_icon_html( $icon = '', $extra_class = '' ) {
		if ( is_string( $icon ) && 0 === strpos( $icon, 'ticon' ) ) {
			$class = explode( ' ', $icon );
		} else {
			$class = array( 'ticon' );
			$class[] = 'ticon-' . sanitize_html_class( $icon );
		}

		if ( $extra_class ) {
			$class[] = $extra_class;
		}
		$html = '<span class="' . esc_attr( implode( ' ', $class ) ) . '" aria-hidden="true"></span>';

		/**
		 * Filters the theme icon html.
		 *
		 * @param string $html
		 */
		$html = apply_filters( 'wpex_theme_icon_html', $html, $icon, $extra_class );

		return $html;
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Buttons ]
/*-------------------------------------------------------------------------------*/

/**
 * Returns social button class.
 *
 * @since 3.0.0
 * @todo add opacity hovers here (add new parameter $has_hover = true )
 */
function wpex_get_social_button_class( $style = 'default' ) {
	$class = '';

	if ( 'default' === $style ) {

		/**
		 * Filters the default social button style.
		 *
		 * @param string $style
		 */
		$style = (string) apply_filters( 'wpex_default_social_button_style', 'none' );
	}

	switch ( $style ) {
		case 'none':
			$class = 'wpex-social-btn-no-style';
			break;

		// Colored
		case 'colored':
			$class = 'wpex-social-btn-colored wpex-social-color';
			break;

		// Minimal
		case 'minimal':
			$class = 'wpex-social-btn-minimal wpex-social-color-hover';
			break;
		case 'minimal-rounded':
			$class = 'wpex-social-btn-minimal wpex-social-color-hover wpex-semi-rounded';
			break;
		case 'minimal-round':
			$class = 'wpex-social-btn-minimal wpex-social-color-hover wpex-round';
			break;

		// Flat
		case 'flat':
			$class = 'wpex-social-btn-flat wpex-social-color-hover';
			break;
		case 'flat-rounded':
			$class = 'wpex-social-btn-flat wpex-social-color-hover wpex-semi-rounded';
			break;
		case 'flat-round';
			$class = 'wpex-social-btn-flat wpex-social-color-hover wpex-round';
			break;

		// Flat color
		case 'flat-color':
			$class = 'wpex-social-btn-flat wpex-social-bg';
			break;
		case 'flat-color-rounded':
			$class = 'wpex-social-btn-flat wpex-social-bg wpex-semi-rounded';
			break;
		case 'flat-color-round':
			$class = 'wpex-social-btn-flat wpex-social-bg wpex-round';
			break;

		// 3D
		case '3d':
			$class = 'wpex-social-btn-3d';
			break;
		case '3d-color':
			$class = 'wpex-social-btn-3d wpex-social-bg';
			break;

		// Black
		case 'black':
			$class = 'wpex-social-btn-black';
			break;
		case 'black-rounded':
			$class = 'wpex-social-btn-black wpex-semi-rounded';
			break;
		case 'black-round':
			$class = 'wpex-social-btn-black wpex-round';
			break;

		// Black + Color Hover
		case 'black-ch':
			$class = 'wpex-social-btn-black-ch wpex-social-bg-hover';
			break;
		case 'black-ch-rounded':
			$class = 'wpex-social-btn-black-ch wpex-social-bg-hover wpex-semi-rounded';
			break;
		case 'black-ch-round':
			$class = 'wpex-social-btn-black-ch wpex-social-bg-hover wpex-round';
			break;

		// Graphical
		case 'graphical':
			$class = 'wpex-social-bg wpex-social-btn-graphical';
			break;
		case 'graphical-rounded':
			$class = 'wpex-social-bg wpex-social-btn-graphical wpex-semi-rounded';
			break;
		case 'graphical-round':
			$class = 'wpex-social-bg wpex-social-btn-graphical wpex-round';
			break;

		// Bordered
		case 'bordered':
			$class = 'wpex-social-btn-bordered wpex-social-border wpex-social-color';
			break;
		case 'bordered-rounded':
			$class = 'wpex-social-btn-bordered wpex-social-border wpex-semi-rounded wpex-social-color';
			break;
		case 'bordered-round':
			$class = 'wpex-social-btn-bordered wpex-social-border wpex-round wpex-social-color';
			break;

	}

	/**
	 * Filters the social button class.
	 *
	 * @param string $class
	 */
	$class = (string) apply_filters( 'wpex_get_social_button_class', 'wpex-social-btn ' . $class );

	return $class;
}

/**
 * Returns theme button classes based on args.
 *
 * @since 3.2.0
 */
function wpex_get_button_classes( $style = array(), $color = '', $size = '', $align = '' ) {
	$args = $style;

	if ( ! is_array( $args ) ) {
		$args = array(
			'style' => $style,
			'color' => $color,
			'size'  => $size,
			'align' => $align,
		);
	}

	$defaults = apply_filters( 'wpex_button_default_args', array(
		'style' => get_theme_mod( 'default_button_style' ),
		'color' => get_theme_mod( 'default_button_color' ),
		'size'  => '',
		'align' => '',
	) );

	foreach ( $defaults as $key => $value ) {
		if ( empty( $args[$key] ) ) {
			$args[$key] = $defaults[ $key ];
		}
	}

	extract( $args );

	$classes = array();

	switch ( $style ) {
		case 'plain-text':
			$classes[] = 'theme-txt-link';
			break;
		default:
			if ( $style ) {
				$classes[] = 'theme-button';
				$classes[] = sanitize_html_class( $style );
			} else {
				$classes[] = 'theme-button';
			}
			break;
	}

	if ( $color ) {
		$classes[] = sanitize_html_class( $color );
	}

	if ( $size ) {
		$classes[] = sanitize_html_class( $size );
	}

	if ( $align ) {
		$classes[] = 'align-' . sanitize_html_class( $align );
	}

	/**
	 * Filters the default theme button classes.
	 *
	 * @param array $classes
	 */
	$classes = (array) apply_filters( 'wpex_button_classes', $classes, $args );

	// Sanitize classes
	$classes = array_map( 'esc_attr', $classes );
	$classes = array_filter( $classes );

	// Convert class to string.
	$class = implode( ' ', $classes );

	// Apply deprecated filters.
	$class = apply_filters( 'wpex_get_theme_button_classes', $class, $style, $color, $size, $align ); // @todo deprecate

	return $class;
}

/**
 * Returns CSS for custom button color based on style.
 *
 * @since 4.3.2
 */
function wpex_get_button_custom_color_css( $style = 'flat', $color ='' ) {
	if ( empty( $color ) ) {
		return;
	}

	switch ( $style ) {

		// Alter color
		case 'plain-text';
			return 'color:' . esc_attr( $color ) . ';';
			break;

		// Alter background-color
		case 'flat':
		case 'graphical':
		case 'three-d':
			return 'background:' . esc_attr( $color ) . ';';
			break;

		// Alter border-color
		case 'outline';
		case 'minimal-border';
			return 'border-color:' . esc_attr( $color ) . ';color:' . esc_attr( $color ) . ';';
			break;
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Lightbox ]
/*-------------------------------------------------------------------------------*/

/**
 * Returns array of lightbox settings.
 */
function wpex_get_lightbox_settings() {
	$animationDuration = absint( get_theme_mod( 'lightbox_animation_duration', 366 ) );

	$settings = array(
		'animationEffect'       => 0 === $animationDuration ? '0' : 'fade', // 0, zoom, fade, zoom-in-out
		'zoomOpacity'           => 'auto', // If opacity is "auto", then opacity will be changed if image and thumbnail have different aspect ratios
		'animationDuration'     => $animationDuration,
		'transitionEffect'      => esc_html( wpex_get_mod( 'lightbox_transition_effect', 'fade', true ) ),
		'transitionDuration'    => absint( wpex_get_mod( 'lightbox_transition_duration', 366, true ) ),
		'gutter'                => absint( 50 ),
		'loop'                  => wp_validate_boolean( get_theme_mod( 'lightbox_loop', false ) ),
		'arrows'                => wp_validate_boolean( get_theme_mod( 'lightbox_arrows', true ) ),
		'infobar'               => wp_validate_boolean( true ),
		'smallBtn'              => 'auto',
		'closeExisting'         => true, // prevent multiple instance stacking
		//'hideScrollbar'         => wp_validate_boolean( false ),
		//'preventCaptionOverlap' => true, // causes jumpiness on first item
		'buttons'               => array(
			'zoom',
			'slideShow',
			'close',
			//'share',
		),
		'slideShow' => array(
			'autoStart' => wp_validate_boolean( get_theme_mod( 'lightbox_slideshow_autostart', false ) ),
			'speed'     => absint( get_theme_mod( 'lightbox_slideshow_speed', 3000 ) ),
		),
		'lang' => 'en',
		'i18n' => array(
			'en' => array(
				'CLOSE'          => esc_html__( 'Close', 'total' ),
				'NEXT'           => esc_html__( 'Next', 'total' ),
				'PREV'           => esc_html__( 'Previous', 'total' ),
				'ERROR'          => esc_html__( 'The requested content cannot be loaded. Please try again later.', 'total' ),
				'PLAY_START'     => esc_html__( 'Start slideshow', 'total' ),
				'PLAY_STOP'      => esc_html__( 'Pause slideshow', 'total' ),
				'FULL_SCREEN'    => esc_html__( 'Full screen', 'total' ),
				'THUMBS'         => esc_html__( 'Thumbnails', 'total' ),
				'DOWNLOAD'       => esc_html__( 'Download', 'total' ),
				'SHARE'          => esc_html__( 'Share', 'total' ),
				'ZOOM'           => esc_html__( 'Zoom', 'total' ),
			)
		),
	);

	if ( wp_validate_boolean( get_theme_mod( 'lightbox_thumbnails', true ) ) ) {
		$settings['buttons'][] = 'thumbs';
		$settings['thumbs'] = array(
			'autoStart'   => wp_validate_boolean( get_theme_mod( 'lightbox_thumbnails_auto_start', false ) ),
			'hideOnClose' => wp_validate_boolean( true ),
			'axis'        => 'y',
		);
	}

	if ( wp_validate_boolean( get_theme_mod( 'lightbox_fullscreen', false ) ) ) {
		$settings['buttons'][] = 'fullScreen';
	}

	$settings = apply_filters( 'wpex_get_lightbox_settings', $settings ); // @todo deprecate

	/**
	 * Filters the lightbox settings.
	 *
	 * @param array $settings
	 */
	$settings = (array) apply_filters( 'wpex_lightbox_settings', $settings ); // new filter since v5

	return $settings;
}

/**
 * Echo lightbox image URL.
 *
 * @since 2.0.0
 */
function wpex_lightbox_image( $attachment = '' ) {
	echo wpex_get_lightbox_image( $attachment );
}

/**
 * Returns lightbox image URL.
 *
 * @since 2.0.0
 */
function wpex_get_lightbox_image( $attachment = '' ) {

	// If $attachment is a post then lets get the attachment from the post.
	if ( 'attachment' !== get_post_type( $attachment ) ) {
		$attachment = get_post_thumbnail_id( $attachment );
	}

	// Get attachment if empty (in standard WP loop).
	if ( ! $attachment ) {
		if ( 'attachment' == get_post_type() ) {
			$attachment = get_the_ID();
		} else {
			if ( $meta = get_post_meta( get_the_ID(), 'wpex_lightbox_thumbnail', true ) ) {
				$attachment = $meta;
			} else {
				$attachment = get_post_thumbnail_id();
			}
		}
	}

	// If the attachment is an ID lets get the URL.
	if ( is_numeric( $attachment ) ) {
		$image = '';
	} elseif ( is_array( $attachment ) ) {
		return $attachment[0];
	} else {
		return $attachment;
	}

	if ( $filtered_image = apply_filters( 'wpex_get_lightbox_image', null, $attachment ) ) {
		return $filtered_image;
	}

	// Sanitize data.
	$image = wpex_get_post_thumbnail_url( array(
		'attachment' => $attachment,
		'image'      => $image,
		'size'       => apply_filters( 'wpex_get_lightbox_image_size', 'lightbox' ),
		'retina'     => false, // no need to create retina for lightbox images.
	) );

	return esc_url( $image );
}

/**
 * Returns array for use with inline gallery lightbox.
 *
 * @since 4.8
 */
function wpex_parse_inline_lightbox_gallery( $attachments = array() ) {
	if ( ! $attachments ) {
		return;
	}
	$gallery = array();
	$has_titles = apply_filters( 'wpex_inline_lightbox_gallery_titles', true );
	$has_captions = apply_filters( 'wpex_inline_lightbox_gallery_captions', true );
	$count = -1;
	foreach ( $attachments as $attachment ) {
		$gallery_image_escaped = esc_url( wpex_get_lightbox_image( $attachment ) );
		$video_escaped         = esc_url( wpex_get_video_embed_url( wpex_get_attachment_data( $attachment, 'video' ) ) );
		if ( $gallery_image_escaped || $video_escaped ) {
			$count ++;
			$gallery[$count]['src'] = $video_escaped ?: $gallery_image_escaped;
			if ( $video_escaped && $gallery_image_escaped ) {
				$gallery[$count]['thumb'] = $gallery_image_escaped;
			}
			if ( $has_titles ) {
				$title = wpex_get_attachment_data( $attachment, 'alt' );
				if ( $title ) {
					$gallery[$count]['title'] = esc_html( $title );
				}
			}
			if ( $has_captions ) {
				$caption = wpex_get_attachment_data( $attachment, 'caption' );
				if ( $caption ) {
					$gallery[$count]['caption'] = wp_kses_post( $caption );
				}
			}
		}
	}
	return htmlspecialchars( wp_json_encode( $gallery ) );
}

/*-------------------------------------------------------------------------------*/
/* [ WooCommerce ]
/*-------------------------------------------------------------------------------*/

/**
 * Outputs placeholder image.
 *
 * @since 1.0.0
 */
function wpex_woo_placeholder_img() {
	$placeholder = '';
	if ( function_exists( 'wc_placeholder_img_src' ) ) {
		$wc_placeholder_img_src = wc_placeholder_img_src();
		if ( $wc_placeholder_img_src ) {
			$placeholder = '<img src="' . esc_url( $wc_placeholder_img_src ) . '" alt="' . esc_attr__( 'Placeholder Image', 'total' ) . '" class="woo-entry-image-main">';
		}
	}
	echo apply_filters( 'wpex_woo_placeholder_img_html', $placeholder );
}

/**
 * Outputs product price.
 *
 * @since 1.0.0
 */
function wpex_woo_product_price( $post = '', $before = '', $after = '' ) {
	echo wpex_get_woo_product_price( $post );
}

/**
 * Returns product price.
 *
 * @since 1.0.0
 */
function wpex_get_woo_product_price( $post = '', $before = '', $after = '' ) {
	$post = get_post( $post );
	if ( 'product' == get_post_type( $post ) ) {
		$product = wc_get_product( $post );
		$price = $product->get_price_html();
		if ( $price ) {
			return $before . $price . $after;
		}
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Elementor ]
/*-------------------------------------------------------------------------------*/

/**
 * Returns elementor content to display on the front-end.
 *
 * @since 4.9
 */
function wpex_get_elementor_content_for_display( $template_id = '' ) {
	if ( shortcode_exists( 'elementor-template' ) ) {
		return do_shortcode( '[elementor-template id="' . absint( $template_id ) . '"]' );
	}
	$front_end = new \Elementor\Frontend();
	return $front_end->get_builder_content_for_display( $template_id );
}


/*-------------------------------------------------------------------------------*/
/* [ WPBakery ]
/*-------------------------------------------------------------------------------*/

/**
 * Output CSS for wpbakery template.
 *
 * @since 5.0
 */
function wpex_vc_meta_inline_style( $id = '' ) {
	echo wpex_get_vc_meta_inline_style( $id );
}

/**
 * Return inline style element for the CSS saved in the VC meta for any post ID.
 *
 * @since 4.0
 */
function wpex_get_vc_meta_inline_style( $id = '' ) {
	if ( ! $id ) {
		return;
	}
	$css = get_post_meta( $id, '_wpb_shortcodes_custom_css', true );
	if ( $css ) {
		return '<style>' . wp_strip_all_tags( $css ) . '</style>';
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Tribe Events ]
/*-------------------------------------------------------------------------------*/

/**
 * Check if currently on a tribe events page.
 *
 * @since 4.0
 */
function wpex_is_tribe_events() {
	if ( is_search() ) {
		return false; // fixes some bugs
	}
	if ( tribe_is_event()
		|| tribe_is_view()
		|| tribe_is_list_view()
		|| tribe_is_event_category()
		|| tribe_is_in_main_loop()
		|| tribe_is_day()
		|| tribe_is_month()
		|| is_singular( 'tribe_events' ) ) {
		return true;
	}
}

/**
 * Displays event date.
 *
 * @since 3.3.3
 */
function wpex_get_tribe_event_date( $instance = '' ) {
	if ( ! function_exists( 'tribe_get_start_date' ) ) {
		return;
	}
	return apply_filters(
		'wpex_get_tribe_event_date',
		tribe_get_start_date( get_the_ID(), false, get_option( 'date_format' ) ),
		$instance
	);
}

/**
 * Gets correct tribe events page ID.
 *
 * @since 3.3.3
 */
function wpex_get_tribe_events_main_page_id() {
	if ( $mod = get_theme_mod( 'tribe_events_main_page' ) ) {
		return $mod;
	}

	if ( class_exists( 'Tribe__Settings_Manager' ) ) {
		$page_slug = Tribe__Settings_Manager::get_option( 'eventsSlug', 'events' );
		if ( $page_slug && $page = get_page_by_path( $page_slug ) ) {
			return $page->ID;
		}
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Enqueue Scripts ]
/*-------------------------------------------------------------------------------*/

/**
 * Define default masonry settings.
 *
 * @since 5.1
 */
function wpex_get_masonry_settings() {
	$settings = array(
		'transformsEnabled'  => true, // I believe this maybe deprecated in isotope now.
		'isOriginLeft'       => ! is_rtl(),
		'transitionDuration' => '0.4s',
		'layoutMode'         => 'masonry',
		'horizontalOrder'    => true,
	);

	/**
	 * Filters the masonry grid settings.
	 *
	 * @param array $settings
	 */
	$settings = (array) apply_filters( 'wpex_masonry_settings', $settings );

	return $settings;
}

/**
 * Enqueue masonry scripts.
 *
 * @since 5.0
 */
function wpex_enqueue_masonry_scripts() {
	wpex_enqueue_isotope_scripts();
}

/**
 * Enqueue isotope scripts.
 *
 * @since 4.9
 */
function wpex_enqueue_isotope_scripts() {
	wp_enqueue_script( 'imagesloaded' );
	wp_enqueue_script( 'isotope' );
	wp_enqueue_script( 'wpex-isotope' );
}

/**
 * Enqueue lightbox scripts.
 */
function wpex_enqueue_lightbox_scripts() {
	TotalTheme\Lightbox::enqueue_scripts();
}

/**
 * Enqueue slider scripts.
 *
 * @since 4.9
 */
function wpex_enqueue_slider_pro_scripts( $noCarouselThumbnails = false ) {
	wp_enqueue_script( 'slider-pro' );
	wp_enqueue_style( 'slider-pro' );
	if ( $noCarouselThumbnails ) {
		wp_enqueue_script( 'wpex-slider-pro-custom-thumbs' );
	}
	wp_enqueue_script( 'wpex-slider-pro' );
}

/*-------------------------------------------------------------------------------*/
/* [ PHP Helpers ]
/*-------------------------------------------------------------------------------*/

/**
 * Inserts a new key/value before the key in the array.
 *
 * @param $key  The key to insert before.
 * @param $array  An array to insert in to.
 * @param $new_key  The key/array to insert.
 * @param $new_value  An value to insert.
 */
function wpex_array_insert_before( $key, array $array, $new_key, $new_value = null ) {
	if ( array_key_exists( $key, $array ) ) {
		$new = array();
		foreach( $array as $k => $value ) {
			if ( $k === $key ) {
				if ( is_array( $new_key ) && count( $new_key ) > 0) {
					$new = array_merge( $new, $new_key );
				} else {
					$new[$new_key] = $new_value;
				}
			}
			$new[$k] = $value;
		}
		return $new;
	}
	return false;
}

/**
 * Inserts a new key/value after the key in the array.
 *
 * @param $key  The key to insert after.
 * @param $array  An array to insert in to.
 * @param $new_key  The key/array to insert.
 * @param $new_value  An value to insert.
 */
function wpex_array_insert_after( $key, array  $array, $new_key, $new_value = null ) {
	if ( array_key_exists( $key, $array ) ) {
		$new = array();
		foreach( $array as $k => $value ) {
			$new[$k] = $value;
			if ( $k === $key ) {
				if (is_array( $new_key ) && count( $new_key ) > 0) {
					$new = array_merge( $new, $new_key );
				} else {
					$new[$new_key] = $new_value;
				}
			}
		}
		return $new;
	}
	return false;
}

/*-------------------------------------------------------------------------------*/
/* [ Fallbacks ]
/*-------------------------------------------------------------------------------*/

/**
 * Output inline style tag based on attributes.
 */
function wpex_parse_inline_style( $atts = array(), $add_style = true ) {
	if ( ! empty( $atts ) && is_array( $atts ) && function_exists( 'vcex_inline_style' ) ) {
		return vcex_inline_style( $atts, $add_style );
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Other ]
/*-------------------------------------------------------------------------------*/

/**
 * Output screen reader text.
 *
 * @since 5.0.8
 */
function wpex_screen_reader_text( $text ) {
	echo '<span class="screen-reader-text">' . $text . '</span>';
}

/**
 * Check user access.
 *
 * @since 4.0
 */
function wpex_user_can_access( $check, $custom_callback = '' ) {
	switch ( $check ) {
		case 'not_paged':
			return is_paged() ? false : true;
			break;
		case 'logged_in':
			return is_user_logged_in();
			break;
		case 'logged_out':
			return is_user_logged_in() ? false : true;
			break;
		case 'custom':
			if ( ! is_callable( $custom_callback ) ) {
				return true;
			}
			return call_user_func( $custom_callback );
			break;
		default:
			return true;
		break;
	}
}

/**
 * Returns string version of WP core get_post_class.
 *
 * @since 3.5.0
 * @todo deprecate? Doesn't seem to be used anymore.
 */
function wpex_get_post_class( $class = '', $post_id = null ) {
	return 'class="' . esc_attr( implode( ' ', get_post_class( $class, $post_id ) ) ) . '"';
}

/**
 * Minify CSS.
 *
 * @since 1.6.3
 */
function wpex_minify_css( $css = '' ) {
	if ( ! $css ) {
		return;
	}

	// Normalize whitespace.
	$css = preg_replace( '/\s+/', ' ', $css );

	// Remove ; before }
	$css = preg_replace( '/;(?=\s*})/', '', $css );

	// Remove space after , : ; { } */ >
	$css = preg_replace( '/(,|:|;|\{|}|\*\/|>) /', '$1', $css );

	// Remove space before , ; { }
	$css = preg_replace( '/ (,|;|\{|})/', '$1', $css );

	// Strips leading 0 on decimal values (converts 0.5px into .5px).
	$css = preg_replace( '/(:| )0\.([0-9]+)(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}.${2}${3}', $css );

	// Strips units if value is 0 (converts 0px to 0).
	$css = preg_replace( '/(:| )(\.?)0(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}0', $css );

	return trim( $css );
}

/**
 * Allow to remove method for an hook when, it's a class method used and class doesn't have global for instanciation.
 *
 * @since 3.4.0
 */
function wpex_remove_class_filter( $hook_name = '', $class_name ='', $method_name = '', $priority = 0 ) {
	global $wp_filter;

	// Make sure class exists
	if ( ! class_exists( $class_name ) ) {
		return false;
	}

	// Take only filters on right hook name and priority
	if ( ! isset($wp_filter[$hook_name][$priority] ) || ! is_array( $wp_filter[$hook_name][$priority] ) ) {
		return false;
	}

	// Loop on filters registered
	foreach( (array) $wp_filter[$hook_name][$priority] as $unique_id => $filter_array ) {

		// Test if filter is an array ! (always for class/method)
		// @todo consider using has_action instead
		// @link https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/
		if ( isset( $filter_array['function'] ) && is_array( $filter_array['function'] ) ) {

			// Test if object is a class, class and method is equal to param !
			if ( is_object( $filter_array['function'][0] )
				&& get_class( $filter_array['function'][0] )
				&& get_class( $filter_array['function'][0] ) == $class_name
				&& $filter_array['function'][1] == $method_name
			) {
				if ( isset( $wp_filter[$hook_name] ) ) {
					// WP 4.7
					if ( is_object( $wp_filter[$hook_name] ) ) {
						unset( $wp_filter[$hook_name]->callbacks[$priority][$unique_id] );
					}
					// WP 4.6
					else {
						unset( $wp_filter[$hook_name][$priority][$unique_id] );
					}
				}
			}

		}

	}

	return false;
}