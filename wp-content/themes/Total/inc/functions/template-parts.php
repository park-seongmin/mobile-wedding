<?php
/**
 * Returns file for specific template parts.
 *
 * @package TotalTheme
 * @subpackage Functions
 * @version 5.1.3
 */

defined( 'ABSPATH' ) || exit;

/**
 * Define all theme template parts.
 *
 * @since 3.5.0
 * @todo deprecate or add all template parts
 */
function wpex_template_parts() {
	return apply_filters( 'wpex_template_parts', array(

		// Toggle bar
		'togglebar'                    => 'partials/togglebar/togglebar-layout',
		'togglebar_button'             => 'partials/togglebar/togglebar-button',
		'togglebar_dismiss'            => 'partials/togglebar/togglebar-dismiss',
		'togglebar_content'            => 'partials/togglebar/togglebar-content',

		// Topbar
		'topbar'                       => 'partials/topbar/topbar-layout',
		'topbar_content'               => 'partials/topbar/topbar-content',
		'topbar_social'                => 'partials/topbar/topbar-social',

		// Header
		'header'                       => 'partials/header/header-layout',
		'header_logo'                  => 'partials/header/header-logo',
		'header_logo_inner'            => 'partials/header/header-logo-inner',
		'header_menu'                  => 'partials/header/header-menu',
		'header_aside'                 => 'partials/header/header-aside',
		'header_buttons'               => 'partials/header/header-buttons',
		'header_search_dropdown'       => 'partials/search/header-search-dropdown',
		'header_search_replace'        => 'partials/search/header-search-replace',
		'header_search_overlay'        => 'partials/search/header-search-overlay',
		'header_mobile_menu_fixed_top' => 'partials/header/header-menu-mobile-fixed-top',
		'header_mobile_menu_navbar'    => 'partials/header/header-menu-mobile-navbar',
		'header_mobile_menu_icons'     => 'partials/header/header-menu-mobile-icons',
		'header_mobile_menu_alt'       => 'partials/header/header-menu-mobile-alt',
		'header_mobile_menu_extras'    => 'partials/header/header-menu-mobile-extras',

		// Page header
		'page_header'                  => 'partials/page-header',
		'page_header_content'          => 'partials/page-header/page-header-content',
		'page_header_aside'            => 'partials/page-header/page-header-aside',
		'page_header_title'            => 'partials/page-header-title',
		'page_header_subheading'       => 'partials/page-header-subheading',

		// Archives
		'term_description'             => 'partials/term-description',

		// Single blocks
		'cpt_single_blocks'            => 'partials/cpt/cpt-single',
		'page_single_blocks'           => 'partials/page-single-layout',
		'blog_single_blocks'           => 'partials/blog/blog-single-layout',
		'portfolio_single_blocks'      => 'partials/portfolio/portfolio-single-layout',
		'staff_single_blocks'          => 'partials/staff/staff-single-layout',
		'testimonials_single_blocks'   => 'partials/testimonials/testimonials-single-layout',

		// Blog Entry
		'blog_entry'                   => 'partials/blog/blog-entry-layout',

		// Blog Post
		'blog_single_quote'            => 'partials/blog/blog-single-quote',
		'blog_single_media'            => 'partials/blog/blog-single-media',
		'blog_single_title'            => 'partials/blog/blog-single-title',
		'blog_single_meta'             => 'partials/blog/blog-single-meta',
		'blog_single_content'          => 'partials/blog/blog-single-content',
		'blog_single_tags'             => 'partials/blog/blog-single-tags',
		'blog_single_related'          => 'partials/blog/blog-single-related',

		// Custom Types
		'cpt_entry'                    => 'partials/cpt/cpt-entry',
		'cpt_single_media'             => 'partials/cpt/cpt-single-media',
		'cpt_single_related'           => 'partials/cpt/cpt-single-related',

		// Footer
		'footer_callout'               => 'partials/footer/footer-callout',
		'footer'                       => 'partials/footer/footer-layout',
		'footer_widgets'               => 'partials/footer/footer-widgets',
		'footer_bottom'                => 'partials/footer/footer-bottom',
		'footer_reveal_open'           => 'partials/footer/footer-reveal-open',
		'footer_reveal_close'          => 'partials/footer/footer-reveal-close',

		// Footer Bottom
		'footer_bottom_copyright'      => 'partials/footer/footer-bottom-copyright',
		'footer_bottom_menu'           => 'partials/footer/footer-bottom-menu',

		// Mobile
		'mobile_searchform'            => 'partials/search/mobile-searchform',

		// Other
		'breadcrumbs'                  => 'partials/breadcrumbs',
		'social_share'                 => 'partials/social-share',
		'post_series'                  => 'partials/post-series',
		'scroll_top'                   => 'partials/scroll-top',
		'post_meta'                    => 'partials/meta/meta',
		'next_prev'                    => 'partials/next-prev',
		'post_edit'                    => 'partials/post-edit',
		'post_slider'                  => 'partials/post-slider',
		'author_bio'                   => 'author-bio',
		'link_pages'                   => 'partials/link-pages',
		'search_entry'                 => 'partials/search/search-entry',

	) );

}

/**
 * Get specific template part.
 *
 * @since 3.5.0
 */
function wpex_get_template_part( $slug = '', $name = null ) {
	$parts = wpex_template_parts();
	if ( isset( $parts[$slug] ) ) {
		$slug = $parts[$slug];
	}
	if ( is_callable( $slug ) ) {
		return call_user_func( $slug ); // Allow people to override template part with function output.
	} else {
		get_template_part( $slug, $name );
	}
}
