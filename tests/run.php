<?php
/**
 * php tests/run.php
 *
 * Loads every snippet and template against stubs and runs its callbacks, so a
 * renamed hook or a wrong argument count shows up here. The hook names are
 * checked against Easy SVG Support and Easy SVG Pro by the README's table.
 */

declare(strict_types=1);

require __DIR__ . '/stubs.php';

$root   = dirname( __DIR__ );
$failed = 0;
function check( string $what, bool $ok ): void {
	global $failed;
	echo ( $ok ? 'ok   ' : 'FAIL ' ) . $what . "\n";
	$failed += $ok ? 0 : 1;
}
function calls( string $kind ): array {
	return array_values( array_filter( $GLOBALS['calls'], static fn( $c ) => $c[0] === $kind ) );
}

$files = array(
	'snippets/free/render-an-icon.php',
	'snippets/free/filter-icon-markup.php',
	'snippets/free/filter-registrable-icons.php',
	'snippets/free/sanitizer-allow-list.php',
	'snippets/free/upload-limits.php',
	'snippets/free/write-stored-icons.php',
	'snippets/free/addon-sanitizer.php',
	'snippets/free/panel-extension.php',
	'snippets/pro/register-collection.php',
	'snippets/pro/source-folders.php',
	'snippets/pro/registered-icon.php',
	'snippets/pro/icon-set-events.php',
	'snippets/pro/import-and-configurator.php',
	'snippets/pro/abilities.php',
	'templates/icon-pack-plugin/acme-icons.php',
	'templates/theme/functions-easy-svg.php',
	'templates/acf/acf-icon-field.php',
	'templates/metabox/metabox-icon-field.php',
	'templates/elementor/acme-elementor.php',
	'templates/wpbakery/wpbakery-icon-element.php',
	'templates/php-only-block/acme-feature-block.php',
	'templates/javascript-block/acme-badge-block.php',
	'templates/javascript-block-jsx/acme-badge-jsx-block.php',
);
ob_start();
foreach ( $files as $file ) {
	require $root . '/' . $file;
}
$printed = (string) ob_get_clean();

// The real order: plugins load after mu-plugins, and two snippets wait for that.
do_action( 'plugins_loaded' );

// ─── Free ───────────────────────────────────────────────────────────────────

check( 'render-an-icon: five calls, the qualified name', 5 === count( calls( 'easy_svg_icon' ) ) && 'easy-svg/heart' === calls( 'easy_svg_icon' )[0][1] );
check( 'render-an-icon: label and size reach the tag', array( 'label' => 'Liked' ) === calls( 'easy_svg_icon' )[1][2] && '24px' === calls( 'easy_svg_icon' )[2][2]['size'] );
check( 'render-an-icon: output printed', 5 === substr_count( $printed, 'data-icon="easy-svg/heart"' ) );

$out = apply_filters( 'easy_svg_icon_markup', '<svg viewBox="0 0 1 1"></svg>', 'heart', 'easy-svg' );
check( 'icon_markup: class added to the free library', str_starts_with( $out, '<svg class="acme-icon acme-icon--heart"' ) );
$keep = '<svg class="theirs"></svg>';
check( 'icon_markup: an existing class is left alone', $keep === apply_filters( 'easy_svg_icon_markup', $keep, 'heart', 'easy-svg' ) );
check( 'icon_markup: other collections untouched', 'x' === apply_filters( 'easy_svg_icon_markup', 'x', 'heart', 'other' ) );
check( 'icon_markup: acme/box served from the theme folder', str_contains( apply_filters( 'easy_svg_icon_markup', '', 'box', 'acme' ), '<path' ) );
check( 'icon_markup: acme/evil refused by the sanitizer', '' === apply_filters( 'easy_svg_icon_markup', '', 'evil', 'acme' ) );
check( 'icon_markup: a path in the slug is not read', '' === apply_filters( 'easy_svg_icon_markup', '', '../box', 'acme' ) );

$icons = apply_filters(
	'easy_svg_registrable_icons',
	array(
		array( 'slug' => 'heart', 'label' => 'Heart', 'content' => '<svg/>' ),
		array( 'slug' => 'wip-star', 'label' => 'Star', 'content' => '<svg/>' ),
	)
);
check( 'registrable_icons: wip- dropped, list kept', array( 'heart' ) === array_column( $icons, 'slug' ) );
check( 'registrable_icons: a non-list passes through', 'nope' === apply_filters( 'easy_svg_registrable_icons', 'nope' ) );

