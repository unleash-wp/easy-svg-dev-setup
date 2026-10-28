<?php
/**
 * Plugin Name: Easy SVG Pro snippet: source folders
 *
 * Icon folders. Pro reads wp-content/easy-svg-icons/<folder>/ and the active
 * theme's assets/icons (child over parent) by itself. These hooks add to that.
 *
 * Needs the "Theme & folder sources" switch.
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 */

/*
 * easy_svg_pro_source_dirs( array $dirs ): absolute paths, no trailing slash.
 * Pro keys a folder by its LAST path segment, so give each one a name nobody
 * else uses. Symbolic links pointing out of a folder are refused.
 */
add_filter(
	'easy_svg_pro_source_dirs',
	// Untyped and cast: another plugin may have broken this list before us.
	static function ( $dirs ): array {
		$dirs   = is_array( $dirs ) ? $dirs : array();
		$dirs[] = WP_CONTENT_DIR . '/shared-icons/acme-brand';
		return $dirs;
	}
);

/*
 * easy_svg_pro_source_max_icons( int $max = 1000 ): icons read per folder.
 * Over it a folder is cut short and the Packs screen says what was left out.
 * Set what the pack really holds: every icon is read and sanitised on a cache miss.
 */
add_filter(
	'easy_svg_pro_source_max_icons',
	static function ( $max ): int {
		return max( (int) $max, 1400 );
	}
);

/*
 * easy_svg_pro_theme_collection_slug( string $slug ): the theme folder's
 * collection name. Default: the manifest's slug, else 'theme'.
 */
add_filter(
	'easy_svg_pro_theme_collection_slug',
	static function () {
		return 'acme-theme';
	}
);
