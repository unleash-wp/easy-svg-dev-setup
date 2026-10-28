<?php
/**
 * easy_svg_icon_markup( string $markup, string $slug, string $collection )
 *
 * Filters one icon on its way out of easy_svg_icon(), the shortcode and every
 * page builder element that renders through it. Not the Icon block: that one
 * draws what was registered, see filter-registrable-icons.php.
 *
 * What you return is printed as it is, so you own the escaping.
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 */

// Add a class to every icon of the free library.
add_filter(
	'easy_svg_icon_markup',
	static function ( $markup, $slug, $collection ) {
		if ( 'easy-svg' !== $collection || '' === $markup ) {
			return $markup;
		}
		// Only when the root has no class yet: a browser keeps the first of two.
		if ( 1 === preg_match( '/^\s*<svg\b[^>]*\sclass=/i', $markup ) ) {
			return $markup;
		}
		return (string) preg_replace( '/<svg\b/i', '<svg class="acme-icon acme-icon--' . esc_attr( $slug ) . '"', $markup, 1 );
	},
	10,
	3
);

/*
 * Serve a collection of your own without Pro: `acme/<slug>` read from a theme
 * folder. Run it through the site's sanitizer, because this markup is printed.
 */
add_filter(
	'easy_svg_icon_markup',
	static function ( $markup, $slug, $collection ) {
		if ( 'acme' !== $collection || '' !== $markup || ! function_exists( 'easy_svg_sanitizer' ) ) {
			return $markup;
		}
		if ( 1 !== preg_match( '/^[a-z0-9](?:[a-z0-9_-]*[a-z0-9])?$/', $slug ) ) {
			return $markup;
		}
		$file = get_stylesheet_directory() . '/assets/acme-icons/' . $slug . '.svg';
		if ( ! is_file( $file ) ) {
			return $markup;
		}
		$sanitizer = easy_svg_sanitizer();
		$clean     = null !== $sanitizer ? $sanitizer->sanitize( (string) file_get_contents( $file ) ) : false;
		if ( ! is_string( $clean ) ) {
			return '';
		}
		// The second layer the plugin puts on its own icons. Not promised API, so guarded.
		return function_exists( 'easy_svg_harden_icon_markup' ) ? easy_svg_harden_icon_markup( $clean ) : $clean;
	},
	10,
	3
);
