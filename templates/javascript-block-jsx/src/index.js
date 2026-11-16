/**
 * acme/badge-jsx: an icon and a label, the icon chosen with Easy SVG Pro.
 * `@unleash-wp/easy-svg-pro` works like `@wordpress/components`: webpack.config.js
 * maps it to Pro's script on the page, so this block requires Easy SVG Pro in
 * the editor. The page is drawn by render.php with the free plugin alone.
 */
import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, BlockControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { __ } from '@wordpress/i18n';
import { IconControl, IconToolbarButton } from '@unleash-wp/easy-svg-pro';
import metadata from './block.json';

function Edit( { attributes, setAttributes } ) {
	const setIcon = ( icon ) => setAttributes( { icon } );

	return (
		<div { ...useBlockProps() }>
			<InspectorControls>
				<PanelBody title={ __( 'Badge', 'acme' ) }>
					<IconControl
						label={ __( 'Icon', 'acme' ) }
						help={ __( 'Shown in front of the label.', 'acme' ) }
						value={ attributes.icon }
						// Collection slugs to offer, for example [ 'acme' ]. Empty offers all.
						collections={ [] }
						onChange={ setIcon }
					/>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Label', 'acme' ) }
						value={ attributes.label }
						onChange={ ( label ) => setAttributes( { label } ) }
					/>
				</PanelBody>
			</InspectorControls>

			{ /* Straight into the toolbar: a Dropdown around the field would close as the library opens. */ }
			<BlockControls group="other">
				<IconToolbarButton value={ attributes.icon } collections={ [] } onChange={ setIcon } />
			</BlockControls>

			{ /* The editor shows what the page will show: render.php, through the server. */ }
			<ServerSideRender block={ metadata.name } attributes={ attributes } />
		</div>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	// Drawn by render.php, so the markup follows the icon when it changes.
	save: () => null,
} );
