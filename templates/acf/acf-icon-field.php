<?php
/**
 * ACF (6.3+) or Secure Custom Fields: an Icon Picker field with Easy SVG icons.
 *
 * Pro adds an "Easy SVG" tab to ACF's own icon_picker field type. Switch "ACF
 * icon picker" on under Icons, Features. The field stores the icon's address,
 * `collection/slug`, which easy_svg_icon() takes as it is.
 *
 * Drop this file into a plugin or require it from functions.php.
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 */

add_action(
	'acf/include_fields',
	static function () {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}
		acf_add_local_field_group(
			array(
				'key'      => 'group_acme_feature',
				'title'    => 'Feature',
				'fields'   => array(
					array(
						'key'           => 'field_acme_feature_icon',
						'label'         => 'Icon',
						'name'          => 'feature_icon',
						'type'          => 'icon_picker',
						// A field that lists its tabs shows only those. Keep easy_svg in.
						'tabs'          => array( 'easy_svg' ),
						'return_format' => 'array',
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'page',
						),
					),
				),
			)
		);
	}
);

/**
 * One icon_picker value as markup, under either return format.
 *
 * 'array' gives { type, value } and only type easy_svg is ours: a URL from
 * another tab has a slash too. 'string' gives the value alone.
 *
 * @param mixed $value What get_field() returned.
 * @param array $args  Passed to easy_svg_icon(): class, size, label, decorative.
 * @return string
 */
function acme_acf_icon( $value, array $args = array() ): string {
	if ( is_array( $value ) ) {
		if ( 'easy_svg' !== ( $value['type'] ?? null ) ) {
			return '';
		}
		$value = $value['value'] ?? '';
	}
	$name = '/^[a-z0-9](?:[a-z0-9_-]*[a-z0-9])?\/[a-z0-9](?:[a-z0-9_-]*[a-z0-9])?$/';
	if ( ! is_string( $value ) || 1 !== preg_match( $name, trim( $value ) ) || ! function_exists( 'easy_svg_icon' ) ) {
		return '';
	}
	return easy_svg_icon( trim( $value ), $args );
}

/*
 * In the template:
 *
 *     echo acme_acf_icon( get_field( 'feature_icon' ), array( 'class' => 'feature__icon' ) );
 */
