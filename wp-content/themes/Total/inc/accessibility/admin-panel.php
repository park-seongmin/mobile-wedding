<?php
namespace TotalTheme\Accessibility;

defined( 'ABSPATH' ) || exit;

/**
 * Adds custom CSS to the site to tweak the main accent colors.
 *
 * @package TotalTheme
 * @version 5.3
 */
final class Admin_Panel {

	/**
	 * Instance.
	 *
	 * @access private
	 * @var object Class object.
	 */
	private static $instance;

	/**
	 * Create or retrieve the instance of Admin_Panel.
	 */
	public static function instance() {
		if ( is_null( static::$instance ) ) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	/**
	 * Class constructor.
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'add_page' ), 50 );
			add_action( 'admin_init', array( $this, 'register_settings' ) );
		}
	}

	/**
	 * Add sub menu page.
	 *
	 * @since 4.6.5
	 */
	public function add_page() {
		add_submenu_page(
			WPEX_THEME_PANEL_SLUG,
			esc_attr__( 'Accessibility', 'total' ),
			esc_attr__( 'Accessibility', 'total' ),
			'edit_theme_options',
			WPEX_THEME_PANEL_SLUG . 'wpex-accessibility',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Register a setting and its sanitization callback.
	 *
	 * @since 4.6.5
	 */
	public function register_settings() {
		register_setting(
			'wpex_accessibility_settings',
			'wpex_accessibility_settings',
			array(
				'sanitize_callback' => array( $this, 'save_options' ),
				'default' => null,
			)
		);
	}

	/**
	 * Sanitization callback.
	 *
	 * @since 4.6.5
	 */
	public function save_options( $options ) {

		$settings = $this->get_settings();

		if ( empty( $settings ) ) {
			return;
		}

		foreach ( $settings as $k => $v ) {

			$type    = isset( $v['type'] ) ? $v['type'] : 'input';
			$default = isset( $v['default'] ) ? $v['default'] : null;

			switch ( $type ) {

				case 'checkbox':

					if ( isset( $options[$k] ) ) {
						if ( ! $default ) {
							set_theme_mod( $k, true );
						} else {
							remove_theme_mod( $k );
						}
					} else {
						if ( $default ) {
							set_theme_mod( $k, false );
						} else {
							remove_theme_mod( $k );
						}
					}

					break;

				case 'aria_label':

					$aria_labels = get_theme_mod( 'aria_labels' );

					if ( empty( $options[$k] ) ) {
						unset( $aria_labels[$k] );
					} else {

						$defaults = wpex_aria_label_defaults();

						if ( ! isset( $defaults[$k] ) || ( $defaults[$k] !== $options[$k] ) ) {
							$aria_labels[$k] = $options[$k];
						}

					}

					if ( ! empty( $aria_labels ) ) {
						set_theme_mod( 'aria_labels', $aria_labels );
					} else {
						remove_theme_mod( 'aria_labels' );
					}

					break;

				default:

					if ( ! empty( $options[$k] ) && $default != $options[$k] ) {
						set_theme_mod( $k, wp_strip_all_tags( $options[$k] ) );
					} else {
						remove_theme_mod( $k );
					}

					break;

			} // end switch.

		} // end foreach.

	}

	/**
	 * Return array of settings.
	 *
	 * @since 4.6.5
	 */
	public function get_settings() {
		$array = array(
			// General options.
			'skip_to_content' => array(
				'name' => esc_html__( 'Skip to content link', 'total' ),
				'default' => true,
				'type' => 'checkbox',
				'description' => esc_html__( 'Enables the skip to content link when clicking tab as soon as your site loads.', 'total' ),
			),
			'skip_to_content_id' => array(
				'name' => esc_html__( 'Skip to content ID', 'total' ),
				'default' => '#content',
				'type' => 'text',
			),
			'remove_menu_ids' => array(
				'name' => esc_html__( 'Remove Menu ID attributes', 'total' ),
				'default' => false,
				'type' => 'checkbox',
				'description' => esc_html__( 'Removes the ID attributes added by default in WordPress to each item in your menu.', 'total' ),
			),
			'aria_landmarks_enable' => array(
				'name' => esc_html__( 'Aria Landmarks', 'total' ),
				'default' => false,
				'type' => 'checkbox',
				'description' => esc_html__( 'Enables the aria landmark tags in the theme which are disabled by default as they generate errors in the W3C checker.', 'total' ),
			),
			// Aria labels.
			'site_navigation' => array(
				'name' => esc_html__( 'Main Menu', 'total' ),
				'type' => 'aria_label',
			),
			'search' => array(
				'name' => esc_html__( 'Search', 'total' ),
				'type' => 'aria_label',
			),
			'mobile_menu' => array(
				'name' => esc_html__( 'Mobile Menu', 'total' ),
				'type' => 'aria_label',
			),
			'mobile_menu_toggle' => array(
				'name' => esc_html__( 'Mobile Menu Open Button', 'total' ),
				'type' => 'aria_label',
			),
			'mobile_menu_close' => array(
				'name' => esc_html__( 'Mobile Menu Close Button', 'total' ),
				'type' => 'aria_label',
			),
			'breadcrumbs' => array(
				'name' => esc_html__( 'Breadcrumbs', 'total' ),
				'type' => 'aria_label',
			),
			'footer_callout' => array(
				'name' => esc_html__( 'Footer Callout', 'total' ),
				'type' => 'aria_label',
			),
			'footer_bottom_menu' => array(
				'name' => esc_html__( 'Footer Menu', 'total' ),
				'type' => 'aria_label',
			),
		);

		if ( WPEX_WOOCOMMERCE_ACTIVE ) {
			$array['shop_cart'] = array(
				'name' => esc_html__( 'Your cart', 'total' ),
				'type' => 'aria_label',
			);
		}

		return $array;

	}

	/**
	 * Settings page output.
	 *
	 * @since 4.6.5
	 */
	public function create_admin_page() { ?>

		<div class="wrap">

			<h1><?php esc_html_e( 'Accessibility', 'total' ); ?></h1>

			<?php $this->nav_tabs(); ?>

			<form method="post" action="options.php">

				<?php settings_fields( 'wpex_accessibility_settings' ); ?>

				<?php
				$tabs = array();
				foreach ( $this->get_settings() as $setting_id => $setting ) {
					$tab = ( 'aria_label' === $setting['type'] ) ? 'aria_labels' : 'general';
					$tabs[$tab][$setting_id] = $setting;
				}

				foreach ( $tabs as $tab => $settings ) {

					// Note array_key_first was added in PHP 7.3
					if ( function_exists( 'array_key_first' ) && $tab === array_key_first( $tabs ) ) {
						$active = ' wpex-admin-tabs__panel--active';
					} elseif( $tab === 'general' ) {
						$active = ' wpex-admin-tabs__panel--active';
					} else {
						$active = '';
					}
					?>

					<table id="wpex-admin-tabpanel--<?php echo esc_attr( $tab ); ?>" class="form-table wpex-admin-tabs__panel<?php echo esc_attr( $active ); ?>" role="tabpanel" tabindex="0" aria-labelledby="wpex-admin-tab--<?php echo esc_attr( $tab ); ?>">

						<?php foreach ( $settings as $setting_id => $setting ) {

							$type = ! empty( $setting[ 'type' ] ) ? $setting[ 'type' ] : 'input';

							?>

								<tr valign="top">

									<th scope="row">
										<?php if ( 'checkbox' === $type ) {
											echo esc_html( $setting['name'] );
										} else { ?>
											<label for="wpex_accessibility_settings[<?php echo esc_attr( $setting_id ); ?>]"><?php echo esc_html( $setting['name'] ); ?></label>
										<?php } ?>
									</th>

									<td><?php $this->setting_field( $setting_id, $setting ); ?></td>

								</tr>

							<?php } ?>

						</table>

					<?php } ?>

				<?php submit_button(); ?>

			</form>

		</div>

	<?php }

	/**
	 * Return setting field.
	 *
	 * @since 5.1.2
	 */
	public function setting_field( $key, $setting ) {

		$type = ! empty( $setting[ 'type' ] ) ? $setting[ 'type' ] : 'input';
		$default = isset( $setting[ 'default' ] ) ? $setting[ 'default' ] : null;
		$description = ! empty( $setting[ 'description' ] ) ? $setting[ 'description' ] : null;

		switch ( $type ) {

			case 'checkbox':

				$theme_mod = get_theme_mod( $key, $default );

				?>

				<?php if ( $description ) { ?>
					<label for="wpex_accessibility_settings[<?php echo esc_attr( $key ); ?>]">
				<?php } ?>

				<input id="wpex_accessibility_settings[<?php echo esc_attr( $key ); ?>]" type="checkbox" name="wpex_accessibility_settings[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $theme_mod ); ?>" <?php checked( $theme_mod, true ); ?>>

				<?php if ( $description ) { ?>
					<?php echo esc_html( $description ); ?>
					</label>
				<?php } ?>

				<?php break;

			case 'aria_label':

				$aria_label = wpex_get_aria_label( $key );

				?>

				<input type="text" id="wpex_accessibility_settings[<?php echo esc_attr( $key ); ?>]" name="wpex_accessibility_settings[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $aria_label ); ?>">

				<?php if ( $description ) { ?>
					<p class="description"><?php echo esc_html( $description ); ?></p>
				<?php } ?>

				<?php break;

			default:

				$theme_mod = get_theme_mod( $key, $default );

				?>

				<input type="text" id="wpex_accessibility_settings[<?php echo esc_attr( $key ); ?>]" name="wpex_accessibility_settings[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $theme_mod ); ?>">

				<?php if ( $description ) { ?>
					<p class="description"><?php echo esc_html( $description ); ?></p>
				<?php } ?>

				<?php break;

		} // end switch.

	}

	/**
	 * Panel tabs.
	 *
	 * @since 5.1.2
	 */
	public function nav_tabs() { ?>

		<h2 class="nav-tab-wrapper wpex-admin-tabs__list" role="tablist">
			<a id="wpex-admin-tab--general" href="#" class="nav-tab nav-tab-active wpex-admin-tabs__tab" aria-controls="wpex-admin-tabpanel--general" aria-selected="true" role="tab" tabindex="0"><?php esc_html_e( 'General', 'total' ); ?></a>
			<a id="wpex-admin-tab--aria_labels" href="#" class="nav-tab wpex-admin-tabs__tab" aria-controls="wpex-admin-tabpanel--aria_labels" aria-selected="false" role="tab" tabindex="-1"><?php esc_html_e( 'Aria Labels', 'total' ); ?></a>
		</h2>

	<?php }

}