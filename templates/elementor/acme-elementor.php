<?php
/**
 * Loads the widget only once Elementor is there: a class extending
 * \Elementor\Widget_Base is a fatal on any site without it.
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 */

add_action(
	'elementor/widgets/register',
	static function ( $widgets_manager ) {
		require_once __DIR__ . '/class-acme-icon-widget.php';
		$widgets_manager->register( new Acme_Icon_Widget() );
	}
);
