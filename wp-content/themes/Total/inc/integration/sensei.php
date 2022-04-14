<?php
namespace TotalTheme\Integration;

defined( 'ABSPATH' ) || exit;

/**
 * Sensei Integration.
 *
 * @package TotalTheme
 * @subpackage Integration
 * @version 5.2
 */
final class Sensei {

	/**
	 * Instance.
	 *
	 * @access private
	 * @var object Class object.
	 */
	private static $instance;

	/**
	 * Create or retrieve the instance of Sensei.
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

		// Add theme support.
		add_action( 'after_setup_theme', array( $this, 'declare_support' ) );

		// Load custom CSS file for tweaks.
		add_action( 'wp_enqueue_scripts', array( $this, 'load_custom_stylesheet' ), 10 );

		// Declare Sensei Layouts.
		add_filter( 'wpex_post_layout_class', array( $this, 'layouts' ), 10 );

		// Add custom sidebar.
		add_filter( 'wpex_register_sidebars_array', array( $this, 'register_sensei_sidebar' ), 10 );
		add_filter( 'wpex_get_sidebar', array( $this, 'display_sensei_sidebar' ), 10 );

		// Add correct theme wrappers.
		add_action( 'sensei_before_main_content', array( $this, 'before_main_content' ), 10 );
		add_action( 'sensei_after_main_content', array( $this, 'after_main_content' ), 10 );

		// Alter page header.
		add_filter( 'wpex_title', array( $this, 'alter_title' ) );

		// Alter breadcrumbs.
		add_filter( 'wpex_breadcrumbs_trail', array( $this, 'breadcrumbs_trail' ) );

		// Declare accent backgrounds.
		add_filter( 'wpex_accent_backgrounds', array( $this, 'accent_backgrounds' ) );

		// Set module term description above the loop.
		add_filter( 'wpex_has_term_description_above_loop', array( $this, 'has_term_description_above_loop' ) );

		// Add title above module description.
		add_action( 'wpex_hook_content_top', array( $this, 'above_content_module_title' ), 10 );

		// Get global Sensei class.
		global $woothemes_sensei;

		// Hook into the global $woothemes_sensei object to tweak things.
		if ( ! empty( $woothemes_sensei ) && is_object( $woothemes_sensei ) ) {

			// Remove duplicate pagination.
			remove_action( 'sensei_pagination', array( $woothemes_sensei->frontend, 'sensei_output_content_pagination' ), 10 );

			// Remove default wrappers.
			remove_action( 'sensei_before_main_content', array( $woothemes_sensei->frontend, 'sensei_output_content_wrapper' ), 10 );
			remove_action( 'sensei_after_main_content', array( $woothemes_sensei->frontend, 'sensei_output_content_wrapper_end' ), 10 );

		}

	}

	/**
	 * Declare theme support.
	 *
	 * @since 3.0.8
	 */
	public static function declare_support() {
		add_theme_support( 'sensei' );
	}

	/**
	 * Load custom CSS file for tweaks only when needed.
	 *
	 * @since 3.0.8
	 */
	public static function load_custom_stylesheet() {
		if ( is_sensei() || is_tax( 'module' ) ) {
			wp_enqueue_style( 'wpex-sensei', wpex_asset_url( 'css/wpex-sensei.css' ), array(), WPEX_THEME_VERSION );
		}
	}

	/**
	 * Declare layout.
	 *
	 * @since 3.0.8
	 */
	public static function layouts( $layout ) {
		if ( is_singular( 'course' ) || is_singular( 'lessen' ) ) {
			$layout = wpex_get_default_content_area_layout();
		}
		return $layout;
	}

	/**
	 * Add custom sidebar.
	 *
	 * @since 3.0.8
	 */
	public static function register_sensei_sidebar( $sidebars ) {
		$sidebars['sensei_sidebar'] = esc_html__( 'Sensei Sidebar', 'total' );
		return $sidebars;
	}

	/**
	 * Alter main sidebar to display sensei sidebar.
	 *
	 * @since 3.0.8
	 */
	public static function display_sensei_sidebar( $sidebar ) {
		if ( is_sensei() ) {
			$sidebar = 'sensei_sidebar';
		}
		return $sidebar;
	}

	/**
	 * Before main content wrapper.
	 *
	 * @since 3.0.8
	 */
	public static function before_main_content() {

		ob_start(); ?>

		<div id="content-wrap" class="container wpex-clr">

			<?php wpex_hook_primary_before(); ?>

			<div id="primary" class="content-area wpex-clr">

				<?php wpex_hook_content_before(); ?>

				<div id="content" class="site-content wpex-clr">

					<?php wpex_hook_content_top(); ?>

		<?php
		echo ob_get_clean();
	}

	/**
	 * After main content wrapper.
	 *
	 * @since 3.0.8
	 */
	public static function after_main_content() {

		ob_start(); ?>

					<?php wpex_hook_content_bottom(); ?>

				</div>

				<?php wpex_hook_content_after(); ?>

			</div>

			<?php wpex_hook_primary_after(); ?>

		</div>

		<?php
		echo ob_get_clean();
	}

