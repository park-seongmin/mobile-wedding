<?php
/**
 * Main Loop : Product
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'woocommerce_get_template_part' ) ) {
	get_template_part( 'partials/loop' );
}

woocommerce_get_template_part( 'content', 'product' );