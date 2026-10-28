<?php
/**
 * Elementor: a widget of your own with an icon control. Loaded by
 * acme-elementor.php once Elementor is there.
 *
 * Nothing Easy SVG specific in here. Switch "Elementor icon picker"
 * on under Icons, Features and every Controls_Manager::ICONS control, yours
 * included, gets a tab per collection. Icons_Manager::render_icon() then prints
 * the inline SVG. Every Elementor icon widget works the same way.
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * One icon and a caption.
 */
class Acme_Icon_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'acme_icon';
	}

	public function get_title() {
		return esc_html__( 'ACME Icon', 'acme' );
	}

	public function get_icon() {
		return 'eicon-favorite';
	}

	public function get_categories() {
		return array( 'general' );
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'Content', 'acme' ) ) );
		$this->add_control(
			'icon',
			array(
				'label'   => esc_html__( 'Icon', 'acme' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => '',
					'library' => '',
				),
			)
		);
		$this->add_control(
			'caption',
			array(
				'label' => esc_html__( 'Caption', 'acme' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		echo '<div class="acme-icon">';
		\Elementor\Icons_Manager::render_icon( $settings['icon'], array( 'aria-hidden' => 'true' ) );
		echo '<span>' . esc_html( (string) ( $settings['caption'] ?? '' ) ) . '</span>';
		echo '</div>';
	}
}
