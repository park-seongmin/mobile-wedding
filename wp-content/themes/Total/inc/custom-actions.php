<?php
namespace TotalTheme;

defined( 'ABSPATH' ) || exit;

/**
 * Custom user actions.
 *
 * @package TotalTheme
 * @version 5.3
 */
final class Custom_Actions {

	/**
	 * Class instance.
	 *
	 * @access private
	 * @var object Class object.
	 */
	private static $instance;

	/**
	 * Create or retrieve the instance of Custom_Actions.
	 */
	public static function instance() {
		if ( is_null( static::$instance ) ) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		if ( wpex_is_request( 'admin' ) ) {
			add_action( 'admin_menu', __CLASS__ . '::add_admin_page', 40 );
			add_action( 'admin_init', __CLASS__ . '::register_settings' );
		}

		if ( wpex_is_request( 'frontend' ) ) {
			add_action( 'init', __CLASS__ . '::render_actions' );
		}

	}

	/**
	 * Add sub menu page.
	 *
	 * @since 3.0.0
	 */
	public static function add_admin_page() {
		add_submenu_page(
			WPEX_THEME_PANEL_SLUG,
			esc_html__( 'Custom Actions', 'total' ),
			esc_html__( 'Custom Actions', 'total' ),
			'administrator',
			WPEX_THEME_PANEL_SLUG . '-user-actions',
			__CLASS__ . '::create_admin_page'
		);
	}

	/**
	 * Register a setting and its sanitization callback.
	 *
	 * @since 3.0.0
	 */
	public static function register_settings() {
		register_setting(
			'wpex_custom_actions',
			'wpex_custom_actions',
			__CLASS__ . '::admin_sanitize'
		);
	}

	/**
	 * Main Sanitization callback.
	 *
	 * @since 3.0.0
	 */
	public static function admin_sanitize( $options ) {

		if ( ! empty( $options ) ) {

			// Loop through options and save them.
			foreach ( $options as $key => $val ) {

				// Delete action if empty or blank string.
				if ( empty( $val['action'] ) || ctype_space( $val['action'] ) ) {
					unset( $options[$key] );
				}

				// Validate settings.
				else {

					// Sanitize action @todo don't allow javascript anymore?
					//$options[$key]['action'] = wp_kses_post( $val['action'] );

					// Priority must be a number.
					if ( ! empty( $val['priority'] ) ) {
						$options[$key]['priority'] = intval( $val['priority'] );
					}


				}
			}

			return $options;

		}

	}

