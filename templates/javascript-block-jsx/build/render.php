<?php
/**
 * The page output of acme/badge-jsx. It needs only Easy SVG (free): with Pro
 * deactivated the icon is still drawn.
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 *
 * @var array $attributes The block's attributes.
 */

// The free tag, not Pro's: it stays when Pro is deactivated.
$acme_icon = function_exists( 'easy_svg_icon' )
	? easy_svg_icon( (string) ( $attributes['icon'] ?? '' ), array( 'class' => 'acme-badge__icon' ) )
	: '';
?>
<span <?php echo get_block_wrapper_attributes( array( 'class' => 'acme-badge' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escapes it. ?>>
	<?php echo $acme_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- easy_svg_icon() returns hardened SVG. ?>
	<?php echo esc_html( (string) ( $attributes['label'] ?? '' ) ); ?>
</span>
