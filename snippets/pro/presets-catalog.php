<?php
/**
 * The preset catalog, for a staging catalog or an end to end test. Not for
 * production: a pack is only downloaded from unleash-wp.com or the host the
 * endpoint filter names, and the token travels to that host.
 *
 * easy_svg_pro_presets_endpoint( string $url )
 * easy_svg_pro_presets_token( string $token ): normally the bundled updater's
 *   site token; this exists for a checkout, which has no updater.
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
