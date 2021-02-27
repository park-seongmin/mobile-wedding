<?php
/**
 * Search entry header
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.0
 */

defined( 'ABSPATH' ) || exit;

?>

<header <?php wpex_search_entry_header_class(); ?>>
	<h2 <?php wpex_search_entry_title_class(); ?>><a href="<?php wpex_permalink(); ?>"><?php the_title(); ?></a></h2>
</header>