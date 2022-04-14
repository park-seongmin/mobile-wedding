<?php
/**
 * Custom excerpt functions.
 *
 * @package TotalTheme
 * @subpackage Functions
 * @version 5.3.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get or generate excerpts.
 *
 * @since 1.0.0
 */
function wpex_excerpt( $args ) {
	echo wpex_get_excerpt( $args );
}

/**
 * Get or generate excerpts.
 *
 * @since 2.0.0
 */
function wpex_get_excerpt( $args = array() ) {
	if ( ! is_array( $args ) ) {
		$args = array(
			'length' => $args,
		);
	}

	$defaults = array(
		'post_id'              => '',
		'output'               => '',
		'length'               => '30',
		'before'               => '',
		'after'                => '',
		'trim_type'            => 'words',
		'readmore'             => false,
		'readmore_link'        => '',
		'more'                 => '&hellip;',
		'custom_output'        => null,
		'context'              => '', // @todo deprecate context
		'custom_excerpts'      => true,
		'trim_custom_excerpts' => false,
		'custom_excerpts_more' => false,
		'post_password_check'  => true,
	);

	// Parse arguments.
	$args = wp_parse_args( $args, $defaults );

	/**
	 * Filters the default excerpt arguments.
	 *
	 * @param array $args
	 * @param string $context
	 */
	$args = apply_filters( 'wpex_excerpt_args', $args, $args['context'] );

	// Extract args.
	extract( $args );

	// Return custom output if defined as an argument
	// @todo Should we add a filter around the custom output to allow us to bail early?
	if ( $custom_output ) {
		return $custom_output;
	}

	// Sanitize data.
	$excerpt = intval( $length );

	// If length is empty or zero return.
	if ( empty( $length ) || '0' === $length || false == $length ) {
		return;
	}

	// Get global post.
	$post = get_post( $post_id );

	// Display password protected notice.
	if ( $args['post_password_check'] && $post->post_password ) {
		return '<p>' . esc_html__( 'This is a password protected post.', 'total' ) . '</p>';
	}

	// Get post data.
	$post_id = $post->ID;
	$post_content = $post->post_content;

	// Return the content including more tag
	// Note: We can NOT display the full post if we are also viewing the post or we will get a memory error.
	if ( '9999' == $length && $post_id != wpex_get_current_post_id() ) {

		/**
		 * Apply the core content filters.
		 *
		 * @param string $content
		 */
		return apply_filters( 'the_content', get_the_content( '', '&hellip;' ) );

	}

	// Return the content excluding more tag.
	if ( '-1' == $length ) {

		/**
		 * Apply the core content filters.
		 *
		 * @param string $content
		 */
		return apply_filters( 'the_content', $post_content );

	}

	// Custom Excerpts.
	if ( $custom_excerpts && ! empty( $post->post_excerpt ) ) {

		/**
		 * Apply the core excerpt filter.
		 *
		 * @param string $content
		 * @param object $post
		 */
		$post_excerpt = apply_filters( 'get_the_excerpt', $post->post_excerpt, $post );

		if ( $post_excerpt ) {
			if ( $trim_custom_excerpts && '-1' != $length ) {
				$get_excerpt_length = str_word_count( wp_strip_all_tags( $post_excerpt ) );
				if ( $get_excerpt_length > $length ) {
					$post_excerpt = wp_trim_words( $post_excerpt, absint( $length ) );
				}
			}

			// Get output.
			$output = do_shortcode( $post_excerpt );

			// Mainly for testimonials slider.
			if ( $custom_excerpts_more ) {
				$output .= $args['more'];
			}

		}

	// Create Excerpt.
	} else {

		// Check for text shortcode in post.
		if ( false !== strpos( $post_content, '[vc_column_text' ) ) {
			//$pattern = '/\[vc_column_text[^\]]*](.*)\[\/vc_column_text[^\]]*]/uis';
			$pattern = '{\[vc_column_text.*?\](.*?)\[/vc_column_text\]}is';
			preg_match( $pattern, $post_content, $matches );
			if ( isset( $matches[1] ) ) {
				$content = strip_shortcodes( $matches[1] );
			} else {
				$content = strip_shortcodes( $post_content );
			}
		}

		// No text shortcode so lets strip out shortcodes and return the content.
		else {
			$content = strip_shortcodes( $post_content );
		}

		// Trim the content we found.
		if ( 'words' === $trim_type ) {
			$excerpt = wp_trim_words( $content, absint( $length ), $more );
		} else {
			if ( function_exists( 'mb_strimwidth' ) ) {
				$content = wp_strip_all_tags( $content );
				$content = trim( preg_replace( "/[\n\r\t ]+/", ' ', $content ), ' ' );
				$excerpt = mb_strimwidth( $content, 0, $length, $more );
			} else {
				$excerpt = wp_trim_words( $content, absint( $length ), $more );
			}
		}

		// Add excerpt to output.
		if ( $excerpt ) {
			$output .= '<p>'. trim( $excerpt ) .'</p>'; // Already sanitized
		}

	}

	// Add readmore link to output if enabled.
	if ( $readmore ) {
		$read_more_text = $args['read_more_text'] ?? esc_html__( 'Read more', 'total' );

		$more_link = '<a href="' . esc_url( get_permalink( $post_id ) ) . '" class="wpex-readmore theme-button">' . esc_html( $read_more_text ) . '</a>';
		/**
		 * Filters the wpex_excerpt more link.
		 *
		 * @param string $text
		 */
		$read_more_text = apply_filters( 'wpex_excerpt_more_link', $more_link );

		if ( $more_link ) {
			$output .= $more_link;
		}
	}

	/**
	 * Filter the final excerpt output.
	 *
	 * @param string $output
	 * @param array $args
	 */
	$output = apply_filters( 'wpex_excerpt_output', $output, $args );

	if ( $output ) {
		return $before . $output . $after;
	}
}

/**
 * Custom excerpt length for standard posts.
 *
 * @since 1.0.0
 */
function wpex_excerpt_length() {
	$length = get_theme_mod( 'blog_excerpt_length', 40 );

	// Custom category length.
	if ( is_category() ) {
		$term = get_query_var( 'cat' );
		$term_data = get_option( "category_$term" );
		if ( ! empty( $term_data['wpex_term_excerpt_length'] ) ) {
			$length = $term_data['wpex_term_excerpt_length'];
		}
	}

	/**
	 * Filters the excerpt length value.
	 *
	 * @param int $length
	 */
	$length = (int) apply_filters( 'wpex_excerpt_length', $length );

	return $length;
}

/**
 * Change default read more style.
 *
 * @since 1.0.0
 */
function wpex_excerpt_more( $more ) {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'wpex_excerpt_more', 10 );

/**
 * Prevent Page Scroll When Clicking the More Link.
 *
 * @link http://codex.wordpress.org/Customizing_the_Read_More
 * @since 1.0.0
 */
function wpex_remove_more_link_scroll( $link ) {
	$link = preg_replace( '|#more-[0-9]+|', '', $link );
	return $link;
}
add_filter( 'the_content_more_link', 'wpex_remove_more_link_scroll' );