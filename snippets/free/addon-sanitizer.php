<?php
/**
 * For add-on authors: clean SVG exactly the way this site does.
 *
 * easy_svg_sanitizer() is the one function an add-on may rely on. It returns
 * an \enshrined\svgSanitize\Sanitizer with the site's allow-list, or null when
 * the library is not loaded. Never build one yourself: a site that widened the
 * list would then lose what it keeps.
 *
 * EASY_SVG_API (3) changes only when that surface changes shape. Compare
 * against it, not against the plugin version.
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 */

/**
 * Clean one SVG, or '' when it cannot be cleaned or the plugin is missing.
 *
 * @param string $svg Untrusted markup.
 * @return string
 */
function acme_clean_svg( string $svg ): string {
	if ( ! defined( 'EASY_SVG_API' ) || EASY_SVG_API < 3 || ! function_exists( 'easy_svg_sanitizer' ) ) {
		return '';
	}
	$sanitizer = easy_svg_sanitizer();
	if ( null === $sanitizer ) {
		return '';
	}
	$clean = $sanitizer->sanitize( $svg );
	return is_string( $clean ) ? $clean : '';
}
