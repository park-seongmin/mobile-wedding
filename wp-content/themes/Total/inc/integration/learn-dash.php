<?php
namespace TotalTheme\Integration;

defined( 'ABSPATH' ) || exit;

/**
 * Learn Dash Integration.
 *
 * @package TotalTheme
 * @subpackage Integration/Learn_Dash
 * @version 5.2
 */
final class Learn_Dash {

	/**
	 * Instance.
	 *
	 * @access private
	 * @var object Class object.
	 */
	private static $instance;

	/**
	 * Create or retrieve the instance of Learn_Dash.
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
		add_filter( 'wpex_customizer_panels', array( $this, 'customizer_settings' ) );
		add_filter( 'wpex_post_layout_class', array( $this, 'layouts' ) );
		add_filter( 'wpex_main_metaboxes_post_types', array( $this, 'page_settings_meta' ) );
		add_filter( 'wpex_has_breadcrumbs', array( $this, 'wpex_has_breadcrumbs' ) );
	}

	/**
	 * Adds Customizer settings.
	 */
	public function customizer_settings( $panels ) {
		$branding = ( $branding = wpex_get_theme_branding() ) ? ' (' . $branding . ')' : '';
		$panels['learndash'] = array(
			'title'    => 'Learn Dash' . $branding,
			'settings' => WPEX_INC_DIR . 'integration/learn-dash/customizer-settings.php'
		);
		return $panels;
	}

	/**
	 * Alter default layout.
	 */
	public function layouts( $layout ) {
		$types = $this->get_learndash_types();

		foreach( $types as $type ) {

			// Archives.
			if ( is_post_type_archive( $type ) ) {
				return get_theme_mod( $type . '_archives_layout', get_theme_mod( 'learndash_layout' ) );
			}

			// Single posts.
			if ( is_singular( $type ) ) {
				return get_theme_mod( $type . '_single_layout', get_theme_mod( 'learndash_layout' ) );
			}

		}

		// Return layout.
		return $layout;

	}

	/**
	 * Add post types to array of post types to use with Total page settings metabox.
	 */
	public function page_settings_meta( $types ) {
		if ( get_theme_mod( 'learndash_wpex_metabox', true ) ) {
			$types = array_merge( $types, $this->get_learndash_types() );
		}
		return $types;
	}

	/**
	 * Disable breadcrumbs.
	 */
	public function wpex_has_breadcrumbs( $bool ) {

		$types = $this->get_learndash_types();

		foreach( $types as $type ) {

			if ( is_post_type_archive( $type ) || is_singular( $type ) ) {
				$bool = get_theme_mod( 'learndash_breadcrumbs', true );
			}

		}

		return $bool;
	}

	/**
	 * Return array of learndash post types.
	 */
	public function get_learndash_types() {

		if ( function_exists( 'learndash_get_post_types' ) ) {
			return learndash_get_post_types();
		}

		return array(
			'sfwd-courses',
			'sfwd-lessons',
			'sfwd-topic',
			'sfwd-quiz',
			'sfwd-question',
			'sfwd-certificates',
			'sfwd-assignment',
			'sfwd-groups',
		);

	}

}