<?php
/**
 * Switch on what the smoke test needs: wp eval-file tests/smoke-setup.php
 *
 * A separate request, because the features are read once while WordPress boots.
 */

$features = (array) get_option( 'easy_svg_pro_features', array() );
foreach ( array( 'sources', 'collections', 'icon_field_acf', 'metabox', 'elementor', 'wpbakery', 'icon_field_blocks', 'abilities' ) as $key ) {
	$features[ $key ] = true;
}
update_option( 'easy_svg_pro_features', $features );

if ( defined( 'EASY_SVG_SETTINGS_OPTION' ) && function_exists( 'easy_svg_sanitize_settings' ) ) {
	$settings = array_merge( (array) easy_svg_settings(), array( 'icons' => true ) );
	update_option( EASY_SVG_SETTINGS_OPTION, easy_svg_sanitize_settings( $settings ) );
}

WP_CLI::success( 'features on' );
