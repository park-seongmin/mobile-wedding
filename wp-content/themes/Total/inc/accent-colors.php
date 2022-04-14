<?php
namespace TotalTheme;

defined( 'ABSPATH' ) || exit;

/**
 * Accent Colors.
 *
 * @package TotalTheme
 * @version 5.3.1
 */
final class Accent_Colors {

	/**
	 * Class instance.
	 *
	 * @access private
	 * @var object Class object.
	 */
	private static $instance;

	/**
	 * Create or retrieve the instance of Accent_Colors.
	 */
	public static function instance() {
		if ( is_null( static::$instance ) ) {
			static::$instance = new self();
			static::$instance->init_hooks();
		}

		return static::$instance;
	}

	/**
	 * Hook into actions and filters.
	 */
	public function init_hooks() {
		if ( is_customize_preview() ) {
			add_action( 'wp_head', array( $this, 'customizer_css' ), 10 );
		} else {
			add_filter( 'wpex_head_css', array( $this, 'live_css' ), 1 );
		}
	}

	/**
	 * Color Targets.
	 */
	public static function color_targets() {

		$targets = array(

			// Utility classes.
			'.wpex-text-accent',
			'.wpex-hover-text-accent:hover',
			'.wpex-accent-color', // @todo deprecate

			// Menus.
			'#site-navigation .dropdown-menu > li.menu-item > a:hover',
			'#site-navigation .dropdown-menu > li.menu-item.current-menu-item > a',
			'#site-navigation .dropdown-menu > li.menu-item.current-menu-parent > a',

			// Widgets.
			'.modern-menu-widget a:hover',

			// Theme button.
			'.theme-button.outline',
			'.theme-button.clean',

			// Links.
			'a',
			'h1 a:hover',
			'h2 a:hover',
			'h3 a:hover',
			'h4 a:hover',
			'h5 a:hover',
			'h6 a:hover',
			'.entry-title a:hover',
			'.meta a:hover',
			'.wpex-heading a:hover',

		);

		if ( current_theme_supports( 'gutenberg-editor' ) ) {
			$targets[] = '.is-style-outline .wp-block-button__link:not(.has-color):not(.has-text-color):not(.has-background)';
			$targets[] = '.wp-block-button__link.is-style-outline:not(.has-color):not(.has-text-color):not(.has-background)';
		}

		if ( get_theme_mod( 'extend_visual_composer', true ) ) {
			$targets[] = '.vcex-module a:hover .wpex-heading';
			$targets[] = '.vcex-icon-box-link-wrap:hover .wpex-heading';
		}

		/**
		 * Filters the accent color targets/elements.
		 *
		 * @param array $targets
		 */
		$targets = (array) apply_filters( 'wpex_accent_texts', $targets );

		return $targets;

	}

