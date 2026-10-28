<?php
/**
 * The templates in a real WordPress: wp eval-file tests/smoke-wordpress.php --user=admin
 *
 * Needs Easy SVG Support, Easy SVG Pro with a licence, ACF, Meta Box, Elementor
 * and WPBakery active, the acme-icons plugin, a theme carrying
 * templates/theme/, and the templates loaded (see README, Checks). Turn the
 * features on first with tests/smoke-setup.php.
 */

$failed = 0;
$check  = static function ( string $what, bool $ok ) use ( &$failed ): void {
	WP_CLI::log( ( $ok ? 'ok   ' : 'FAIL ' ) . $what );
	$failed += $ok ? 0 : 1;
};
$is_svg = static function ( $html ): bool {
	return is_string( $html ) && false !== strpos( $html, '<svg' );
};

$check( 'pack plugin: starter-shapes/circle renders', $is_svg( easy_svg_icon( 'starter-shapes/circle' ) ) );
$check( 'theme: acme_theme_icon( circle ) renders', $is_svg( acme_theme_icon( 'circle' ) ) );
$check( 'unknown icon renders nothing', '' === easy_svg_icon( 'starter-shapes/nope' ) );

// ACF.
$field = function_exists( 'acf_get_field' ) ? acf_get_field( 'field_acme_feature_icon' ) : null;
$check( 'acf: field registered with the easy_svg tab', is_array( $field ) && in_array( 'easy_svg', (array) $field['tabs'], true ) );
$check( 'acf: Pro offers the easy_svg tab', array_key_exists( 'easy_svg', (array) apply_filters( 'acf/fields/icon_picker/tabs', array() ) ) );
$check(
	'acf: array value renders',
	$is_svg(
		acme_acf_icon(
			array(
				'type'  => 'easy_svg',
				'value' => 'starter-shapes/circle',
			)
		)
	)
);

// Meta Box.
$check( 'metabox: easy_svg_icon resolves to Pro\'s field class', 'RWMB_Input_Field' !== apply_filters( 'rwmb_field_class', 'RWMB_Input_Field', 'easy_svg_icon' ) );
$page = wp_insert_post(
	array(
		'post_type'   => 'page',
		'post_status' => 'publish',
		'post_title'  => 'Smoke',
	)
);
update_post_meta( $page, 'feature_icon', 'starter-shapes/square' );
$check( 'metabox: rwmb_meta gives the address', 'starter-shapes/square' === rwmb_meta( 'feature_icon', array(), $page ) );
$check( 'metabox: rwmb_the_value gives the markup', $is_svg( rwmb_the_value( 'feature_icon', array(), $page, false ) ) );

// Elementor.
$types = \Elementor\Plugin::instance()->widgets_manager->get_widget_types();
$check( 'elementor: acme_icon widget registered', isset( $types['acme_icon'] ) );
$html = \Elementor\Icons_Manager::try_get_icon_html(
	array(
		'library' => 'esvg-starter-shapes',
		'value'   => \UWP\EasySvgPro\elementor_icon_prefix( 'starter-shapes' ) . 'triangle-up',
	)
);
$check( 'elementor: Icons_Manager renders our library as SVG', $is_svg( $html ) );

// WPBakery.
$check( 'wpbakery: acme_feature mapped', class_exists( 'WPBMap' ) && false !== WPBMap::getShortCode( 'acme_feature' ) );
$check( 'wpbakery: shortcode renders the icon', $is_svg( do_shortcode( '[acme_feature icon="starter-shapes/circle" title="Hi"]' ) ) );

// PHP-only block.
$check( 'php-only block: registered', WP_Block_Type_Registry::get_instance()->is_registered( 'acme/feature' ) );
$check(
	'php-only block: renders the icon',
	$is_svg(
		render_block(
			array(
				'blockName' => 'acme/feature',
				'attrs'     => array(
					'iconName' => 'starter-shapes/circle',
					'title'    => 'Hi',
				),
			)
		)
	)
);

// Abilities.
$ability = function_exists( 'wp_get_ability' ) ? wp_get_ability( 'easy-svg-pro/list-icons' ) : null;
$result  = null !== $ability ? $ability->execute( array( 'search' => 'circle' ) ) : null;
$check( 'abilities: list-icons finds circle', is_array( $result ) && $result['total'] >= 1 );

wp_delete_post( $page, true );

if ( $failed ) {
	WP_CLI::error( "$failed failed" );
}
WP_CLI::success( 'all passed' );
