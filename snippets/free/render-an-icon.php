<?php
/**
 * Print a stored icon: easy_svg_icon( string $name, array $args = array() ): string
 *
 * $name is `collection/slug`; an unknown icon gives ''. $args: class, size,
 * label, decorative.
 * Shortcode: [easy_svg_icon name="easy-svg/heart" size="24px" label="Liked"]
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 */

// Guarded, so the theme keeps working while the plugin is deactivated.
if ( function_exists( 'easy_svg_icon' ) ) {

	// Decorative: aria-hidden="true", a 1em square that grows with the text.
	echo easy_svg_icon( 'easy-svg/heart' ); // phpcs:ignore WordPress.Security.EscapeOutput

	// A label makes it content: role="img" and aria-label.
	echo easy_svg_icon( 'easy-svg/heart', array( 'label' => __( 'Liked', 'acme' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput

	// A fixed size and a class of your own.
	echo easy_svg_icon( // phpcs:ignore WordPress.Security.EscapeOutput
		'easy-svg/heart',
		array(
			'size'  => '24px',
			'class' => 'acme-button__icon',
		)
	);

	// 'size' => '' prints no size at all, so your CSS decides.
	echo easy_svg_icon( 'easy-svg/heart', array( 'size' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput

	// Meaningful without a label: the icon's stored name becomes the aria-label.
	echo easy_svg_icon( 'easy-svg/heart', array( 'decorative' => false ) ); // phpcs:ignore WordPress.Security.EscapeOutput
}

/*
 * Default size in your stylesheet: .acme-button { --easy-svg-icon-size: 1.5rem; }
 * An SVG with width or height on its root keeps that size and ignores both.
 */
