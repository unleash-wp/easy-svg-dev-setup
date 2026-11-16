<?php
/**
 * The JSX version of templates/javascript-block/, WooCommerce style:
 * src/index.js imports IconControl and IconToolbarButton from
 * `@unleash-wp/easy-svg-pro`, and webpack.config.js maps that import to Easy
 * SVG Pro's script. build/ is committed, so the template runs as it is; run
 * `npm install && npm run build` after changing src/.
 *
 * The editor needs Easy SVG Pro with "PHP-only blocks" switched on under
 * Easy SVG → Settings → Features. The page is drawn by render.php; icons of
 * your sets and of WordPress's own collection need Pro active there too (an
 * ended license is enough), free library icons need only Easy SVG (free).
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 */

add_action(
	'init',
	static function () {
		// block.json in build/ brings the editor script, its asset file and render.php.
		register_block_type( __DIR__ . '/build' );
	}
);
