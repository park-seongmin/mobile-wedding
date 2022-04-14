<?php
/**
 * Custom pagination functions
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
	# Numbered
	# Next/Prev
	# Inifite Scroll
	# Load More

/*-------------------------------------------------------------------------------*/
/* [ General ]
/*-------------------------------------------------------------------------------*/

/**
 * Returns correct loop pagination.
 *
 * @since 4.8
 * @todo update to pass all query_vars so it can be used in any situation.
 */
function wpex_loop_pagination( $loop_type = '', $count = '' ) {

	// Display pagination.
	if ( 'blog' === $loop_type ) {

		global $wp_query;

		$query = $wp_query->query;
		$query['posts_per_page'] = $wp_query->query_vars['posts_per_page']; // pass posts_per_page to the query.

		$pagination_args = array(
			'query'    => $query,
			'is_home'  => is_home(),
			'grid'     => '#blog-entries', // @todo update to target the prev element if this var is empty
			'maxPages' => $wp_query->max_num_pages,
			'category' => is_category() ? get_query_var( 'cat' ) : false,
			'count'    => $count ? $count : wpex_get_loop_counter(),
		);

		if ( is_category() || is_tag() || is_tax() ) {
			$pagination_args['term_id'] = get_queried_object_id(); // added in v5.0
		}

		wpex_blog_pagination( $pagination_args );

	} else {
		wpex_pagination();
	}

}

/**
 * Archive pagination.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'wpex_blog_pagination' ) ) {

	function wpex_blog_pagination( $args = array() ) {

		$pagination_style = get_theme_mod( 'blog_pagination_style', 'standard' );

		if ( is_category() ) {

			$cat_meta = wpex_get_category_meta( get_query_var( 'cat' ), 'wpex_term_pagination' );

			if ( $cat_meta ) {
				$pagination_style = $cat_meta;
			}

		}

		switch ( $pagination_style ) {

			case 'infinite_scroll':

				if ( 'grid-entry-style' === wpex_blog_entry_style() ) {
					$infinite_type = 'standard-grid';
				} else {
					$infinite_type = 'standard';
				}

				wpex_infinite_scroll( $infinite_type );

				break;

			case 'load_more':
				wpex_loadmore( $args );
				break;

			case 'next_prev':
				wpex_archive_next_prev_links();
				break;

			default:
				wpex_pagination();
				break;

		} // end switch

	}

}

/*-------------------------------------------------------------------------------*/
/* [ Numbered ]
/*-------------------------------------------------------------------------------*/

/**
 * Numbered Pagination for archives.
 *
 * @since 4.8
 * @todo replace for blog and main archives
 */
function wpex_get_pagination() {

	// Arrow style
	$arrow_icon = get_theme_mod( 'pagination_arrow' );
	$arrow_icon = $arrow_icon ? esc_attr( $arrow_icon ) : 'angle';

	// Arrows with RTL support
	$prev_arrow = is_rtl() ? 'ticon ticon-' . $arrow_icon . '-right' : 'ticon ticon-' . $arrow_icon . '-left';
	$next_arrow = is_rtl() ? 'ticon ticon-' . $arrow_icon . '-left' : 'ticon ticon-' . $arrow_icon . '-right';

	return get_the_posts_pagination( array(
		'type'               => 'list',
		'prev_text'          => '<span class="' . esc_attr( $prev_arrow ) . '" aria-hidden="true"></span><span class="screen-reader-text">' . esc_html__( 'Previous', 'total' ) . '</span>',
		'next_text'          => '<span class="' . esc_attr( $next_arrow ) . '" aria-hidden="true"></span><span class="screen-reader-text">' . esc_html__( 'Next', 'total' ) . '</span>',
		'before_page_number' => '<span class="screen-reader-text">' . esc_html__( 'Page', 'total' ) . ' </span>',
	) );
}