check( 'allowed_tags: feDropShadow appended', array( 'svg', 'feDropShadow' ) === apply_filters( 'esw_svg_allowed_tags', array( 'svg' ) ) );
check( 'allowed_attributes: focusable appended', in_array( 'focusable', apply_filters( 'esw_svg_allowed_attributes', array() ), true ) );
check( 'remote references: left at the default', ! isset( $GLOBALS['hooks']['esw_svg_remove_remote_references'] ) );
check( 'max_bytes: 5 MB', 5 * MB_IN_BYTES === apply_filters( 'easy_svg_max_bytes', MB_IN_BYTES ) );
check( 'max_use_tags: 2000', 2000 === apply_filters( 'easy_svg_max_use_tags', 1000 ) );

check( 'write-stored-icons: both verdicts', array( 'markup' => 'updated', 'label' => 'renamed' ) === acme_replace_icon( 7, '<svg/>', ' New ' ) );
check( 'addon-sanitizer: clean passes, script refused', '<svg/>' === acme_clean_svg( '<svg/>' ) && '' === acme_clean_svg( '<svg><script/></svg>' ) );
check( 'addon-sanitizer: over the size bound is refused', '' === acme_clean_svg( '<svg>' . str_repeat( ' ', 2 * MB_IN_BYTES ) . '</svg>' ) );
check( 'addon-sanitizer: too complex is refused', '' === acme_clean_svg( '<svg>' . str_repeat( '<use/>', 4 ) . '</svg>' ) );
check( 'addon-sanitizer: the result is hardened', '<svg></svg>' === acme_clean_svg( '<svg><!--x--></svg>' ) );

do_action( 'easy_svg_panel_enqueue', 'easy-svg-panel' );
check( 'panel_enqueue: our script depends on the handle', array( 'easy-svg-panel' ) === calls( 'wp_enqueue_script' )[0][1][2] );
$tabs = apply_filters( 'easy_svg_panel_menu_tabs', array( 'settings' => 'Settings' ) );
check( 'panel_menu_tabs: tab with label and capability', 'Settings' === $tabs['settings'] && 'edit_others_posts' === $tabs['acme']['capability'] );
check( 'pro_url: replaced', 'https://example.com/our-icon-offer' === apply_filters( 'easy_svg_pro_url', 'https://unleash-wp.com' ) );

// ─── Pro ────────────────────────────────────────────────────────────────────

do_action( 'init' );
$reg = calls( 'register_collection' );
check( 'register-collection: folder, inline set, theme brand folder', array( 'acme', 'acme-ui', 'acme-brand' ) === array_column( $reg, 1 ) );
check( 'register-collection: inline set is slug => markup', is_array( $reg[1][3] ) && isset( $reg[1][3]['close'] ) );

$dirs = apply_filters( 'easy_svg_pro_source_dirs', array( '/a' ) );
check( 'source_dirs: kept the list, added the shared folder', '/a' === $dirs[0] && in_array( '/srv/wp-content/shared-icons/acme-brand', $dirs, true ) );
check( 'source_dirs: a broken list becomes an array', is_array( apply_filters( 'easy_svg_pro_source_dirs', 'broken' ) ) );
check( 'source_dirs: pack plugin adds one folder per collection', in_array( $root . '/templates/icon-pack-plugin/icons/starter-shapes', $dirs, true ) );
check( 'source_max_icons: never lowers', 1400 === apply_filters( 'easy_svg_pro_source_max_icons', 1000 ) && 5000 === apply_filters( 'easy_svg_pro_source_max_icons', 5000 ) );
check( 'source_file_max_bytes: never lowers', 3 * MB_IN_BYTES === apply_filters( 'easy_svg_pro_source_file_max_bytes', 2 * MB_IN_BYTES ) && 9 * MB_IN_BYTES === apply_filters( 'easy_svg_pro_source_file_max_bytes', 9 * MB_IN_BYTES ) );
check( 'theme_collection_slug: renamed', 'acme-theme' === apply_filters( 'easy_svg_pro_theme_collection_slug', 'theme' ) );

check( 'registered_icon: thinner stroke in acme-outline', 'stroke-width="1.5"' === apply_filters( 'easy_svg_pro_registered_icon', 'stroke-width="2"', 'acme-outline/home' ) );
check( 'registered_icon: other collections untouched', 'stroke-width="2"' === apply_filters( 'easy_svg_pro_registered_icon', 'stroke-width="2"', 'acme/home' ) );

