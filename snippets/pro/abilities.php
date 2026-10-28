<?php
/**
 * Easy SVG Pro abilities (WordPress 6.9+ Abilities API), category easy-svg-pro.
 * Switch "AI agent abilities", on by default. Every ability asks for
 * manage_options plus the right of the route behind it, and one whose feature
 * is off is not registered at all. Full list: abilities.md.
 *
 * Over REST: /wp-json/wp-abilities/v1/abilities/easy-svg-pro/<name>/run with
 * the arguments in `input`. Core picks the method from the annotations: GET
 * for a reading ability, DELETE for a destructive idempotent one, else POST.
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 */

/**
 * Icons whose slug or label contains $search, from every source.
 *
 * @param string $search Case-insensitive substring.
 * @return array|WP_Error items, total, page, per_page, has_more, truncated.
 */
function acme_find_icons( string $search ) {
	if ( ! function_exists( 'wp_get_ability' ) ) {
		return new WP_Error( 'acme_no_abilities', 'This WordPress has no Abilities API.' );
	}
	$ability = wp_get_ability( 'easy-svg-pro/list-icons' );
	if ( null === $ability ) {
		return new WP_Error( 'acme_no_ability', 'Easy SVG Pro abilities are off or Pro is not active.' );
	}
	return $ability->execute(
		array(
			'source'   => 'all',
			'search'   => $search,
			'per_page' => 50,
		)
	);
}
