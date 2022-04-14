<?php
/**
 * Customizer => Social Share.
 *
 * @package TotalTheme
 * @subpackage Customizer
 * @version 5.3.1
 */

defined( 'ABSPATH' ) || exit;

$social_share_items = wpex_social_share_items();

if ( $social_share_items ) {

	$social_share_choices = array();

	foreach ( $social_share_items as $k => $v ) {
		$social_share_choices[$k] = $v['site'];
	}

	$this->sections['wpex_social_sharing'] = array(
		'title' => esc_html__( 'Social Share Buttons', 'total' ),
		'panel' => 'wpex_general',
		'settings' => array(
			array(
				'id' => 'social_share_shortcode',
				'transport' => 'partialRefresh',
				'control' => array(
					'label' => esc_html__( 'Alternative Shortcode', 'total' ),
					'type' => 'text',
					'description' => esc_html__( 'Override the theme default social share with your custom social sharing shortcode.', 'total' ),
				),
			),
			array(
				'id' => 'social_share_heading',
				'transport' => 'partialRefresh',
				'default' => esc_html__( 'Share This', 'total' ),
				'control' => array(
					'label' => esc_html__( 'Horizontal Position Heading', 'total' ),
					'type'  => 'text',
					'description' => esc_html__( 'Leave blank to disable.', 'total' ),
				),
			),
			array(
				'id' => 'social_share_heading_tag',
				'default' => 'h4',
				'transport' => 'partialRefresh',
				'control' => array(
					'label' => esc_html__( 'Heading Tag', 'total' ),
					'type' => 'select',
					'choices' => array(
						'div' => 'div',
						'h2' => 'h2',
						'h3' => 'h3',
						'h4' => 'h4',
						'h5' => 'h5',
						'h6' => 'h6',
					),
				),
				'control_display' => array(
					'check' => 'social_share_heading',
					'value' => 'not_empty',
				),
			),
			array(
				'id' => 'social_share_twitter_handle',
				'transport' => 'postMessage',
				'control' => array(
					'label' => esc_html__( 'Twitter Handle', 'total' ),
					'type' => 'text',
				),
			),
			array(
				'id'  => 'social_share_sites',
				'transport' => 'partialRefresh',
				'default' => array( 'twitter', 'facebook', 'linkedin', 'email' ),
				'control' => array(
					'label'  => esc_html__( 'Sites', 'total' ),
					'desc' => esc_html__( 'Click and drag and drop elements to re-order them.', 'total' ),
					'type' => 'wpex-sortable',
					'choices' => $social_share_choices,
				),
			),
			array(
				'id' => 'social_share_position',
				'transport' => 'partialRefresh',
				'default' => 'horizontal',
				'control' => array(
					'label' => esc_html__( 'Position', 'total' ),
					'type' => 'select',
					'choices' => array(
						'horizontal' => esc_html__( 'Horizontal', 'total' ),
						'vertical' => esc_html__( 'Vertical (Fixed)', 'total' ),
					),
				),
				'control_display' => array(
					'check' => 'social_share_style',
					'value' => array( 'flat', 'minimal', 'three-d', 'rounded', 'custom' ),
				),
			),
			array(
				'id' => 'social_share_align',
				'transport' => 'postMessage',
				'control' => array(
					'type' => 'select',
					'label' => esc_html__( 'Alignment', 'total' ),
					'choices' => array(
						'' => esc_html__( 'Default', 'total' ),
						'left' => esc_html__( 'Left', 'total' ),
						'center' => esc_html__( 'Center', 'total' ),
						'right' => esc_html__( 'Right', 'total' ),
					),
				),
				'control_display' => array(
					'check' => 'social_share_position',
					'value' => 'horizontal',
				),
			),
			array(
				'id' => 'social_share_style',
				'transport' => 'partialRefresh',
				'default' => 'flat',
				'control' => array(
					'label' => esc_html__( 'Style', 'total' ),
					'type'  => 'select',
					'choices' => array(
						'flat' => esc_html__( 'Flat', 'total' ),
						'minimal' => esc_html__( 'Minimal', 'total' ),
						'three-d' => esc_html__( '3D', 'total' ),
						'rounded' => esc_html__( 'Rounded', 'total' ),
						'mag' => esc_html__( 'Magazine', 'total' ),
						'custom' => esc_html__( 'Custom', 'total' ),
					),
				),
			),
			array(
				'id' => 'social_share_link_color',
				'transport' => 'postMessage',
				'control' => array(
					'type' => 'color',
					'label' => esc_html__( 'Link Color', 'total' ),
				),
				'control_display' => array(
					'check' => 'social_share_style',
					'value' => 'custom',
				),
				'inline_css' => array(
					'target' => '.style-custom .wpex-social-share__link',
					'alter' => 'color',
				),
			),
			array(
				'id' => 'social_share_link_bg_color',
				'transport' => 'postMessage',
				'control' => array(
					'type' => 'color',
					'label' => esc_html__( 'Background', 'total' ),
				),
				'control_display' => array(
					'check' => 'social_share_style',
					'value' => 'custom',
				),
				'inline_css' => array(
					'target' => '.style-custom  .wpex-social-share__link',
					'alter' => 'background-color',
				),
			),
			array(
				'id' => 'social_share_link_bg_color_hover',
				'transport' => 'postMessage',
				'control' => array(
					'type' => 'color',
					'label' => esc_html__( 'Background: Hover', 'total' ),
				),
				'control_display' => array(
					'check' => 'social_share_style',
					'value' => 'custom',
				),
				'inline_css' => array(
					'target' => '.style-custom  .wpex-social-share__link:hover',
					'alter' => 'background-color',
				),
			),
			array(
				'id' => 'social_share_link_border_radius',
				'transport' => 'postMessage',
				'control' => array(
					'type' => 'select',
					'label' => esc_html__( 'Border Radius', 'total' ),
					'choices' => wpex_utl_border_radius(),
				),
				'control_display' => array(
					'check' => 'social_share_style',
					'value' => 'custom',
				),
			),
			array(
				'id' => 'social_share_label',
				'transport' => 'partialRefresh',
				'default' => true,
				'control' => array(
					'label' => esc_html__( 'Display Horizontal Style Label?', 'total' ),
					'type' => 'checkbox',
				),
				'control_display' => array(
					'check' => 'social_share_style',
					'value' => array( 'flat', 'minimal', 'three-d', 'rounded', 'custom' ),
				),
			),
			array(
				'id' => 'social_share_stretch_items',
				'transport' => 'partialRefresh',
				'default' => false,
				'control' => array(
					'label' => esc_html__( 'Stretch Items?', 'total' ),
					'type' => 'checkbox',
					'description' => esc_html__( 'Will stretch the links to fill up the space for the horizontal style.', 'total' ),
				),
			),
			array(
				'id' => 'social_share_link_dims',
				'transport' => 'postMessage',
				'control' => array(
					'type' => 'text',
					'label' => esc_html__( 'Dimensions', 'total' ),
					'description' => esc_html__( 'This field is used for the height, line-height and width of each social link when displaying the icon only without labels.', 'total' ),
				),
				'inline_css' => array(
					'target' => '.wpex-social-share__link--sq',
					'alter' => array( 'height', 'width', 'line-height' ),
					'sanitize' => 'px',
					'important' => true,
				),
			),
			array(
				'id' => 'social_share_font_size',
				'transport' => 'postMessage',
				'control' => array(
					'type' => 'text',
					'label' => esc_html__( 'Font Size', 'total' ),
					'input_attrs' => array(
						'placeholder' => '1em',
					),
				),
				'inline_css' => array(
					'target' => '.wpex-social-share__link',
					'alter' => 'font-size',
				),
			),
		)
	);

}