do_action( 'easy_svg_pro_set_icons_writing', 12 );
do_action( 'easy_svg_pro_set_icons_written', 12, array( 'a' => array(), 'b' => array() ) );
check( 'set_icons_writing: meta stamped', 12 === calls( 'update_post_meta' )[0][1] );
$changed = array_values( array_filter( calls( 'do_action' ), static fn( $c ) => 'acme_icon_set_changed' === $c[1] ) );
check( 'set_icons_written: two args reach the callback', array( 12, 2 ) === $changed[0][2] );
check( 'set_write_context: true stays true', true === apply_filters( 'easy_svg_pro_set_write_context', true ) );
check( 'set_screen_redirect: off', false === apply_filters( 'easy_svg_pro_set_screen_redirect', true, null ) );

check( 'import limits: three raised', 8000 === apply_filters( 'easy_svg_pro_import_max_entries', 5000 ) && 32 * MB_IN_BYTES === apply_filters( 'easy_svg_pro_import_max_total_bytes', 0 ) && 24 * MB_IN_BYTES === apply_filters( 'easy_svg_pro_import_font_max_bytes', 0 ) );
check( 'dangerous_tags: only adds', array( 'script', 'image' ) === apply_filters( 'easy_svg_pro_dangerous_tags', array( 'script' ) ) );

acme_find_icons( 'heart' );
check( 'abilities: list-icons with search', 'heart' === calls( 'ability_execute' )[0][1]['search'] );

// ─── Templates ──────────────────────────────────────────────────────────────

ob_start();
do_action( 'admin_notices' );
check( 'pack plugin: no notice while Pro is active', '' === ob_get_clean() );

check( 'theme: acme_theme_icon qualifies the slug', str_contains( acme_theme_icon( 'circle' ), 'acme-theme/circle' ) );

do_action( 'acf/include_fields' );
$field = calls( 'acf_group' )[0][1]['fields'][0];
check( 'acf: icon_picker field with the easy_svg tab', 'icon_picker' === $field['type'] && array( 'easy_svg' ) === $field['tabs'] );
check( 'acf: array format renders ours', str_contains( acme_acf_icon( array( 'type' => 'easy_svg', 'value' => 'acme/star' ) ), 'acme/star' ) );
check( 'acf: string format renders', str_contains( acme_acf_icon( 'acme/star' ), 'acme/star' ) );
check( 'acf: another tab is not ours', '' === acme_acf_icon( array( 'type' => 'url', 'value' => 'https://x.test/a/b.svg' ) ) );
check( 'acf: empty and bare slug render nothing', '' === acme_acf_icon( '' ) && '' === acme_acf_icon( 'star' ) && '' === acme_acf_icon( null ) );

$boxes = apply_filters( 'rwmb_meta_boxes', array() );
check( 'metabox: field type easy_svg_icon', 'easy_svg_icon' === $boxes[0]['fields'][0]['type'] );

$manager = new class() {
	public $widgets = array();
	public function register( $widget ) {
		$this->widgets[] = $widget;
	}
};
do_action( 'elementor/widgets/register', $manager );
$widget = $manager->widgets[0];
$widget->run_register_controls();
check( 'elementor: ICONS control', 'icons' === $widget->controls['icon']['type'] );
$widget->settings = array(
	'icon'    => array( 'value' => 'acme/star', 'library' => 'esvg-acme' ),
	'caption' => '<b>Hi</b>',
);
ob_start();
$widget->run_render();
$html = (string) ob_get_clean();
check( 'elementor: renders through Icons_Manager, caption escaped', 'acme/star' === calls( 'render_icon' )[0][1]['value'] && str_contains( $html, '&lt;b&gt;' ) );

do_action( 'vc_before_init' );
check( 'wpbakery: param type easy_svg_icon', 'easy_svg_icon' === calls( 'vc_map' )[0][1]['params'][0]['type'] );
$sc = $GLOBALS['hooks']['shortcode:acme_feature'][0][0];
check( 'wpbakery: shortcode renders the icon', str_contains( $sc( array( 'icon' => 'acme/star', 'title' => 'T' ) ), 'acme/star' ) );

$block = calls( 'register_block_type' )[0];
check( 'php-only block: autoRegister and the icon marker', true === $block[2]['supports']['autoRegister'] && 'icon' === $block[2]['attributes']['iconName']['unleashwp']['component'] );
check( 'php-only block: render_callback uses the free tag', str_contains( $block[2]['render_callback']( array( 'iconName' => 'acme/star' ) ), 'acme/star' ) );

