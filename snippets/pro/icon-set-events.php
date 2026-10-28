<?php
/**
 * Plugin Name: Easy SVG Pro snippet: icon set events
 *
 * Hear about every change to an icon set. Every path that writes a set comes
 * through one function: the editor, bulk editing, the importers, WP-CLI and
 * client mode.
 *
 * easy_svg_pro_set_icons_writing( int $post_id ): before, the old icons still stored.
 * easy_svg_pro_set_icons_written( int $post_id, array $icons ): after.
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 */

add_action(
	'easy_svg_pro_set_icons_writing',
	static function ( $post_id ) {
		update_post_meta( (int) $post_id, '_acme_icons_touched', time() );
	}
);

// Tell a deploy pipeline or a cache which set changed.
add_action(
	'easy_svg_pro_set_icons_written',
	static function ( $post_id, $icons ) {
		do_action( 'acme_icon_set_changed', (int) $post_id, is_array( $icons ) ? count( $icons ) : 0 );
	},
	10,
	2
);

/*
 * easy_svg_pro_set_write_context( bool $locks ): true for REST writes and
 * WP-CLI. Return true from code of your own that writes sets outside those two,
 * so two writes cannot undo each other.
 */
add_filter(
	'easy_svg_pro_set_write_context',
	static function ( $locks ) {
		return $locks || doing_action( 'acme_nightly_icon_sync' );
	}
);

/*
 * easy_svg_pro_set_screen_redirect( bool $redirect, WP_Post|null $set ): false
 * keeps the classic set screen instead of handing over to the library grid.
 */
add_filter( 'easy_svg_pro_set_screen_redirect', '__return_false' );
