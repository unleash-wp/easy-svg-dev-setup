/**
 * acme/badge-jsx: the JSX version of templates/javascript-block/.
 * `npm install && npm run build` writes build/index.js and build/index.asset.php.
 */
import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, BlockControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { __ } from '@wordpress/i18n';
import metadata from '../block.json';

// Read at render, not at import: Pro's script may print after this one.
const pro = () => window.EasySvgPro || {};

function Edit( { attributes, setAttributes } ) {
	const { IconControl, IconToolbarButton } = pro();
	const setIcon = ( icon ) => setAttributes( { icon } );

	return (
		<div { ...useBlockProps() }>
			<InspectorControls>
				<PanelBody title={ __( 'Badge', 'acme' ) }>
					{ IconControl ? (
						<IconControl
							label={ __( 'Icon', 'acme' ) }
							help={ __( 'Shown in front of the label.', 'acme' ) }
							value={ attributes.icon }
							// Collection slugs to offer, for example [ 'acme' ]. Empty offers all.
							collections={ [] }
							onChange={ setIcon }
						/>
					) : (
						<TextControl
							__nextHasNoMarginBottom
							label={ __( 'Icon name', 'acme' ) }
							help={ __( 'For example easy-svg/heart.', 'acme' ) }
							value={ attributes.icon }
							onChange={ setIcon }
						/>
					) }
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Label', 'acme' ) }
						value={ attributes.label }
						onChange={ ( label ) => setAttributes( { label } ) }
					/>
				</PanelBody>
			</InspectorControls>

			{ /* Straight into the toolbar: a Dropdown around the field would close as the library opens. */ }
			{ IconToolbarButton && (
				<BlockControls group="other">
					<IconToolbarButton value={ attributes.icon } collections={ [] } onChange={ setIcon } />
				</BlockControls>
			) }

			{ /* The editor shows what the page will show: render_callback, through the server. */ }
			<ServerSideRender block={ metadata.name } attributes={ attributes } />
		</div>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	// Drawn in PHP by render_callback, so the markup follows the icon when it changes.
	save: () => null,
} );