	/**
	 * Settings page.
	 *
	 * @since 3.0.0
	 */
	public static function create_admin_page() {

		wp_enqueue_style(
			'wpex-custom-actions-admin',
			get_theme_file_uri( '/assets/css/wpex-custom-actions-admin.css' ),
			array(),
			WPEX_THEME_VERSION,
			'all'
		);

		wp_enqueue_script(
			'wpex-custom-actions-admin',
			get_theme_file_uri( '/assets/js/dynamic/admin/wpex-custom-actions-admin.min.js' ),
			array( 'jquery' ),
			WPEX_THEME_VERSION,
			false
		);

		?>

		<div class="wrap wpex-ca-admin-wrap">

			<h1><?php esc_html_e( 'Custom Actions', 'total' ); ?></h1>

			<p><?php esc_html_e( 'Here you can insert HTML code into any section of the theme. PHP code is not allowed for security reasons. If you wish to insert PHP code into a theme action you will want to use a child theme or shortcodes in the fields below.', 'total' ); ?></p>

			<hr>

			<form method="post" action="options.php">

				<?php settings_fields( 'wpex_custom_actions' ); ?>

				<?php $options = get_option( 'wpex_custom_actions' ); ?>

				<div class="wpex-ca-admin-inner">

					<div class="wpex-ca-admin-list">

						<?php
						// Get hooks.
						$wp_hooks = array(
							'wp_hooks' => array(
								'label' => 'WordPress',
								'hooks' => array(
									'wp_head',
									'wp_body_open',
									'wp_footer',
								),
							),
							'html' => array(
								'label' => 'HTML',
								'hooks' => array( 'wpex_hook_after_body_tag' )
							)
						);

						// Theme hooks.
						$theme_hooks = wpex_theme_hooks();

						// Remove header hooks if builder is enabled.
						if ( wpex_header_builder_id() ) {
							unset( $theme_hooks['header'] );
							unset( $theme_hooks['header_logo'] );
							unset( $theme_hooks['main_menu'] );
						}

						// Combine hooks.
						$hooks = ( $wp_hooks + $theme_hooks );

						// Loop through sections.
						foreach( $hooks as $section ) : ?>

							<div class="wpex-ca-admin-list-group">

								<h2><?php echo esc_html( $section['label'] ); ?></h2>

								<?php
								// Loop through hooks
								$hooks = $section['hooks'];

								foreach ( $hooks as $hook ) :

									// Get data
									$action = ! empty( $options[$hook]['action'] ) ? $options[$hook]['action'] : '';
									$priority = isset( $options[$hook]['priority'] ) ? intval( $options[$hook]['priority'] ) : 10;
									$not_empty = ( $action && ! ctype_space( $action ) ) ? true : false;

									?>

										<div class="wpex-ca-admin-list-item wpex-ca-closed<?php if ( $not_empty ) echo ' wpex-ca-admin-not-empty'; ?>">

											<div class="wpex-ca-admin-list-item-header">
												<h3><?php echo wp_strip_all_tags( $hook ); ?></span></h3>
												<div class="hide-if-no-js">
													<button class="wpex-ca-admin-toggle" aria-expanded="false">
														<span class="screen-reader-text"><?php esc_html_e( 'Toggle fields for action hook:', 'total' ); ?> <?php echo wp_strip_all_tags( $hook ); ?></span>
														<svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true" focusable="false"><path d="M17.5 11.6L12 16l-5.5-4.4.9-1.2L12 14l4.5-3.6 1 1.2z"></path></svg></span>
													</button>
												</div>
											</div>

											<div class="wpex-ca-admin-list-item-fields">

												<p>
													<label for="wpex_custom_actions[<?php echo esc_attr( $hook ); ?>][action]"><?php esc_html_e( 'Code', 'total' ); ?></label>
													<textarea id="wpex_custom_actions[<?php echo esc_attr( $hook ); ?>][action]" placeholder="<?php esc_attr_e( 'Enter your custom action here&hellip;', 'total' ); ?>" name="wpex_custom_actions[<?php echo esc_attr( $hook ); ?>][action]" rows="10" cols="50" style="width:100%;"><?php echo esc_textarea( $action ); ?></textarea>
												</p>

												<p class="wpex-clr">
													<label for="wpex_custom_actions[<?php echo esc_attr( $hook ); ?>][priority]"><?php esc_html_e( 'Priority', 'total' ); ?></label>
													<input id="wpex_custom_actions[<?php echo esc_attr( $hook ); ?>][priority]" name="wpex_custom_actions[<?php echo esc_attr( $hook ); ?>][priority]" type="number" value="<?php echo esc_attr( $priority ); ?>">
												</p>

											</div><!-- .wpex-ca-admin-list-item-fields -->

										</div><!-- .wpex-ca-admin-list-item -->

								<?php endforeach; ?>

							</div><!-- .wpex-ca-admin-list-group -->

						<?php endforeach; ?>

					</div><!-- .wpex-ca-admin-list -->

					<div class="wpex-ca-admin-sidebar">

						<div class="wpex-ca-admin-save">

							<h3><?php esc_html_e( 'Save Your Actions', 'total' ); ?></h3>

							<div class="wpex-ca-admin-save__inner">
								<p><?php esc_html_e( 'Click the button below to save your custom actions.', 'total' ); ?></p>
								<?php submit_button(); ?>
							</div>

						</div><!-- .wpex-ca-admin-save -->

					</div><!-- .wpex-ca-admin-sidebar -->

				</div><!-- .wpex-ca-admin -->

			</form>

		</div><!-- .wrap -->

	<?php }

	/**
	 * Outputs code on the front-end.
	 *
	 * @since 3.0.0
	 */
	public static function render_actions() {

		// Get actions.
		$actions = get_option( 'wpex_custom_actions' );

		// Return if actions are empty.
		if ( empty( $actions ) ) {
			return;
		}

		// Loop through options.
		foreach ( $actions as $key => $val ) {
			if ( ! empty( $val['action'] ) ) {
				$priority = isset( $val['priority'] ) ? intval( $val['priority'] ) : 10;
				add_action( $key, __CLASS__ . '::execute_action', $priority );
			}
		}

	}

	/**
	 * Used to execute an action.
	 *
	 * @since 3.0.0
	 */
	public static function execute_action() {
		$hook    = current_filter();
		$actions = get_option( 'wpex_custom_actions' );
		$output  = $actions[$hook]['action'];

		if ( $output && empty( $actions[$hook]['php'] ) ) {
			//$output = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $output ); // remove script tags
			//$output = wp_kses_post( $output ); // @todo
			echo do_shortcode( $output );
		}

	}

}