	/**
	 * Background Targets.
	 */
	public static function background_targets() {

		$targets = array(

			// Utility.
			'.wpex-bg-accent',
			'.wpex-hover-bg-accent:hover',

			// Alt accent.
			'.wpex-bg-accent_alt',
			'.wpex-hover-bg-accent_alt:hover',

			// Badge.
			'.wpex-badge',

			// Legacy utility class.
			'.wpex-accent-bg',

			// Buttons.
			'input[type="submit"]',
			'.theme-button',
			'button',
			'.button',
			'.active > .theme-button',
			'.theme-button.active',

			// Outline buttons.
			'.theme-button.outline:hover',
			'.active > .theme-button.outline',
			'.theme-button.outline.active',
			'.theme-button.outline:hover',

			// Everything else.
			'.post-edit a',
			'.background-highlight', // legacy
			'.tagcloud a:hover',
			'.post-tags a:hover',
			'.wpex-carousel .owl-dot.active',
			'.wpex-carousel .owl-prev',
			'.wpex-carousel .owl-next',
			'body #header-two-search #header-two-search-submit',
			'#site-navigation .menu-button > a > span.link-inner',
			'.modern-menu-widget li.menu-item.current-menu-item a',
			'#sidebar .widget_nav_menu .current-menu-item > a',
			'.widget_nav_menu_accordion .widget_nav_menu li.menu-item.current-menu-item > a',
			'#site-navigation-wrap.has-menu-underline .main-navigation-ul>li>a>.link-inner::after',
			'#wp-calendar caption',
			'#wp-calendar tbody td:hover a',

		);

		if ( current_theme_supports( 'gutenberg-editor' ) ) {
			$targets[] = '.wp-block-search .wp-block-search__button';
			$targets[] = '.wp-block-file a.wp-block-file__button';
			$targets[] = '.is-style-fill .wp-block-button__link:not(.has-background)';
			$targets[] = '.wp-block-button__link.is-style-fill:not(.has-background)';
			$targets[] = '.is-style-outline .wp-block-button__link:not(.has-color):not(.has-text-color):not(.has-background):hover';
			$targets[] = '.wp-block-button__link.is-style-outline:not(.has-color):not(.has-text-color):not(.has-background):hover';
		}

		if ( get_theme_mod( 'extend_visual_composer', true ) ) {
			$targets[] = '.vcex-testimonials-fullslider .sp-button:hover';
			$targets[] = '.vcex-testimonials-fullslider .sp-selected-button';
			$targets[] = '.vcex-testimonials-fullslider.light-skin .sp-button:hover';
			$targets[] = '.vcex-testimonials-fullslider.light-skin .sp-selected-button';
			$targets[] = '.vcex-testimonials-fullslider .sp-button.sp-selected-button';
			$targets[] = '.vcex-testimonials-fullslider .sp-button:hover';
		}

		/**
		 * Filters the accent background targets/elements.
		 *
		 * @param array $targets
		 */
		$targets = (array) apply_filters( 'wpex_accent_backgrounds', $targets );

		return $targets;

	}

	/**
	 * Border Targets.
	 */
	public static function border_targets() {

		$targets = array(
			'.wpex-border-accent',
			'.wpex-hover-border-accent:hover',
			'.wpex-slider .sp-bottom-thumbnails.sp-has-pointer .sp-selected-thumbnail:before,.wpex-slider .sp-bottom-thumbnails.sp-has-pointer .sp-selected-thumbnail:after' => array( 'bottom' ),
			'.wpex-dropdown-top-border #site-navigation .dropdown-menu li.menu-item ul.sub-menu' => array( 'top' ),
			'.theme-heading.border-w-color span.text' => array( 'bottom' ),
		);

		if ( current_theme_supports( 'gutenberg-editor' ) ) {
			$targets[] = '.is-style-outline .wp-block-button__link:not(.has-color):not(.has-text-color):not(.has-background)';
			$targets[] = '.wp-block-button__link.is-style-outline:not(.has-color):not(.has-text-color):not(.has-background)';
		}

		/**
		 * Filters the accent color border targets/elements.
		 *
		 * @param array $targets
		 */
		$targets = (array) apply_filters( 'wpex_accent_borders', $targets );

		return $targets;

	}

	/**
	 * Hover Background targets.
	 */
	public static function hover_background_targets() {

		$targets = array(
			'.wpex-bg-accent_alt',
			'.wpex-hover-bg-accent_alt:hover',

			// Buttons
			'.post-edit a:hover',
			'.theme-button:hover',
			'input[type="submit"]:hover',
			'button:hover',
			'.button:hover',
			'.active > .theme-button',
			'.theme-button.active',

			// Carousel
			'.wpex-carousel .owl-prev:hover',
			'.wpex-carousel .owl-next:hover',

			// Other
			'#site-navigation .menu-button > a > span.link-inner:hover',
		);

		if ( current_theme_supports( 'gutenberg-editor' ) ) {
			$targets[] = '.wp-block-search .wp-block-search__button';
			$targets[] = '.wp-block-file a.wp-block-file__button';
		}

		/**
		 * Filters the accent color hover background targets/elements.
		 *
		 * @param array $targets
		 */
		$targets = (array) apply_filters( 'wpex_accent_hover_backgrounds', $targets );

		return $targets;

	}

