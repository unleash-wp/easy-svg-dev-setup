/**
 * acme/badge: an icon and a label. The icon is chosen in the sidebar and from
 * the toolbar with Easy SVG Pro; without Pro the sidebar shows a text field.
 * Plain script on purpose, so the template runs without a build step.
 */
( function ( wp ) {
	var el = wp.element.createElement;
	var be = wp.blockEditor;
	var C = wp.components;
	var pro = window.EasySvgPro || {};

	function Sidebar( props ) {
		var a = props.attributes;
		var setIcon = function ( icon ) {
			props.setAttributes( { icon: icon } );
		};
		return el(
			be.InspectorControls,
			null,
			el(
				C.PanelBody,
				{ title: 'Badge' },
				pro.IconControl
					? el( pro.IconControl, {
						label: 'Icon',
						help: 'Shown in front of the label.',
						value: a.icon,
						// Collection slugs to offer, for example [ 'acme' ]. Empty offers all.
						collections: [],
						onChange: setIcon
					} )
					: el( C.TextControl, {
						label: 'Icon name',
						help: 'For example easy-svg/heart.',
						value: a.icon,
						onChange: setIcon,
						__nextHasNoMarginBottom: true
					} ),
				el( C.TextControl, {
					label: 'Label',
					value: a.label,
					onChange: function ( label ) {
						props.setAttributes( { label: label } );
					},
					__nextHasNoMarginBottom: true
				} )
			)
		);
	}

	function Toolbar( props ) {
		// A button that opens the icon library itself: a dropdown around the field would close as the library opens.
		if ( ! pro.IconToolbarButton ) {
			return null;
		}
		return el(
			be.BlockControls,
			{ group: 'other' },
			el( pro.IconToolbarButton, {
				// No label: Pro's own "Choose icon" comes in the editor's language.
				value: props.attributes.icon,
				collections: [],
				onChange: function ( icon ) {
					props.setAttributes( { icon: icon } );
				}
			} )
		);
	}

	wp.blocks.registerBlockType( 'acme/badge', {
		apiVersion: 3,
		title: 'Badge',
		category: 'widgets',
		attributes: {
			icon: { type: 'string', default: '' },
			label: { type: 'string', default: '' }
		},
		edit: function ( props ) {
			// The editor shows what the page will show: render_callback, through the server.
			return el(
				'div',
				be.useBlockProps(),
				el( Sidebar, props ),
				el( Toolbar, props ),
				el( wp.serverSideRender, { block: 'acme/badge', attributes: props.attributes } )
			);
		},
		// Drawn in PHP by render_callback, so the markup follows the icon when it changes.
		save: function () {
			return null;
		}
	} );
}( window.wp ) );
