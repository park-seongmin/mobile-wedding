<?php
defined( 'ABSPATH' ) || exit;

/**
 * WPBakery Row Row parsing.
 *
 * @package TotalTheme
 * @subpackage WPBakery
 * @version 5.3
 */
final class VCEX_Parse_Row_Atts {

	/**
	 * Class Constructor
	 *
	 * @since 2.0.2
	 */
	public function __construct( $atts ) {

		// Set attributes var.
		$this->atts = $atts;

		// Loop through attributes and parse them.
		foreach ( $this->atts as $key => $value ) {
			$method = 'parse_' . $key;
			if ( method_exists( $this, $method ) ) {
				$this->$method( $value );
			}
		}

	}

	/**
	 * Full width edits.
	 *
	 * @since 2.0.2
	 */
	private function parse_full_width( $value ) {
		if ( $value
			&& apply_filters( 'wpex_boxed_layout_vc_stretched_rows_reset', true )
			&& 'boxed' === wpex_site_layout()
		) {
			$this->atts['full_width_style'] = $this->atts['full_width'];
			$this->atts['full_width_boxed_layout'] = 'true';
			$this->atts['full_width'] = ''; // unset full-width
		}
	}

	/**
	 * Convert older ID param to el_id.
	 *
	 * @since 2.0.2
	 */
	private function parse_id( $value ) {
		if ( $value && empty( $this->atts['el_id'] ) ) {
			$this->atts['el_id'] = $value;
			unset( $this->atts['id'] );
		}
	}

	/**
	 * Convert match_column_height to equal_height.
	 *
	 * @since 2.0.2
	 */
	private function parse_match_column_height( $value ) {
		if ( $value ) {
			$this->atts['equal_height'] = 'yes';
			unset( $this->atts['match_column_height'] );
		}
	}

	/**
	 * Convert bg_image ID into src.
	 *
	 * @since 2.0.2
	 */
	private function parse_bg_image( $value ) {

		// Check old bg_img url.
		if ( ! empty( $value ) && empty( $this->atts['css'] ) ) {
			$this->atts['bg_image'] = esc_url( wp_get_attachment_url( $value ) );
		}

		// Check CSS.
		if ( $bg_image = $this->get_bg_from_css( $this->atts ) ) {
			$this->atts['bg_image'] = esc_url( $bg_image );
			return;
		}

	}

	/**
	 * Convert center row into bool.
	 *
	 * @since 2.0.2
	 */
	private function parse_center_row( $value ) {
		if ( ! empty( $this->atts['full_width'] ) ) {
			$this->atts['center_row'] = false;
		} elseif ( 'yes' === $value && 'full-screen' === wpex_content_area_layout() ) {
			$this->atts['center_row'] = true;
		} else {
			$this->atts['center_row'] = false;
		}
	}

	/**
	 * Convert 'no-margins' to '0px' column_spacing.
	 *
	 * @since 2.0.2
	 */
	private function parse_no_margins( $value ) {
		if ( 'true' == $value ) {
			$this->atts['column_spacing'] = '0px';
		}
	}

	/**
	 * Convert style to typography style.
	 *
	 * @since 2.0.2
	 */
	private function parse_style( $value ) {

		// Sanitize to make sure it hasn't been parsed by another function.
		$value = $value ? $value : $this->atts['style'];

		// Return if empty or set to none.
		if ( empty( $value ) || 'none' === $value ) {
			return;
		}

		// Set new typography_style atts.
		if ( empty( $this->atts['typography_style'] ) ) {
			$this->atts['typography_style'] = wpex_typography_style_class( $value );
			$this->atts['style'] = '';
		}

	}

	/**
	 * Convert Typography style to correct classname.
	 *
	 * @since 2.0.2
	 */
	private function parse_typography_style( $value ) {
		if ( function_exists( 'wpex_typography_style_class' ) ) {
			$this->atts['typography_style'] = wpex_typography_style_class( $this->atts['typography_style'] );
		}
	}

	/**
	 * Background style class.
	 *
	 * @since 2.0.2
	 * @deprecated bg_style is deprecated, this is a fallback.
	 */
	private function parse_bg_style_class() {

		// If background image isn't defined we don't need to add a background style class.
		if ( empty( $this->atts['bg_image'] ) ) {
			return;
		}

		// Get background style.
		$bg_style = ( isset( $this->atts['bg_style'] ) ) ? $this->atts['bg_style'] : 'cover';

		// Return correct background style class.
		if ( ! $bg_style ) {
			$this->atts['bg_style_class'] = '';
		} elseif( 'stretch' === $bg_style || 'cover' === $bg_style ) {
			$this->atts['bg_style_class'] = 'bg-cover';
		} elseif( 'repeat' === $bg_style ) {
			$this->atts['bg_style_class'] = 'wpex-bg-repeat';
		} elseif( 'fixed' === $bg_style ) {
			$this->atts['bg_style_class'] = 'bg-fixed';
		} elseif( 'repeat-x' === $bg_style ) {
			$this->atts['bg_style_class'] = 'wpex-bg-repeat-x';
		} elseif( 'repeat-y' === $bg_style ) {
			$this->atts['bg_style_class'] = 'wpex-bg-repeat-y';
		} elseif( 'fixed-top' === $bg_style ) {
			$this->atts['bg_style_class'] = 'bg-fixed-top';
		} elseif( 'repeat-bottom' === $bg_style ) {
			$this->atts['bg_style_class'] = 'bg-fixed-bottom';
		}

	}

	/**
	 * Finds the attachment object from the generated CSS by WPBakery.
	 *
	 * @since 3.0.0
	 */
	private static function get_bg_from_css( $atts ) {
		if ( empty( $atts['css'] ) ) {
			return false;
		}
		if ( preg_match( '/\?id=(\d+)/', $atts['css'], $id ) === false ) {
			return false;
		}
		if ( count( $id ) < 2 ) {
			return false;
		}
		$id = $id[1];
		return wp_get_attachment_url( $id );
	}

	/**
	 * Returns attributes.
	 *
	 * @since 2.0.2
	 */
	public function return_atts() {
		return $this->atts;
	}

}