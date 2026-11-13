<?php
/**
 * Plugin Name: Easy SVG snippet: add-on sanitizer
 *
 * easy_svg_sanitizer(): the site's sanitizer with its allow-list, or null.
 *
 * Clean SVG the way this site does. Check EASY_SVG_API, not the plugin
 * version, and never build a sanitizer yourself: it would miss the site's list.
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
	if ( ! defined( 'EASY_SVG_API' ) || EASY_SVG_API < 1 || ! function_exists( 'easy_svg_sanitizer' ) ) {
		return '';
	}
	$sanitizer = easy_svg_sanitizer();
	if ( null === $sanitizer ) {
		return '';
	}
	$clean = $sanitizer->sanitize( $svg );
	return is_string( $clean ) ? $clean : '';
}
