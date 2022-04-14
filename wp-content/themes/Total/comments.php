 <?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments and the comment
 * form. The actual display of comments is handled by a callback to
 * wpex_comment() which is located at functions/comments-callback.php
 *
 * @package TotalTheme
 * @subpackage Templates
 * @version 5.3.1
 */

defined( 'ABSPATH' ) || exit;

// Return if password is required.
if ( post_password_required() ) {
	return;
}

// Return if comments are disabled and there aren't any comments.
if ( ! comments_open() && get_comments_number() < 1 ) {
	return;
}

// Add classes to the comments main wrapper.
$classes = 'comments-area wpex-mb-40 wpex-clr';

// Add container for full screen layout.
if ( 'full-screen' === wpex_content_area_layout() ) {
	$classes .= ' container';
}

wpex_hook_comments_before();

?>

<section id="comments" class="<?php echo esc_attr( $classes ); ?>">

	<?php
	// Get comments title.
	$comments_number = number_format_i18n( get_comments_number() );
	if ( '1' == $comments_number ) {
		$comments_title = esc_html__( 'This Post Has One Comment', 'total' );
	} else {
		$comments_title = sprintf( esc_html__( 'This Post Has %s Comments', 'total' ), $comments_number );
	}
	$comments_title = apply_filters( 'wpex_comments_title', $comments_title );

	// Display comments heading.
	wpex_heading( array(
		'tag'           => get_theme_mod( 'comments_heading_tag' ) ?: 'h3',
		'content'		=> $comments_title,
		'classes'		=> array( 'comments-title' ) ,
		'apply_filters'	=> 'comments',
	) );
	?>

	<?php wpex_hook_comments_top(); ?>

	<?php if ( have_comments() ) : ?>

		<ol class="comment-list"><?php

			// Display comments.
			wp_list_comments( array(
				'style'       => 'ol',
				'avatar_size' => 50,
				'format'      => 'html5',
			) );

		?></ol>

		<?php
		// Comments next/prev pagination.
		the_comments_navigation(); ?>

		<?php if ( ! comments_open() && get_comments_number() ) : ?>

			<p class="no-comments"><?php esc_html_e( 'Comments are closed.' , 'total' ); ?></p>

		<?php endif; ?>

	<?php endif; ?>

	<?php
	// Display the comment form.
	comment_form(); ?>

	<?php wpex_hook_comments_bottom(); ?>

</section>

<?php wpex_hook_comments_after(); ?>