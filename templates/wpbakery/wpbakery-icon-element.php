<?php
/**
 * WPBakery Page Builder: an element of your own with the easy_svg_icon param.
 *
 * Switch "WPBakery icon element" on under Icons, Features. Pro then registers
 * the param type easy_svg_icon (and its own element "Easy SVG Icon"). The param
 * stores the icon's address, `collection/slug`.
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 */

// vc_before_init is where WPBakery's own integrations map their elements.
add_action(
	'vc_before_init',
	static function () {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}
		vc_map(
			array(
				'name'     => 'ACME Feature',
				'base'     => 'acme_feature',
				'category' => 'ACME',
				'params'   => array(
					array(
						// WPBakery reads a param back by an unquoted selector:
						// letters, digits and underscores only.
						'type'        => 'easy_svg_icon',
						'param_name'  => 'icon',
						'heading'     => 'Icon',
						'admin_label' => true,
					),
					array(
						'type'       => 'textfield',
						'param_name' => 'title',
						'heading'    => 'Title',
					),
				),
			)
		);
	}
);

// Registered whether or not the switch is on, so a saved page never prints its shortcode.
add_shortcode(
	'acme_feature',
	static function ( $atts ) {
		$atts = shortcode_atts(
			array(
				'icon'  => '',
				'title' => '',
			),
			$atts,
			'acme_feature'
		);
		$icon = function_exists( 'easy_svg_icon' ) ? easy_svg_icon( (string) $atts['icon'], array( 'class' => 'acme-feature__icon' ) ) : '';
		return '<div class="acme-feature">' . $icon . '<h3>' . esc_html( (string) $atts['title'] ) . '</h3></div>';
	}
);
