<?php
/**
 * Returns the page header title.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.1.3
 */

defined( 'ABSPATH' ) || exit;

$args = wpex_page_header_title_args();

if ( empty( $args['string'] ) ) {
	return;
}

$schema_escaped = ! empty( $args['schema_markup'] ) ? $args['schema_markup'] : ''; // already escaped via wpex_get_schema_markup() at /inc/functions/frontend/schema-markup.php

?>

<?php wpex_hook_page_header_title_before(); ?>

<<?php wpex_page_header_title_tag( $args ); ?> <?php wpex_page_header_title_class(); ?><?php echo $schema_escaped; ?>>

	<span><?php echo do_shortcode( wp_kses_post( $args['string'] ) ); ?></span>

</<?php wpex_page_header_title_tag( $args ); ?>>

<?php wpex_hook_page_header_title_after(); ?>