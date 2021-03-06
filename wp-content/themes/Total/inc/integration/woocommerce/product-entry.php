<?php
namespace TotalTheme\Integration\WooCommerce;

defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce Product Entry Tweaks.
 *
 * @package TotalTheme
 * @subpackage Integration/WooCommerce
 * @version 5.3
 */
final class Product_Entry {

	/**
	 * Instance.
	 *
	 * @access private
	 * @var object Class object.
	 */
	private static $instance;

	/**
	 * Create or retrieve the instance of Product_Entry.
	 */
	public static function instance() {
		if ( is_null( static::$instance ) ) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Add HTML to product entries
		// Note link opens on 10 and closes on 5.
		if ( apply_filters( 'wpex_woocommerce_has_shop_loop_item_inner_div', true ) ) {
			add_action( 'woocommerce_before_shop_loop_item', array( $this, 'add_shop_loop_item_inner_div' ), 0 );
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'close_shop_loop_item_inner_div' ), 99 );
		}

		// Add wrapper around product entry details to align buttons.
		if ( apply_filters( 'wpex_woocommerce_has_product_entry_details_wrap', true ) ) {
			add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'loop_details_open' ), 99 );
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'loop_details_close' ), 4 );
		}

		// Add out of stock badge.
		if ( apply_filters( 'wpex_woocommerce_out_of_stock_badge', true ) ) {
			add_action( 'woocommerce_before_shop_loop_item', array( $this, 'add_shop_loop_item_out_of_stock_badge' ) );
		}

		// Remove loop product thumbnail function and add our own that pulls from template parts.
		// @todo add setting to disable this (make sure associated customizater settings are removed as well).
		if ( apply_filters( 'wpex_woocommerce_template_loop_product_thumbnail', true ) ) {

			// Tweak link open/close.
			remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

			// Add link around media.
			add_action( 'wpex_woocommerce_loop_thumbnail_before', 'woocommerce_template_loop_product_link_open', 0 );
			add_action( 'wpex_woocommerce_loop_thumbnail_after', 'woocommerce_template_loop_product_link_close', 11 );

			// Display custom thumbnail media.
			remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
			add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'loop_product_thumbnail' ), 10 );

			if ( get_theme_mod( 'woo_default_entry_buttons', false ) ) {
				// Add element around add to cart button.
				add_filter( 'woocommerce_loop_add_to_cart_link', __CLASS__ . '::add_to_cart_link_wrapper', 9999 );
			} else {
				// Add custom cart icons into thumbnail wrap.
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
				add_action( 'wpex_woocommerce_loop_thumbnail_after', array( $this, 'loop_add_to_cart' ), 40 );
			}

		}

	}

	/**
	 * Adds an opening div "product-inner" around product entries.
	 *
	 * @since 4.4
	 */
	public function add_shop_loop_item_inner_div() {

		$class = 'product-inner wpex-flex wpex-flex-col wpex-flex-grow wpex-relative';

		$align = get_theme_mod( 'woo_entry_align', null );

		if ( 'right' === $align || 'center' === $align || 'left' === $align ) {
			$class .= ' text' . $align;
		}

		echo '<div class="' . esc_attr( $class ) . '">';
	}

	/**
	 * Closes the "product-inner" div around product entries.
	 *
	 * @since 4.4
	 */
	public function close_shop_loop_item_inner_div() {
		echo '</div>';
	}

	/**
	 * Closes the "product-inner" div around product entries.
	 *
	 * @since 4.4
	 */
	public static function add_to_cart_link_wrapper( $link ) {

		$class = 'product-actions';

		if ( get_theme_mod( 'woo_entry_equal_height' ) && get_theme_mod( 'woo_default_entry_buttons' ) ) {
			$class .= ' wpex-mt-auto';
		}

		return '<div class="' . esc_attr( $class ) . '">' . $link . '</div>';
	}

	/**
	 * Adds an out of stock tag to the products.
	 *
	 * @since 4.4
	 */
	public function add_shop_loop_item_out_of_stock_badge() {
		if ( true === wpex_woo_product_instock() ) {
			return;
		}
		$text = apply_filters( 'wpex_woo_outofstock_text', esc_html__( 'Out of Stock', 'total' ) );
		echo '<div class="outofstock-badge">' . esc_html( $text ) . '</div>';
	}

	/**
	 * Open details wrapper
	 *
	 * @since 4.4
	 */
	public function loop_details_open() {
		echo '<div class="product-details wpex-pt-15">'; // @todo change padding to margin
	}

	/**
	 * Close details wrapper
	 *
	 * @since 4.4
	 */
	public function loop_details_close() {
		echo '</div>';
	}

	/**
	 * Returns our product thumbnail from our template parts based on selected style in theme mods.
	 *
	 * @since 4.8
	 */
	public function loop_product_thumbnail() {

		// Get entry product media style.
		$style = get_theme_mod( 'woo_product_entry_style' );

		if ( ! $style ) {
			$style = 'image-swap';
		}

		// Get entry product media template part.
		echo '<div class="wpex-loop-product-images wpex-overflow-hidden wpex-relative">';
			do_action( 'wpex_woocommerce_loop_thumbnail_before' );
				get_template_part( 'woocommerce/loop/thumbnail/' . $style );
			do_action( 'wpex_woocommerce_loop_thumbnail_after' );
		echo '</div>';

	}

	/**
	 * Output loop add to cart buttons with customw wrapper.
	 *
	 * @since 4.8
	 */
	public function loop_add_to_cart() { ?>
		<div class="wpex-loop-product-add-to-cart"><?php woocommerce_template_loop_add_to_cart(); ?></div>
	<?php }

}