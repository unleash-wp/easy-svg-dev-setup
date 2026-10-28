<?php
/**
 * Easy SVG in a theme. require_once this from functions.php.
 *
 * With Pro and "Theme & folder sources" on, the theme's assets/icons is read by
 * itself, child theme over parent. Its manifest.json names the collection
 * (here acme-theme) and the icons. Nothing has to be registered.
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 */

/**
 * One theme icon, or '' when Easy SVG is not active or the icon is unknown.
 *
 * @param string $slug Icon name inside the theme collection.
 * @param array  $args Passed to easy_svg_icon(): class, size, label, decorative.
 * @return string
 */
function acme_theme_icon( string $slug, array $args = array() ): string {
	if ( ! function_exists( 'easy_svg_icon' ) ) {
		return '';
	}
	return easy_svg_icon( 'acme-theme/' . $slug, $args );
}

/*
 * Icons kept somewhere else than assets/icons: register that folder.
 * Init at 10 or later, Pro registers its own folders at 9.
 */
add_action(
	'init',
	static function () {
		if ( function_exists( 'easy_svg_register_icon_collection' ) ) {
			easy_svg_register_icon_collection( 'acme-brand', 'ACME brand', get_stylesheet_directory() . '/resources/brand-icons' );
		}
	}
);

/*
 * In a template part:
 *
 *     <a class="acme-cta" href="/contact/">
 *         <?php echo acme_theme_icon( 'circle', array( 'class' => 'acme-cta__icon' ) ); ?>
 *         <?php esc_html_e( 'Talk to us', 'acme' ); ?>
 *     </a>
 *
 * In a block theme template, the Icon block (WordPress 7.1+) with "Multiple
 * collections" on offers the same icons. Without PHP: [easy_svg_icon name="acme-theme/circle"].
 *
 * Size from the stylesheet:
 *
 *     .acme-cta { --easy-svg-icon-size: 1.25em; }
 */