	/**
	 * Hover Text targets.
	 */
	public static function hover_text_targets() {

		$targets = array(
			'.wpex-text-accent_alt',
			'.wpex-hover-text-accent_alt:hover',
		);

		/**
		 * Filters the accent color hover text targets/elements.
		 *
		 * @param array $targets
		 */
		$targets = (array) apply_filters( 'wpex_accent_hover_texts', $targets );

		return $targets;

	}

	/**
	 * All Targets.
	 *
	 * note: Used by Customizer class.
	 */
	public static function all_targets() {
		return array(
			// Main accent.
			'texts'            => self::color_targets(),
			'backgrounds'      => self::background_targets(),
			'borders'          => self::border_targets(),
			// Alt accent.
			'backgroundsHover' => self::hover_background_targets(),
			'textsHover'       => self::hover_text_targets(),
		);
	}

	/**
	 * Generates the Accent css.
	 */
	public function accent_css() {

		$accent = wpex_get_custom_accent_color();
		$accent_escaped = esc_attr( $accent );

		if ( ! $accent_escaped ) {
			return;
		}

		$css = '';

		// Texts.
		$color_targets = self::color_targets();
		if ( ! empty( $color_targets ) ) {
			$color_targets_escaped = array_map( 'wp_strip_all_tags', $color_targets );
			$css .= implode( ',', $color_targets_escaped ) . '{color:' . $accent_escaped . ';}';
		}

		// Backgrounds.
		$bg_targets = self::background_targets();
		if ( ! empty( $bg_targets ) ) {
			$bg_targets_escaped = array_map( 'wp_strip_all_tags', $bg_targets );
			$css .= implode( ',', $bg_targets_escaped ) . '{background-color:' . $accent_escaped . ';}';
		}

		// Borders.
		$border_targets = self::border_targets();
		if ( ! empty( $border_targets ) ) {
			foreach ( $border_targets as $key => $val ) {
				if ( is_array( $val ) ) {
					$css .= $key . '{';
					foreach ( $val as $key => $val ) {
						$property_escaped = 'border-' . wp_strip_all_tags( trim( $val ) ) . '-color';
						$css .= $property_escaped . ':' . $accent_escaped . ';';
					}
					$css .= '}';
				} else {
					$css .= wp_strip_all_tags( trim( $val ) ) . '{border-color:' . $accent_escaped . ';}';
				}
			}
		}

		// Return CSS.
		if ( $css ) {
			return $css;
		}

	}

	/**
	 * Generates the Accent hover css.
	 */
	public function accent_hover_css() {

		$accent = wpex_get_custom_accent_color_hover();
		$accent_escaped = esc_attr( $accent );

		if ( ! $accent_escaped ) {
			return;
		}

		$css = '';

		$hover_background_targets = self::hover_background_targets();

		if ( ! empty( $hover_background_targets ) ) {
			$hover_background_targets_escaped = array_map( 'wp_strip_all_tags', $hover_background_targets );
			$css .= implode( ',', $hover_background_targets_escaped ) . '{background-color:' . $accent_escaped . ';}';
		}

		$hover_text_targets = self::hover_text_targets();

		if ( ! empty( $hover_text_targets ) ) {
			$hover_text_targets_escaped = array_map( 'wp_strip_all_tags', $hover_text_targets );
			$css .= implode( ',', $hover_text_targets_escaped ) . '{color:' . $accent_escaped . ';}';
		}

		return $css;

	}

	/**
	 * Customizer Output.
	 */
	public function customizer_css() {
		echo '<style id="wpex-accent-css">' . $this->accent_css() . '</style>';
		echo '<style id="wpex-accent-hover-css">' . $this->accent_hover_css() . '</style>';
	}

	/**
	 * Live site output.
	 */
	public function live_css( $output ) {
		$accent_css = $this->accent_css();
		if ( $accent_css ) {
			$output .= '/*ACCENT COLOR*/' . $accent_css;
		}
		$accent_hover_css = $this->accent_hover_css();
		if ( $accent_hover_css ) {
			$output .= '/*ACCENT HOVER COLOR*/' . $accent_hover_css;
		}
		return $output;
	}

}