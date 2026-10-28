<?php
/**
 * Upload bounds of the free plugin. The defaults suit icons; raise them only
 * for a site that really uploads large drawings.
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 */

// easy_svg_max_mb_ceiling( int $mb = 20 ): the highest cap the Icons screen offers.
add_filter(
	'easy_svg_max_mb_ceiling',
	static function () {
		return 40;
	}
);

// easy_svg_max_bytes( int $bytes ): the cap itself. A bigger file is refused, never cut.
add_filter(
	'easy_svg_max_bytes',
	static function () {
		return 5 * MB_IN_BYTES;
	}
);

/*
 * easy_svg_max_php_tags( int $max = 10 ) and easy_svg_max_use_tags( int $max = 1000 ).
 * Structure, not size: each one rescans the document, so raising them raises
 * how long one small file can hold a worker. No icon comes near either.
 */
add_filter(
	'easy_svg_max_use_tags',
	static function () {
		return 2000;
	}
);

/*
 * easy_svg_respect_network_filetypes( bool $respect = true ). On multisite the
 * network's upload types win. Return false to let each site decide.
 */
// add_filter( 'easy_svg_respect_network_filetypes', '__return_false' );
