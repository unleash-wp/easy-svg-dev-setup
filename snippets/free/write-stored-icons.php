<?php
/**
 * Plugin Name: Easy SVG snippet: write stored icons
 *
 * Change a stored icon of the free library. Use these rather than
 * wp_update_post(): a re-saved post runs through kses and empties the icon for
 * anybody without unfiltered_html, and the cached icon list stays stale.
 *
 * easy_svg_update_icon_markup( int $id, string $markup ): string
 *   'updated', 'not_found', 'no_markup', 'refused' or 'failed'.
 *   The bytes pass the site's sanitizer and the upload bounds.
 *
 * easy_svg_rename_icon( int $id, string $label ): string
 *   'renamed', 'not_found', 'no_label' or 'failed'.
 *   The slug never moves: saved blocks point at it.
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 */

/**
 * Example: give one icon a new drawing and a new name.
 *
 * @param int    $icon_id Post id of the stored icon (post type esw_icon).
 * @param string $svg     New markup from wherever it came.
 * @param string $label   New visible name.
 * @return array{markup:string,label:string}
 */
function acme_replace_icon( int $icon_id, string $svg, string $label ): array {
	if ( ! function_exists( 'easy_svg_update_icon_markup' ) || ! function_exists( 'easy_svg_rename_icon' ) ) {
		return array(
			'markup' => 'unavailable',
			'label'  => 'unavailable',
		);
	}
	return array(
		'markup' => easy_svg_update_icon_markup( $icon_id, $svg ),
		'label'  => easy_svg_rename_icon( $icon_id, sanitize_text_field( $label ) ),
	);
}