/**
 * Numbered Pagination.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'wpex_pagination' ) ) { // MUST KEEP CHECK SO USERS CAN OVERRIDE

	function wpex_pagination( $query = '', $echo = true ) {

		// Arrow style.
		$arrow_icon = get_theme_mod( 'pagination_arrow' );
		$arrow_icon = $arrow_icon ? esc_attr( $arrow_icon ) : 'angle';

		// Arrows with RTL support.
		$prev_arrow = is_rtl() ? 'ticon ticon-' . $arrow_icon . '-right' : 'ticon ticon-' . $arrow_icon . '-left';
		$next_arrow = is_rtl() ? 'ticon ticon-' . $arrow_icon . '-left' : 'ticon ticon-' . $arrow_icon . '-right';

		// Get global $query.
		if ( ! $query ) {
			global $wp_query;
			$query = $wp_query;
		}

		// Set vars.
		$total = $query->max_num_pages;
		$big   = 999999999;

		// Display pagination.
		if ( $total > 1 ) {

			// Get current page.
			if ( $current_page = get_query_var( 'paged' ) ) {
				$current_page = $current_page;
			} elseif ( $current_page = get_query_var( 'page' ) ) {
				$current_page = $current_page;
			} else {
				$current_page = 1;
			}

			// Get permalink structure.
			if ( get_option( 'permalink_structure' ) ) {
				if ( is_page() ) {
					$format = 'page/%#%/';
				} else {
					$format = '/%#%/';
				}
			} else {
				$format = '&paged=%#%';
			}

			// Previous text.
			$prev_text = '<span class="' . esc_attr( $prev_arrow ) . '" aria-hidden="true"></span><span class="screen-reader-text">' . esc_html__( 'Previous', 'total' ) . '</span>';

			// Next text.
			$next_text = '<span class="' . esc_attr( $next_arrow ) . '" aria-hidden="true"></span><span class="screen-reader-text">' . esc_html__( 'Next', 'total' ) . '</span>';

			// Define and add filter to pagination args.
			$args = apply_filters( 'wpex_pagination_args', array(
				'base'               => str_replace( $big, '%#%', html_entity_decode( get_pagenum_link( $big ) ) ),
				'format'             => $format,
				'current'            => max( 1, $current_page ),
				'total'              => $total,
				'mid_size'           => 3,
				'type'               => 'list',
				'prev_text'          => $prev_text,
				'next_text'          => $next_text,
				'before_page_number' => '<span class="screen-reader-text">' . esc_html__( 'Page', 'total' ) . ' </span>',
			) );

			// Alignment classes based on Customizer settings.
			$safe_align = ( $align = get_theme_mod( 'pagination_align' ) ) ? ' text' . sanitize_html_class( $align ) : '';

			// Output.
			$output_escaped = '<div class="wpex-pagination wpex-clear wpex-mt-30 wpex-clr' . $safe_align . '">' . paginate_links( $args ) . '</div>';

			// Output pagination.
			if ( $echo ) {
				echo $output_escaped; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
			} else {
				return $output_escaped; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
			}

		}

	}

}

/*-------------------------------------------------------------------------------*/
/* [ Next/Prev ]
/*-------------------------------------------------------------------------------*/

/**
 * Echo Next/Prev Pagination.
 *
 * @since 4.9
 */
function wpex_archive_next_prev_links( $query = '' ) {
	echo wpex_get_archive_next_prev_links( $query );
}

/**
 * Return Next/Prev Pagination
 *
 * @since 4.9
 */
