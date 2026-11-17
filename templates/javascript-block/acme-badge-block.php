<?php
/**
 * Plugin Name: ACME Badge block (JavaScript)
 * Description: A block with the Easy SVG Pro icon field in the sidebar and the toolbar, written in plain JavaScript.
 * License:     GPL-2.0-or-later
 *
 * A block written in JavaScript with the Easy SVG Pro icon field, in the
 * sidebar (InspectorControls) and in the block toolbar (BlockControls).
 * editor.js is plain script: no build step.
 *
 * Switch on "PHP-only blocks" under Easy SVG → Settings → Features; the same
 * switch covers JavaScript blocks. Without Pro, or with the switch off, the
 * block still loads and the sidebar falls back to a text field. With the
 * switch off the page still draws every icon; without Pro only icons of the
 * free library.
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 */

add_action(
	'init',
	static function () {
		$deps = array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render' );
		// Pro registers its handle on init at 20. A dependency that is not registered makes WordPress drop this script, and the block with it.
		if ( wp_script_is( 'easy-svg-pro-icon-control', 'registered' ) ) {
			$deps[] = 'easy-svg-pro-icon-control';
		}
		wp_register_script( 'acme-badge-editor', plugins_url( 'editor.js', __FILE__ ), $deps, '1.0.0', true );

		register_block_type(
			'acme/badge',
			array(
				'title'           => 'Badge',
				'category'        => 'widgets',
				'editor_script'   => 'acme-badge-editor',
				'attributes'      => array(
					'icon'  => array(
						'type'    => 'string',
						'default' => '',
					),
					'label' => array(
						'type'    => 'string',
						'default' => '',
					),
				),
				'render_callback' => static function ( $attributes ) {
					// The free tag, not Pro's: it stays when Pro is deactivated.
					$icon = function_exists( 'easy_svg_icon' )
						? easy_svg_icon( (string) ( $attributes['icon'] ?? '' ), array( 'class' => 'acme-badge__icon' ) )
						: '';
					return '<span class="acme-badge">' . $icon . esc_html( (string) ( $attributes['label'] ?? '' ) ) . '</span>';
				},
			)
		);
	},
	21
);

/*
 * Size it through the class, in the block's CSS:
 *
 *     .acme-badge__icon { width: 1.25em; height: 1.25em; }
 */
