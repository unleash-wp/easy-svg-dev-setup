<?php
/**
 * Plugin Name: Easy SVG snippet: upload limits
 *
 * How big and how complex one SVG may be before the plugin refuses it. The
 * defaults suit icons. Raise them only for a site that uploads large drawings.
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 */

/*
 * easy_svg_max_bytes( int $bytes ): the largest file the plugin reads, in bytes.
 * Default: 2 MB, or the server's upload limit when that is lower. A bigger file
 * is refused, never cut. There is no screen for it: this filter is the setting.
 */
add_filter(
	'easy_svg_max_bytes',
	static function () {
		return 5 * MB_IN_BYTES;
	}
);

/*
 * easy_svg_max_php_tags( int $max = 10 ) and easy_svg_max_use_tags( int $max = 1000 ).
 * These count structure, not size: each one makes the sanitizer work longer,
 * so a raised limit lets one small file hold a PHP worker longer.
 */
add_filter(
	'easy_svg_max_use_tags',
	static function () {
		return 2000;
	}
);

/*
 * easy_svg_respect_network_filetypes( bool $respect = true ). On multisite the
 * network's allowed upload types win. Return false to let each site decide.
 */
if ( is_multisite() && defined( 'ACME_SVG_SITE_DECIDES_FILETYPES' ) && ACME_SVG_SITE_DECIDES_FILETYPES ) {
	add_filter( 'easy_svg_respect_network_filetypes', '__return_false' );
}
