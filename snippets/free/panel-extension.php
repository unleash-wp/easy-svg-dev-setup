<?php
/**
 * Plugin Name: Easy SVG snippet: admin screen extension
 *
 * Add a tab and a script of your own to the Easy SVG admin screens. Check
 * EASY_SVG_PANEL_API first: a filter cannot be probed for. A capability per
 * tab needs 2 or higher.
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 */

// plugins_loaded, because a mu-plugin loads before the plugin defines the constant.
add_action(
	'plugins_loaded',
	static function () {
		if ( ! defined( 'EASY_SVG_PANEL_API' ) || EASY_SVG_PANEL_API < 2 ) {
			return;
		}

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
		 * easy_svg_panel_menu_tabs( array $tabs ): tab id => label, or with API 2 or higher
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
);

// easy_svg_pro_url( string $url ): the add-on link in the plugin row, shown while Pro is missing.
add_filter(
	'easy_svg_pro_url',
	static function () {
		return 'https://example.com/our-icon-offer';
	}
);
