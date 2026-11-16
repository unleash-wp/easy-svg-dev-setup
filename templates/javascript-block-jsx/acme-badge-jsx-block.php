<?php
/**
 * The JSX version of templates/javascript-block/: block.json, src/index.js in
 * JSX, built with @wordpress/scripts. build/ is committed, so the template runs
 * as it is; run `npm install && npm run build` after changing src/.
 *
 * Switch on "PHP-only blocks" under Easy SVG → Settings → Features; the same
 * switch covers JavaScript blocks. Without Pro, or with the switch off, the
 * block still loads: the sidebar falls back to a text field and the page
 * still renders the icon through the free tag.
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 */

add_action(
	'init',
	static function () {
		$asset = __DIR__ . '/build/index.asset.php';
		$built = is_readable( $asset ) ? require $asset : array(
			'dependencies' => array(),
			'version'      => '1.0.0',
		);
		$deps  = (array) $built['dependencies'];
		// Pro registers its handle on init at 20. A dependency that is not registered makes WordPress drop this script, and the block with it.
		if ( wp_script_is( 'easy-svg-pro-icon-control', 'registered' ) ) {
			$deps[] = 'easy-svg-pro-icon-control';
		}
		// The handle block.json names in editorScript, registered here so the dependency above can join it.
		wp_register_script( 'acme-badge-jsx-editor', plugins_url( 'build/index.js', __FILE__ ), $deps, (string) $built['version'], true );

		register_block_type(
			__DIR__,
			array(
				'render_callback' => static function ( $attributes ) {
					// The free tag, not Pro's: it stays when Pro is deactivated.
					$icon = function_exists( 'easy_svg_icon' )
						? easy_svg_icon( (string) ( $attributes['icon'] ?? '' ), array( 'class' => 'acme-badge__icon' ) )
						: '';
					return '<span class="acme-badge">' . $icon . esc_html( (string) ( $attributes['label'] ?? '' ) ) . '</span>';
				},
			)
		);
	},
	21
);
