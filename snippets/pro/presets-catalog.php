<?php
/**
 * Plugin Name: Easy SVG Pro snippet: templates catalog
 *
 * Where the Templates screen (ready-made sets) gets its catalog. For a staging
 * catalog or an end-to-end test, not for production: a set is only downloaded
 * from unleash-wp.com or the host the endpoint filter names, and the site's
 * token travels to that host.
 *
 * easy_svg_pro_presets_endpoint( string $url )
 * easy_svg_pro_presets_token( string $token ): normally the site token of the
 *   bundled updater. A Git checkout has no updater, so a test sets one here.
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 */

if ( defined( 'ACME_PRESETS_STAGING_URL' ) ) {
	add_filter(
		'easy_svg_pro_presets_endpoint',
		static function () {
			return (string) ACME_PRESETS_STAGING_URL;
		}
	);
}

if ( defined( 'ACME_PRESETS_TEST_TOKEN' ) ) {
	add_filter(
		'easy_svg_pro_presets_token',
		static function () {
			return (string) ACME_PRESETS_TEST_TOKEN;
		}
	);
}
