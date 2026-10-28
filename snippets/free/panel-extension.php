<?php
/**
 * Extend the Icons panel. Check EASY_SVG_PANEL_API (3) first: these hooks cannot
 * be probed, and an older plugin answers them differently.
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 */

if ( defined( 'EASY_SVG_PANEL_API' ) && EASY_SVG_PANEL_API >= 2 ) {

	/*
	 * easy_svg_panel_enqueue( string $handle ): the panel script is registered.
	 * Depend on it and your bundle loads after it, into the same panel.
	 */
	add_action(
		'easy_svg_panel_enqueue',
		static function ( $handle ) {
			wp_enqueue_script(
				'acme-icons-panel',
				plugins_url( 'build/panel.js', __FILE__ ),
				array( (string) $handle ),
				'1.0.0',
				true
			);
		}
	);

	/*
	 * easy_svg_panel_menu_tabs( array $tabs ): tab id => label, or since API 2
	 * tab id => array( 'label' => …, 'capability' => … ). A plain label means
	 * manage_options.
	 */
	add_filter(
		'easy_svg_panel_menu_tabs',
		static function ( $tabs ) {
			$tabs         = is_array( $tabs ) ? $tabs : array();
			$tabs['acme'] = array(
				'label'      => __( 'ACME', 'acme' ),
				'capability' => 'edit_others_posts',
			);
			return $tabs;
		}
	);
}

// easy_svg_pro_url( string $url ): the add-on link in the plugin row, shown while Pro is missing.
add_filter(
	'easy_svg_pro_url',
	static function () {
		return 'https://example.com/our-icon-offer';
	}
);
