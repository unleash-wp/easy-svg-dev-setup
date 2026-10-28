<?php
/**
 * easy_svg_registrable_icons( array $icons )
 *
 * The whole free library on its way to WordPress's icon registry (7.1+), as a
 * list of array( 'slug' => …, 'label' => …, 'content' => … ). The only place
 * the markup the Icon block draws can be changed: core keeps its own
 * allow-list and has no filter.
 *
 * Drop an entry and the icon is not offered. Answer with something that is not
 * a list and nothing is registered.
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 */

// The hook exists only where this constant does; a filter cannot be probed for.
if ( defined( 'EASY_SVG_ICONS_API' ) ) {
	add_filter(
		'easy_svg_registrable_icons',
		static function ( $icons ) {
			if ( ! is_array( $icons ) ) {
				return $icons;
			}
			// Keep drafts of a redesign out of the inserter by a slug prefix.
			return array_values(
				array_filter(
					$icons,
					static function ( $icon ) {
						return is_array( $icon ) && 0 !== strpos( (string) ( $icon['slug'] ?? '' ), 'wip-' );
					}
				)
			);
		}
	);
}
