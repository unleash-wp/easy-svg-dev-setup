<?php
/**
 * Plugin Name:       ACME Icons
 * Description:       Icon collections for Easy SVG Support Pro. Every folder under icons/ is one collection.
 * Version:           1.0.0
 * Requires at least: 6.6
 * Requires PHP:      7.4
 * Update URI:        false
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       acme-icons
 */

declare(strict_types=1);

namespace Acme\Icons;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * One folder per collection, the layout Pro's own "export as plugin" writes.
 * Pro keys a folder by its last path segment, so a flat icons/ would collide
 * with every other pack built the same way.
 */
add_filter(
	'easy_svg_pro_source_dirs',
	// Untyped and cast: another pack may have broken this list before us.
	static function ( $dirs ): array {
		$dirs  = is_array( $dirs ) ? $dirs : array();
		$found = glob( __DIR__ . '/icons/*', GLOB_ONLYDIR );
		return array_merge( $dirs, is_array( $found ) ? $found : array() );
	}
);

/*
 * Pro reads at most 1000 icons per folder. Set this to what the biggest folder
 * really holds, or delete the block when every folder is under 1000.
 */
add_filter(
	'easy_svg_pro_source_max_icons',
	static function ( $max ): int {
		return max( (int) $max, 1000 );
	}
);

// Without Pro the plugin would activate, show nothing and give no reason.
add_action(
	'admin_notices',
	static function (): void {
		if ( ! current_user_can( 'activate_plugins' ) || defined( 'EASY_SVG_PRO_FILE' ) ) {
			return;
		}
		printf(
			'<div class="notice notice-warning"><p>%s</p></div>',
			esc_html__( 'ACME Icons needs Easy SVG Support Pro. Its icons stay hidden until Pro is active.', 'acme-icons' )
		);
	}
);
