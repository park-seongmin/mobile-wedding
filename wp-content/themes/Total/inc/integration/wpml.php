<?php
namespace TotalTheme\Integration;

defined( 'ABSPATH' ) || exit;

/**
 * WPML Integration.
 *
 * @package TotalTheme
 * @subpackage Integration
 * @version 5.2
 */
final class WPML {

	/**
	 * Instance.
	 *
	 * @access private
	 * @var object Class object.
	 */
	private static $instance;

	/**
	 * Create or retrieve the instance of WPML.
	 */
	public static function instance() {
		if ( is_null( static::$instance ) ) {
			static::$instance = new self();
		}

		return static::$instance;
	}

	/**
	 * Hook into actions and filters.
	 */
	public function __construct() {

		add_filter( 'upload_dir', array( $this, 'upload_dir' ) );

		if ( wpex_is_request( 'admin' ) ) {
			add_action( 'admin_init', array( $this, 'register_strings' ) );
			add_filter( 'wpex_shortcodes_tinymce_json', array( $this, 'tinymce_shortcode' ) );
		}

		if ( wpex_is_request( 'frontend' ) ) {
			add_filter( 'body_class', array( $this, 'body_class' ) );
		}

	}

	/**
	 * Registers theme_mod strings into WPML.
	 *
	 * @since 4.6.5
	 */
	public function register_strings() {
		if ( function_exists( 'icl_register_string' ) && $strings = wpex_register_theme_mod_strings() ) {
			foreach( $strings as $string => $default ) {
				icl_register_string( 'Theme Settings', $string, get_theme_mod( $string, $default ) );
			}
		}
	}

	/**
	 * Adds wpml-language-{lang} class to the body tag.
	 *
	 * @since 3.0.0
	 * @param array $classes
	 */
	public function body_class( $classes ) {
		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$classes[] = 'wpml-language-' . sanitize_html_class( ICL_LANGUAGE_CODE );
		}
		return $classes;
	}

	/**
	 * Fix for when users have the Language URL Option on "different domains"
	 * which causes cropped images to fail.
	 *
	 * @since 4.6.5
	 */
	public function upload_dir( $upload ) {

		// Check if WPML language_negociation type
		$language_negociation = apply_filters( 'wpml_setting', false, 'language_negotiation_type' );
		if ( $language_negociation !== false && $language_negociation == 2 ) {
			$upload['baseurl'] = apply_filters( 'wpml_permalink', $upload['baseurl'] );
		}

		// Return $upload var
		return $upload;

	}

	/**
	 * Add shortcodes to the tiny MCE.
	 *
	 * @since 4.6.5
	 */
	public function tinymce_shortcode( $data ) {
		if ( shortcode_exists( 'wpml_translate' ) ) {
			$data['shortcodes']['wpml_lang_selector'] = array(
				'text'   => esc_html__( 'WPML Switcher', 'total' ),
				'insert' => '[wpml_lang_selector]',
			);
		}
		return $data;
	}

}