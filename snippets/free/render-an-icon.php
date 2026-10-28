<?php
/**
 * Render a stored icon: easy_svg_icon( string $name, array $args = array() ): string
 *
 * $name is a bare slug (the free library) or `collection/slug`. The answer is
 * '' for an unknown icon, so a template never prints a broken box.
 *
 * Shortcode, same arguments: [easy_svg_icon name="easy-svg/heart" size="24px" label="Liked"]
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 */

// Guarded: a theme must survive the plugin being deactivated.
if ( function_exists( 'easy_svg_icon' ) ) {

	// Decorative by default: aria-hidden="true", 1em square, follows the text.
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

	// 'size' => '' leaves the size to your CSS entirely.
	echo easy_svg_icon( 'easy-svg/heart', array( 'size' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput

	// Meaningful, named from the stored title rather than a label you pass.
	echo easy_svg_icon( 'easy-svg/heart', array( 'decorative' => false ) ); // phpcs:ignore WordPress.Security.EscapeOutput
}

/*
 * The default size for every icon, in your stylesheet:
 *
 *     .acme-button { --easy-svg-icon-size: 1.5rem; }
 */