function wpex_get_archive_next_prev_links( $query = '' ) {

	if ( ! $query ) {
		global $wp_query;
		$query = $wp_query;
	}

	if ( $query->max_num_pages > 1 ) {

		$output = '<div class="page-jump wpex-clr">';

			$output .= '<div class="alignleft newer-posts">';

				$output .= get_previous_posts_link( '&larr; ' . esc_html__( 'Newer Posts', 'total' ) );

			$output .= '</div>';

			$output .= '<div class="alignright older-posts">';

				$output .= get_next_posts_link( esc_html__( 'Older Posts', 'total' ) . ' &rarr;' );

			$output .= '</div>';

		$output .= '</div>';

		return $output;
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Infinite Scroll ]
/*-------------------------------------------------------------------------------*/


/**
 * Infinite Scroll Pagination
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'wpex_infinite_scroll' ) ) {

	function wpex_infinite_scroll( $type = 'standard' ) {

		// Make sure possibly needed scripts are loaded - @todo optimize?
		wp_enqueue_script( 'imagesloaded' );
		wpex_enqueue_lightbox_scripts();
		wpex_enqueue_slider_pro_scripts();

		// Load infinite scroll script.
		wp_enqueue_script(
			'wpex-infinite-scroll',
			wpex_asset_url( 'js/dynamic/wpex-infinite-scroll.min.js' ),
			array( 'jquery', 'imagesloaded' ),
			WPEX_THEME_VERSION,
			true
		);

		// Loading text.
		$loading_text = get_theme_mod( 'loadmore_loading_text', esc_html__( 'Loading&hellip;', 'total' ) );

		// Loading img.
		$gif = apply_filters( 'wpex_loadmore_gif', includes_url( 'images/spinner-2x.gif' ) );

		// Localize loading text.
		$is_params = apply_filters( 'wpex_infinite_scroll_args', array(
			'loading' => array(
				'msgText'      => '<div class="wpex-infscr-spinner"><img src="' . esc_url( $gif ) . '" class="wpex-spinner" alt="' . esc_attr( $loading_text ) . '"><span class="ticon ticon-spinner"></span></div>',
				'msg'          => null,
				'finishedMsg'  => null,
			),
			'blankImg'     => esc_url( wpex_asset_url( 'images/blank.gif' ) ),
			'navSelector'  => 'div.infinite-scroll-nav',
			'nextSelector' => 'div.infinite-scroll-nav div.older-posts a',
			'itemSelector' => '.blog-entry',
		), 'blog' );

		wp_localize_script( 'wpex-infinite-scroll', 'wpexInfiniteScroll', $is_params );

		// @todo is this needed?
		if ( get_theme_mod( 'blog_entry_audio_output', false )
			|| apply_filters( 'wpex_infinite_scroll_enqueue_mediaelement', false )
		) {
			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
		}

		?>

		<div class="infinite-scroll-nav wpex-clr">
			<div class="alignleft newer-posts"><?php echo get_previous_posts_link( '&larr; ' . esc_html__( 'Newer Posts', 'total' ) ); ?></div>
			<div class="alignright older-posts"><?php echo get_next_posts_link( esc_html__( 'Older Posts', 'total' ) . ' &rarr;' ); ?></div>
		</div>

	<?php }

}

/*-------------------------------------------------------------------------------*/
/* [ Load More @todo convert into singleton class?]
/*-------------------------------------------------------------------------------*/

/**
 * Enqueues loadmore scripts.
 *
 * @since 5.1
 */
function wpex_enqueue_loadmore_scripts() {

	// jQuery needed (must load first if not already loaded)
	wp_enqueue_script( 'jquery' );

	// Images Loaded needed (must go after jquery!!!)
	wp_enqueue_script( 'imagesloaded' );

	// Make sure possibly needed scripts are loaded - @todo how can we load these as needed?
	wpex_enqueue_slider_pro_scripts();
	wpex_enqueue_lightbox_scripts();

	// WP Media.
	if ( get_theme_mod( 'blog_entry_audio_output', false )
		|| apply_filters( 'wpex_loadmore_enqueue_mediaelement', false )
	) {
		wp_enqueue_style( 'wp-mediaelement' );
		wp_enqueue_script( 'wp-mediaelement' );
	}

	// Theme loadmore script.
	if ( WPEX_MINIFY_JS ) {
		$file = 'wpex-loadmore.min.js';
	} else {
		$file = 'wpex-loadmore.js';
	}

	wp_enqueue_script(
		'wpex-loadmore',
		wpex_asset_url( 'js/dynamic/' . $file ),
		array( 'jquery', 'imagesloaded', WPEX_THEME_JS_HANDLE ),
		WPEX_THEME_VERSION,
		true
	);

	wp_localize_script(
		'wpex-loadmore',
		'wpex_loadmore_params',
		array(
			'ajax_url' => set_url_scheme( admin_url( 'admin-ajax.php' ) ),
			'i18n' => array(
				'text'        => wp_strip_all_tags( wpex_get_loadmore_text() ),
				'loadingText' => wp_strip_all_tags( wpex_get_loadmore_loading_text() ),
				'failedText'  => wp_strip_all_tags( wpex_get_loadmore_failed_text() ),
			),
		)
	);

}

/**
 * Returns load more text.
 *
 * @since 5.0
 */
function wpex_get_loadmore_text() {
	$text = wpex_get_translated_theme_mod( 'loadmore_text' );
	if ( empty( $text ) ) {
		$text = esc_html__( 'Load More', 'total' );
	}
	return apply_filters( 'wpex_loadmore_text', $text );
}

/**
 * Returns load more loading text.
 *
 * @since 5.0
 */
function wpex_get_loadmore_loading_text() {
	$text = wpex_get_translated_theme_mod( 'loadmore_loading_text' );
	if ( empty( $text ) ) {
		$text = esc_html__( 'Loading...', 'total' );
	}
	return apply_filters( 'wpex_loadmore_loading_text', $text );
}

/**
 * Returns load more failed text.
 *
 * @since 5.0
 */
function wpex_get_loadmore_failed_text() {
	$text = wpex_get_translated_theme_mod( 'loadmore_failed_text' );
	if ( empty( $text ) ) {
		$text = esc_html__( 'Failed to load posts.', 'total' );
	}
	return apply_filters( 'wpex_loadmore_failed_text', $text );
}

/**
 * Returns load more gif.
 *
 * @since 5.0
 */
function wpex_get_loadmore_gif() {
	return apply_filters( 'wpex_loadmore_gif', null );
}

/**
 * Ajax load more.
 *
 * @since 4.4.1
 */
function wpex_loadmore( $args = array() ) {

	wpex_enqueue_loadmore_scripts();

	$defaults = array(
		'nonce'    => wp_create_nonce( 'wpex-load-more-nonce' ),
		'query'    => '',
		'maxPages' => '',
	);

	$args = wp_parse_args( $args, $defaults );

	$output = '';

	$output .= '<div class="wpex-load-more-wrap">';

		$button_class = 'wpex-load-more theme-button';

		if ( get_theme_mod( 'loadmore_btn_expanded', true ) ) {
			$button_class .= ' expanded';
		} else {
			$button_class .= ' wpex-px-20';
		}

		$max_num_pages = absint( $args['maxPages'] );
		if ( $max_num_pages && $max_num_pages > 1 ) {
			$button_class .= ' wpex-visible';
		}

		$output .= '<a href="#" class="' . esc_attr( $button_class ) . '" data-loadmore="' . htmlentities( wp_json_encode( $args ) ) . '">';

			$output .= '<span class="theme-button-inner">' . esc_html( wpex_get_loadmore_text() ) . '</span>';

		$output .= '</a>';

		$gif = wpex_get_loadmore_gif();

		if ( $gif ) {
			$output .= '<img src="' . esc_url( $gif ) . '" class="wpex-spinner" alt="' . esc_attr( wpex_get_loadmore_loading_text() ) . '">';
		} else {
			$output .= '<div class="wpex-spinner">' . wpex_get_svg( 'wp-spinner', 20 ) . '</div>';
		}

		$output .= '<span class="ticon ticon-spinner" aria-hidden="true"></span>';

		$output .= '</div>';

	echo $output;

}

/**
 * Ajax load more.
 *
 * @since 4.4.1
 */
function wpex_ajax_load_more() {

	check_ajax_referer( 'wpex-load-more-nonce', 'nonce' );

	if ( empty( $_POST['loadmore'] ) || ! is_array( $_POST['loadmore'] ) ) {
		wp_die();
	}

	$loadmore = $_POST['loadmore'];
	//$loadmore = json_decode( html_entity_decode( stripslashes( $_POST['loadmore'] ) ), true );

	$query_args = isset( $loadmore['query'] ) ? $loadmore['query'] : array();

	if ( ! empty( $query_args['s'] ) ) {
		$post_type = 'post'; // search results when set to blog style
	} else {
		$query_args['post_type'] = ! empty( $query_args['post_type'] ) ? $query_args['post_type'] : 'post';
		$post_type = $query_args['post_type'];
	}

	$query_args['post_status'] = 'publish';
	$query_args['paged'] = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 2;

	// Update counter
	$wpex_count = isset( $loadmore['count'] ) ? $loadmore['count'] : 0;
	wpex_set_loop_counter( $wpex_count );

	if ( ! empty( $loadmore['is_home'] ) && $cats = wpex_blog_exclude_categories() ) {
		$query_args['category__not_in'] = $cats;
	}

	// Store load more data so we can access it later.
	set_query_var( 'wpex_loadmore_data', $loadmore );

	ob_start();

	$loop = new WP_Query( $query_args );

	if ( $loop->have_posts() ) :

		while ( $loop->have_posts() ): $loop->the_post();

			if ( 'post' == $post_type ) {
				get_template_part( 'partials/loop/loop', 'blog' ); // @todo support other post types
			}

		endwhile;

	endif;

	// used to update counter properly @todo deprecate?
	echo '<div class="wpex-hidden" data-count="' . esc_attr( wpex_get_loop_counter() ) . '"></div>';

	wp_reset_postdata();

	set_query_var( 'wpex_loadmore_data', null );

	$data = ob_get_clean();

	wp_send_json_success( $data );

	wp_die();

}
add_action( 'wp_ajax_wpex_ajax_load_more', 'wpex_ajax_load_more' );
add_action( 'wp_ajax_nopriv_wpex_ajax_load_more', 'wpex_ajax_load_more' );

/**
 * Get loadmore data.
 *
 * @since 5.0
 */
function wpex_get_loadmore_data( $key = '' ) {

	if ( empty( $_POST['loadmore'] ) ) {
		return;
	}

	$data = get_query_var( 'wpex_loadmore_data' );

	if ( $key ) {
		return isset( $data[$key] ) ? $data[$key] : '';
	}

	return $data;

}