// The JavaScript block, once without Pro (the init above) and once with Pro's handle registered.
$badge = array_values( array_filter( calls( 'register_block_type' ), static fn( $c ) => 'acme/badge' === $c[1] ) );
$scripts = array_values( array_filter( calls( 'wp_register_script' ), static fn( $c ) => 'acme-badge-editor' === $c[1] ) );
check( 'js block: registered with its editor script', 1 === count( $badge ) && 'acme-badge-editor' === $badge[0][2]['editor_script'] );
check( 'js block: without Pro no dependency on its handle', 1 === count( $scripts ) && ! in_array( 'easy-svg-pro-icon-control', $scripts[0][3], true ) );
check( 'js block: render_callback uses the free tag', str_contains( $badge[0][2]['render_callback']( array( 'icon' => 'acme/star', 'label' => '<b>' ) ), 'acme/star' ) );
check( 'js block: the label is escaped', str_contains( $badge[0][2]['render_callback']( array( 'icon' => '', 'label' => '<b>' ) ), '&lt;b&gt;' ) );
$GLOBALS['registered_scripts'] = array( 'easy-svg-pro-icon-control' );
foreach ( $GLOBALS['hooks']['init'] as $entry ) {
	if ( $entry[0] instanceof Closure && str_contains( (string) ( new ReflectionFunction( $entry[0] ) )->getFileName(), 'javascript-block' ) ) {
		$entry[0]();
	}
}
$scripts = array_values( array_filter( calls( 'wp_register_script' ), static fn( $c ) => 'acme-badge-editor' === $c[1] ) );
check( 'js block: with Pro it depends on the icon control', in_array( 'easy-svg-pro-icon-control', end( $scripts )[3], true ) );
$editor = (string) file_get_contents( $root . '/templates/javascript-block/editor.js' );
check( 'js block: sidebar and toolbar use the Pro components, with a fallback', str_contains( $editor, 'pro.IconControl' ) && str_contains( $editor, 'pro.IconToolbarButton' ) && str_contains( $editor, 'C.TextControl' ) );

// The JSX version: block.json names the handle, PHP registers it from the built asset file.
$jsx_dir = $root . '/templates/javascript-block-jsx';
$meta    = (array) json_decode( (string) file_get_contents( $jsx_dir . '/block.json' ), true );
$jsx     = array_values( array_filter( calls( 'register_block_type' ), static fn( $c ) => $jsx_dir === $c[1] ) );
$jsx_js  = array_values( array_filter( calls( 'wp_register_script' ), static fn( $c ) => 'acme-badge-jsx-editor' === $c[1] ) );
$built   = require $jsx_dir . '/build/index.asset.php';
check( 'jsx block: block.json names the editor script PHP registers', 'acme-badge-jsx-editor' === ( $meta['editorScript'] ?? '' ) && 'acme/badge-jsx' === ( $meta['name'] ?? '' ) );
check( 'jsx block: registered from its folder with a render_callback', count( $jsx ) >= 1 && is_callable( $jsx[0][2]['render_callback'] ?? null ) );
check( 'jsx block: the built script ships with its asset file', is_file( $jsx_dir . '/build/index.js' ) && in_array( 'wp-block-editor', $built['dependencies'], true ) );
check( 'jsx block: the script takes the built dependencies', ! array_diff( $built['dependencies'], $jsx_js[0][3] ?? array() ) );
check( 'jsx block: without Pro no dependency on its handle', ! in_array( 'easy-svg-pro-icon-control', $jsx_js[0][3] ?? array( 'easy-svg-pro-icon-control' ), true ) );
check( 'jsx block: with Pro it depends on the icon control', in_array( 'easy-svg-pro-icon-control', end( $jsx_js )[3], true ) );
check( 'jsx block: render_callback uses the free tag and escapes the label', str_contains( $jsx[0][2]['render_callback']( array( 'icon' => 'acme/star', 'label' => '<b>' ) ), 'acme/star' ) && str_contains( $jsx[0][2]['render_callback']( array( 'icon' => '', 'label' => '<b>' ) ), '&lt;b&gt;' ) );
$jsx_src = (string) file_get_contents( $jsx_dir . '/src/index.js' );
check( 'jsx block: JSX source uses both Pro components, with a fallback', str_contains( $jsx_src, '<IconControl' ) && str_contains( $jsx_src, '<IconToolbarButton' ) && str_contains( $jsx_src, '<TextControl' ) );
check( 'jsx block: the build is current (src names what the build holds)', str_contains( (string) file_get_contents( $jsx_dir . '/build/index.js' ), 'IconToolbarButton' ) );

// A hook snippet drops into mu-plugins as it is; render-an-icon prints and must not.
foreach ( glob( $root . '/snippets/*/*.php' ) as $file ) {
	$has = 1 === preg_match( '/^ \* Plugin Name: /m', (string) file_get_contents( $file ) );
	check( 'header: ' . basename( $file ), 'render-an-icon.php' === basename( $file ) ? ! $has : $has );
}

echo $failed ? "\n$failed failed\n" : "\nall passed\n";
exit( $failed ? 1 : 0 );
