<?php
/**
 * Category Tag v2.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 5.1.1
 */

defined( 'ABSPATH' ) || exit;

// Only used for outside position
if ( 'outside_link' !== $position ) {
	return;
}

// Get category taxonomy for current post type
$taxonomy = wpex_get_post_type_cat_tax();

// Return if a taxonomy isn't found
if ( ! $taxonomy ) {
	return;
}

// Get post terms
$terms = get_the_terms( get_the_ID(), $taxonomy );

// Return if no terms found
if ( empty( $terms ) || is_wp_error( $terms ) ) {
	return;
}

?>

<div class="overlay-category-tag-two theme-overlay wpex-absolute wpex-top-0 wpex-left-0 wpex-z-10 wpex-mt-15 wpex-ml-15 wpex-uppercase wpex-text-xs wpex-font-semibold wpex-clr">
	<?php
	$count = 0;
	foreach ( $terms as $term ) {
		$count++;
		$link_class = (array) apply_filters( 'wpex_overlay_category_tag_2_link_class', array(
			'term-' . sanitize_html_class( $term->slug ),
			'count-' . sanitize_html_class( $count ),
			'wpex-block',
			'wpex-float-left',
			'wpex-mr-5',
			'wpex-mb-5',
			'wpex-text-black',
			'wpex-bg-white',
			'wpex-round',
			'wpex-py-5',
			'wpex-px-10',
			'wpex-no-underline',
			'wpex-transition-colors',
			'wpex-duration-200',
			'wpex-hover-bg-accent',
			'wpex-hover-text-white',
		) ); ?>
		<a href="<?php echo esc_url( get_term_link( $term->term_id, $taxonomy ) ); ?>" class="<?php echo esc_attr( implode( ' ', $link_class  ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
	<?php } ?>
</div>