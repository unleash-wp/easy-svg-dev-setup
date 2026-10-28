<?php
/**
 * Meta Box: the field type easy_svg_icon.
 *
 * Switch "Meta Box icon field" on under Icons, Features. The field stores the
 * icon's address, `collection/slug`, as plain post meta. clone => true is not
 * supported yet.
 *
 * @package easy-svg-dev-setup
 * @license GPL-2.0-or-later
 */

add_filter(
	'rwmb_meta_boxes',
	static function ( $meta_boxes ) {
		$meta_boxes   = is_array( $meta_boxes ) ? $meta_boxes : array();
		$meta_boxes[] = array(
			'title'      => 'Feature',
			'post_types' => array( 'page' ),
			'fields'     => array(
				array(
					'id'   => 'feature_icon',
					'name' => 'Icon',
					'type' => 'easy_svg_icon',
				),
			),
		);
		return $meta_boxes;
	}
);

/*
 * In the template. Meta Box's two readers answer differently:
 *
 *     // The address, to pass on yourself.
 *     $icon = function_exists( 'rwmb_meta' ) ? rwmb_meta( 'feature_icon' ) : '';
 *     if ( is_string( $icon ) && function_exists( 'easy_svg_icon' ) ) {
 *         echo easy_svg_icon( $icon, array( 'class' => 'feature__icon' ) );
 *     }
 *
 *     // The markup, printed. Arguments reach easy_svg_icon().
 *     rwmb_the_value( 'feature_icon', array( 'label' => 'Download' ) );
 */
