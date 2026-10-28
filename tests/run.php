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
	'snippets/pro/presets-catalog.php',
	'snippets/pro/abilities.php',
	'templates/icon-pack-plugin/acme-icons.php',
	'templates/theme/functions-easy-svg.php',
	'templates/acf/acf-icon-field.php',
	'templates/metabox/metabox-icon-field.php',
	'templates/elementor/acme-elementor.php',
	'templates/wpbakery/wpbakery-icon-element.php',
	'templates/php-only-block/acme-feature-block.php',
);
ob_start();
foreach ( $files as $file ) {
	require $root . '/' . $file;
}
$printed = (string) ob_get_clean();

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

check( 'allowed_tags: view appended', array( 'svg', 'view' ) === apply_filters( 'esw_svg_allowed_tags', array( 'svg' ) ) );
check( 'allowed_attributes: focusable appended', in_array( 'focusable', apply_filters( 'esw_svg_allowed_attributes', array() ), true ) );
check( 'remote references: left at the default', ! isset( $GLOBALS['hooks']['esw_svg_remove_remote_references'] ) );
check( 'max_mb_ceiling: 40', 40 === apply_filters( 'easy_svg_max_mb_ceiling', 20 ) );
check( 'max_bytes: 5 MB', 5 * MB_IN_BYTES === apply_filters( 'easy_svg_max_bytes', MB_IN_BYTES ) );
check( 'max_use_tags: 2000', 2000 === apply_filters( 'easy_svg_max_use_tags', 1000 ) );

check( 'write-stored-icons: both verdicts', array( 'markup' => 'updated', 'label' => 'renamed' ) === acme_replace_icon( 7, '<svg/>', ' New ' ) );
check( 'addon-sanitizer: clean passes, script refused', '<svg/>' === acme_clean_svg( '<svg/>' ) && '' === acme_clean_svg( '<svg><script/></svg>' ) );

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
check( 'presets: no filter without the constants', ! isset( $GLOBALS['hooks']['easy_svg_pro_presets_endpoint'] ) && ! isset( $GLOBALS['hooks']['easy_svg_pro_presets_token'] ) );

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

echo $failed ? "\n$failed failed\n" : "\nall passed\n";
exit( $failed ? 1 : 0 );
