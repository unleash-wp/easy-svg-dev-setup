<?php
/**
 * Plugin Name: Easy SVG Pro snippet: import limits
 *
 * Bounds of the ZIP and icon font import, and the names the configurator
 * refuses to put on the allow-list.
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 */

// easy_svg_pro_import_max_entries( int $max = 5000 ): entries read from one ZIP.
add_filter(
	'easy_svg_pro_import_max_entries',
	static function () {
		return 8000;
	}
);

// easy_svg_pro_import_max_total_bytes( int $bytes = 16 MB ): total read from one ZIP.
add_filter(
	'easy_svg_pro_import_max_total_bytes',
	static function () {
		return 32 * MB_IN_BYTES;
	}
);

// easy_svg_pro_import_font_max_bytes( int $bytes = 16 MB ): one SVG font, read whole.
add_filter(
	'easy_svg_pro_import_font_max_bytes',
	static function () {
		return 24 * MB_IN_BYTES;
	}
);

/*
 * easy_svg_pro_dangerous_tags( array $tags ) and easy_svg_pro_dangerous_attrs( array $attrs ).
 * Pro merges your answer with its own defaults, so this can only refuse MORE.
 * Checked against includes/configurator/configurator.php.
 */
add_filter(
	'easy_svg_pro_dangerous_tags',
	static function ( $tags ) {
		$tags   = is_array( $tags ) ? $tags : array();
		$tags[] = 'image';
		return $tags;
	}
);
