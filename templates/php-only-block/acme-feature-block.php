<?php
/**
 * A block written in PHP only (WordPress 7.0+, supports.autoRegister) with an
 * icon picker in its sidebar. No JavaScript of your own.
 *
 * Switch "PHP-only block icon field" on under Icons, Features. Pro then draws
 * an icon picker for every attribute marked 'unleashwp' => component icon.
 * Switched off, core shows its plain text field and the page still renders.
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 */

add_action(
	'init',
	static function () {
		register_block_type(
			'acme/feature',
			array(
				'title'           => 'Feature',
				'category'        => 'widgets',
				'supports'        => array( 'autoRegister' => true ),
				'attributes'      => array(
					'title'    => array(
						'type'  => 'string',
						'label' => 'Title',
					),
					'iconName' => array(
						'type'      => 'string',
						'label'     => 'Icon',
						// 'collections' => array( 'acme-theme' ) limits what the picker offers.
						'unleashwp' => array( 'component' => 'icon' ),
					),
				),
				'render_callback' => static function ( $attributes ) {
					// The free tag, not Pro's: it stays when Pro is deactivated.
					$icon = function_exists( 'easy_svg_icon' )
						? easy_svg_icon( (string) ( $attributes['iconName'] ?? '' ), array( 'class' => 'acme-feature__icon' ) )
						: '';
					return '<div class="acme-feature">' . $icon . '<h3>' . esc_html( (string) ( $attributes['title'] ?? '' ) ) . '</h3></div>';
				},
			)
		);
	}
);

/*
 * Size it through the class, in the block's CSS:
 *
 *     .acme-feature__icon { width: 3rem; height: 3rem; }
 */
