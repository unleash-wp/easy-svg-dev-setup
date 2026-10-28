<?php
/**
 * easy_svg_pro_registered_icon( string $content, string $reference )
 *
 * One Pro collection icon on its way to WordPress's icon registry. $reference is
 * `collection/slug`. The only place the Icon block's markup for a Pro
 * collection can change. For the free library use easy_svg_registrable_icons.
 *
 * What you return is what core keeps, minus what its own allow-list drops.
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 */

// Outline icons of one collection drawn with a thinner stroke in the Icon block.
add_filter(
	'easy_svg_pro_registered_icon',
	static function ( $content, $reference ) {
		if ( 0 !== strpos( (string) $reference, 'acme-outline/' ) ) {
			return $content;
		}
		return (string) preg_replace( '/stroke-width="2"/', 'stroke-width="1.5"', (string) $content );
	},
	10,
	2
);
