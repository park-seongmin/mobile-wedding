<?php
namespace TotalTheme\Integration;

defined( 'ABSPATH' ) || exit;

/**
 * Revslider Class
 *
 * @package TotalTheme
 * @subpackage Revslider
 * @version 5.2
 */
final class Revslider {

	/**
	 * Check if the customer has an active license.
	 *
	 * @access public
	 * @var boolean $valid is License valid.
	 */
	public $valid;

	/**
	 * Instance.
	 *
	 * @access private
	 * @var object Class object.
	 */
	private static $instance;

	/**
	 * Create or retrieve the instance of Revslider.
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

		$this->valid = get_option( 'revslider-valid', 'false' );

		if ( wpex_is_request( 'admin' ) ) {
			$this->admin_actions();
		}

		if ( wpex_is_request( 'frontend' ) ) {
			add_filter( 'revslider_meta_generator', '__return_false' );
		}

	}

	/**
	 * Admin actions.
	 */
	public function admin_actions() {

		// Remove things when license isn't valid
		if ( 'false' === $this->valid ) {

			if ( 'false' == $this->valid ) {
				add_action( 'admin_notices', array( $this, 'remove_plugins_page_notices' ), PHP_INT_MAX );
			}
		}

		// Remove metabox from various post types
		add_action( 'do_meta_boxes', array( $this, 'remove_metabox' ) );

	}

	/**
	 * Remove Revolution Slider plugin notices
	 *
	 * @since 4.6.5
	 */
	public function remove_plugins_page_notices() {
		$plugin_id = 'revslider/revslider.php';

		remove_action( 'after_plugin_row_' . $plugin_id, array( 'RevSliderAdmin', 'add_notice_wrap_pre' ), 10, 3 );
		remove_action( 'after_plugin_row_' . $plugin_id, array( 'RevSliderAdmin', 'show_purchase_notice' ), 10, 3);
		remove_action( 'after_plugin_row_' . $plugin_id, array('RevSliderAdmin', 'add_notice_wrap_post' ), 10, 3);

	}

	/**
	 * Remove metabox from VC grid builder
	 *
	 * @since 4.6.5
	 * @todo deprecate | no longer needed?
	 */
	public function remove_metabox() {
		remove_meta_box(
			'mymetabox_revslider_0',
			array(
				'vc_grid_item',
				'templatera',
				'wpex_sidebars',
				'ptu',
				'ptu_tax'
			),
			'normal'
		);
	}

}