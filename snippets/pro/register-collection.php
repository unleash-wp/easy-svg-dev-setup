<?php
/**
 * Plugin Name: Easy SVG Pro snippet: register a collection
 *
 * easy_svg_register_icon_collection( string $slug, string $label, string|array $dir_or_icons ): void
 *
 * A collection from code. Pass a folder and it is read like a drop-in folder:
 * manifest.json is read, every file goes through the site's sanitizer. Pass
 * slug => markup and the markup is only hardened (active parts removed), not
 * sanitized, so use it for markup you wrote yourself.
 *
 * Needs the Collections switch under Easy SVG → Settings → Features. Call it on
 * init at priority 10: Pro reads its own folders at 9 and registers at 11.
 *
 * The slug given here names the collection. A manifest names the icons only.
 * Then: `easy_svg_icon( 'acme-ui/close', array( 'label' => __( 'Close', 'acme' ) ) )`.
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 */

add_action(
	'init',
	static function () {
		if ( ! function_exists( 'easy_svg_register_icon_collection' ) ) {
			return;
		}

		// A folder: acme/<file name>, or the names in its manifest.json.
		easy_svg_register_icon_collection( 'acme', 'ACME Icons', __DIR__ . '/icons' );

		// Inline markup, for two or three icons that live in code.
		easy_svg_register_icon_collection(
			'acme-ui',
			'ACME UI',
			array(
				'close' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" fill="none"/></svg>',
			)
		);
	}
);
