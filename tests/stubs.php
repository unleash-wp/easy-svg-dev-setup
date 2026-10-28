<?php
/**
 * Just enough WordPress, Easy SVG and host plugin to load every snippet and run
 * its callbacks. A stub records calls; it never decides what the real code does.
 */

declare(strict_types=1);

define( 'ABSPATH', __DIR__ . '/' );
define( 'WP_CONTENT_DIR', '/srv/wp-content' );
define( 'MB_IN_BYTES', 1048576 );
define( 'EASY_SVG_API', 3 );
define( 'EASY_SVG_PANEL_API', 3 );
define( 'EASY_SVG_ICONS_API', 3 );

$GLOBALS['hooks'] = array();
$GLOBALS['calls'] = array();

function add_filter( $hook, $cb, $priority = 10, $args = 1 ) {
	$GLOBALS['hooks'][ $hook ][] = array( $cb, $args );
	return true;
}
function add_action( $hook, $cb, $priority = 10, $args = 1 ) {
	return add_filter( $hook, $cb, $priority, $args );
}
function add_shortcode( $tag, $cb ) {
	$GLOBALS['hooks'][ 'shortcode:' . $tag ][] = array( $cb, 1 );
}
function apply_filters( $hook, $value, ...$rest ) {
	foreach ( $GLOBALS['hooks'][ $hook ] ?? array() as $entry ) {
		$value = call_user_func_array( $entry[0], array_slice( array_merge( array( $value ), $rest ), 0, max( 1, $entry[1] ) ) );
	}
	return $value;
}
function do_action( $hook, ...$args ) {
	$GLOBALS['calls'][] = array( 'do_action', $hook, $args );
	foreach ( $GLOBALS['hooks'][ $hook ] ?? array() as $entry ) {
		call_user_func_array( $entry[0], array_slice( $args, 0, $entry[1] ) );
	}
}
function doing_action( $hook ) {
	return false;
}
function __return_false() {
	return false;
}
function __( $text, $domain = '' ) {
	return $text;
}
function esc_html__( $text, $domain = '' ) {
	return $text;
}
function esc_html( $text ) {
	return htmlspecialchars( (string) $text, ENT_QUOTES );
}
function esc_attr( $text ) {
	return htmlspecialchars( (string) $text, ENT_QUOTES );
}
function sanitize_text_field( $text ) {
	return trim( strip_tags( (string) $text ) );
}
function current_user_can( $cap ) {
	return true;
}
function plugins_url( $path, $file ) {
	return 'https://example.test/' . $path;
}
function get_stylesheet_directory() {
	return __DIR__ . '/fixtures/theme';
}
function update_post_meta( $id, $key, $value ) {
	$GLOBALS['calls'][] = array( 'update_post_meta', $id, $key );
}
function wp_enqueue_script( ...$args ) {
	$GLOBALS['calls'][] = array( 'wp_enqueue_script', $args );
}
function shortcode_atts( $pairs, $atts, $tag = '' ) {
	$atts = (array) $atts;
	$out  = array();
	foreach ( $pairs as $name => $default ) {
		$out[ $name ] = array_key_exists( $name, $atts ) ? $atts[ $name ] : $default;
	}
	return $out;
}
function register_block_type( $name, $args ) {
	$GLOBALS['calls'][] = array( 'register_block_type', $name, $args );
}

class WP_Error {
	public $code;
	public function __construct( $code = '', $message = '' ) {
		$this->code = $code;
	}
}

// Easy SVG Support: records what it was asked, renders a marker.
function easy_svg_icon( $name, $args = array() ) {
	$GLOBALS['calls'][] = array( 'easy_svg_icon', $name, $args );
	return '<svg data-icon="' . esc_attr( $name ) . '"></svg>';
}
function easy_svg_update_icon_markup( $id, $markup ) {
	return $id > 0 ? 'updated' : 'not_found';
}
function easy_svg_rename_icon( $id, $label ) {
	return '' === $label ? 'no_label' : 'renamed';
}
class Stub_Sanitizer {
	public function sanitize( $svg ) {
		return false === strpos( $svg, '<script' ) ? $svg : false;
	}
}
function easy_svg_sanitizer() {
	return new Stub_Sanitizer();
}

// Easy SVG Pro.
define( 'EASY_SVG_PRO_FILE', '/srv/plugins/easy-svg-pro/easy-svg-pro.php' );
function easy_svg_register_icon_collection( string $slug, string $label, $dir_or_icons ): void {
	$GLOBALS['calls'][] = array( 'register_collection', $slug, $label, $dir_or_icons );
}

// Abilities API.
class Stub_Ability {
	public function execute( $input = null ) {
		$GLOBALS['calls'][] = array( 'ability_execute', $input );
		return array( 'items' => array() );
	}
}
function wp_get_ability( string $name ) {
	return 'easy-svg-pro/list-icons' === $name ? new Stub_Ability() : null;
}

// Hosts.
function acf_add_local_field_group( $group ) {
	$GLOBALS['calls'][] = array( 'acf_group', $group );
}
function vc_map( $element ) {
	$GLOBALS['calls'][] = array( 'vc_map', $element );
}

require __DIR__ . '/stubs-elementor.php';