	/**
	 * Alter main page header title.
	 *
	 * @since 3.0.8
	 */
	public static function alter_title( $title ) {

		// Single lesson.
		if ( is_singular( 'lesson' ) ) {
			$obj = get_post_type_object( 'lesson' );
			return $obj->labels->name;
		}

		// Single course.
		elseif ( is_singular( 'course' ) ) {
			$obj = get_post_type_object( 'course' );
			return $obj->labels->name;
		}

		// Single Quiz.
		elseif ( is_singular( 'quiz' ) ) {
			$obj = get_post_type_object( 'quiz' );
			return $obj->labels->name;
		}

		// Module tax.
		elseif ( is_tax( 'module' ) ) {
			global $wp_query;
			$term = $wp_query->get_queried_object();
			$tax = get_taxonomy( $term->taxonomy );
			return $tax->labels->name;
		}

		// Course Results - MUST BE LAST.
		else {
			global $wp_query;
			if ( isset( $wp_query->query_vars['course_results'] ) ) {
				$title = esc_html__( 'Course Results', 'total' );
			}
		}

		// Return title.
		return $title;

	}

	/**
	 * Alter breadcrumbs trail.
	 *
	 * @since 3.0.8
	 * @todo check and make sure it's using latest breadcrumb helper functions.
	 */
	public static function breadcrumbs_trail( $trail ) {

		// Add course to single lesson and remove post type archive.
		if ( is_singular( 'lesson' ) ) {

			unset( $trail['post_type_archive'] );

			$offset = 1;
			$og_trail = $trail;
			$courses_obj = get_post_type_object( 'course' );
			$courses = '<a href="' . esc_url( get_post_type_archive_link( 'course' ) ) . '" itemprop="url"><span itemprop="title">' . esc_html( $courses_obj->labels->name ) . '</span></a>';
			$lessons_obj = get_post_type_object( 'lesson' );
			$lessons = '<a href="' . esc_url( get_post_type_archive_link( 'lesson' ) ) . '" itemprop="url"><span itemprop="title">' . esc_html( $lessons_obj->labels->name ) . '</span></a>';
			$course_id = intval( get_post_meta( get_the_ID(), '_lesson_course', true ) );
			$course = '<a href="' . esc_url( get_permalink( $course_id ) ) . '" itemprop="url"><span itemprop="title">' . esc_html( get_the_title( $course_id ) ) . '</span></a>';
			$trail = array_slice( $og_trail, 0, $offset, true ) + array(
				'courses_archive' => $courses,
				'lessons_archive' => $lessons,
				'lesson_course' => $course,
			) + array_slice( $og_trail, $offset, NULL, true);

		}

		// Add course to Module.
		elseif ( is_tax( 'module' ) ) {
			if ( ! empty( $_GET['course_id'] ) ) {
				$course_id = absint( $_GET['course_id'] );
				$offset = 1;
				$og_trail = $trail;
				$courses_obj = get_post_type_object( 'course' );
				$courses = '<a href="' . esc_url( get_post_type_archive_link( 'course' ) ) . '" itemprop="url"><span itemprop="title">' . esc_html( $courses_obj->labels->name ) . '</span></a>';
				$lesson = '<a href="' . esc_url( get_permalink( $course_id ) ) . '" itemprop="url"><span itemprop="title">' . esc_html( get_the_title( $course_id ) ) . '</span></a>';
				$trail = array_slice( $og_trail, 0, $offset, true ) + array(
					'post_type_archive' => $courses,
					'module_course' => $lesson
				) + array_slice( $og_trail, $offset, NULL, true);
			}
		}

		// Course Results.
		else {
			global $wp_query;
			if ( isset( $wp_query->query_vars['course_results'] ) ) {

				// Add link to course.
				$course = get_page_by_path( $wp_query->query_vars['course_results'], OBJECT, 'course' );
				$course_id = $course->ID;
				$trail['lesson_course'] = '<a href="' . esc_url( get_permalink( $course_id ) ) . '" itemprop="url"><span itemprop="title">' . esc_html( get_the_title( $course_id ) ) . '</span></a>';

				// And trail end.
				$trail['trail_end'] = esc_html__( 'Course Results', 'total' );

			}
		}

		// Return trail.
		return $trail;

	}

	/**
	 * Set module term description above loop.
	 *
	 * @since 3.0.8
	 */
	public static function has_term_description_above_loop( $bool ) {
		if ( is_tax( 'module' ) ) {
			$bool = true;
		}
		return $bool;
	}

	/**
	 * Add title above module term description.
	 *
	 * @since 3.0.8
	 */
	public static function above_content_module_title( $bool ) {
		if ( is_tax( 'module' ) ) {
			echo '<h1>'. single_term_title( '', false ) .'</h1>';
		}
	}

	/**
	 * Adds background accents for Sensei.
	 *
	 * @since 3.0.8
	 */
	public static function accent_backgrounds( $backgrounds ) {
		return array_merge( array(
			'a.view-results',
			'a.view-results-link',
			'a.sensei-certificate-link',
			'.module header h2 a',
			'.course-container a.button',
			'.course-container a.button:visited',
			'.course-container a.comment-reply-link',
			'.course-container #commentform #submit',
			'.course-container .submit',
			'.course-container input[type=submit]',
			'.course-container input.button',
			'.course-container button.button',
			'.course a.button',
			'.course a.button:visited',
			'.course a.comment-reply-link',
			'.course #commentform #submit',
			'.course .submit',
			'.course input[type=submit]',
			'.course input.button',
			'.course button.button',
			'.lesson a.button',
			'.lesson a.button:visited',
			'.lesson a.comment-reply-link',
			'.lesson #commentform #submit',
			'.lesson .submit',
			'.lesson input[type=submit]',
			'.lesson input.button',
			'.lesson button.button',
			'.quiz a.button',
			'.quiz a.button:visited',
			'.quiz a.comment-reply-link',
			'.quiz #commentform #submit',
			'.quiz .submit',
			'.quiz input[type=submit]',
			'.quiz input.button',
			'.quiz button.button',
		), $backgrounds );
	}

}