<?php
// Elementor's three classes the widget template touches.

namespace Elementor;

class Controls_Manager {
	const ICONS = 'icons';
	const TEXT  = 'text';
}
class Icons_Manager {
	public static function render_icon( $icon, $attributes = array(), $tag = 'i' ) {
		$GLOBALS['calls'][] = array( 'render_icon', $icon, $attributes );
		echo '<svg></svg>';
		return true;
	}
}
abstract class Widget_Base {
	public $controls = array();
	public $settings = array();
	protected function start_controls_section( $id, $args = array() ) {}
	protected function end_controls_section() {}
	protected function add_control( $id, $args ) {
		$this->controls[ $id ] = $args;
	}
	public function get_settings_for_display() {
		return $this->settings;
	}
	public function run_register_controls() {
		$this->register_controls();
	}
	public function run_render() {
		$this->render();
	}
}
