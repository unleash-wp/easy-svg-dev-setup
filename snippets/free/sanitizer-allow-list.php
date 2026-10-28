<?php
/**
 * Plugin Name: Easy SVG snippet: sanitizer allow-list
 *
 * The three filters that loosen a security decision. Add only what a real icon
 * set needs: every name here is something an uploaded file may then contain.
 *
 * Never add script, style, foreignObject, animate, set, animateTransform or
 * animateMotion. The list checks names, not what their attributes point at.
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 */

// esw_svg_allowed_tags( array $tags ): one more element name.
add_filter(
	'esw_svg_allowed_tags',
	static function ( $tags ) {
		$tags   = is_array( $tags ) ? $tags : array();
		$tags[] = 'view';
		return $tags;
	}
);

// esw_svg_allowed_attributes( array $attributes ): one more attribute name.
add_filter(
	'esw_svg_allowed_attributes',
	static function ( $attributes ) {
		$attributes   = is_array( $attributes ) ? $attributes : array();
		$attributes[] = 'focusable';
		return $attributes;
	}
);

/*
 * esw_svg_remove_remote_references( bool $remove = true )
 *
 * Off, an uploaded SVG may fetch from another host on every visitor's behalf.
 * So only on a site that sets the constant in wp-config.php.
 */
if ( defined( 'ACME_SVG_ALLOW_REMOTE_REFERENCES' ) && ACME_SVG_ALLOW_REMOTE_REFERENCES ) {
	add_filter( 'esw_svg_remove_remote_references', '__return_false' );
}
