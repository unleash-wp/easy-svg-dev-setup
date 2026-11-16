/**
 * Types for the editor's autocomplete. `@unleash-wp/easy-svg-pro` is not an
 * npm package: webpack.config.js maps it to window.EasySvgPro, which Easy SVG
 * Pro puts on the page with the handle easy-svg-pro-icon-control.
 */
declare module '@unleash-wp/easy-svg-pro' {
	import type { ComponentType } from 'react';

	interface IconFieldProps {
		/** The saved icon name, `collection/icon`. */
		value?: string;
		/** Gets the new name; IconControl passes '' after Remove. */
		onChange?: ( name: string ) => void;
		/** Collection slugs to offer; empty offers all. */
		collections?: string[];
		/** The field's label, or the toolbar button's tooltip. */
		label?: string;
	}

	/** The field for InspectorControls. */
	export const IconControl: ComponentType< IconFieldProps & { help?: string } >;

	/** The button for BlockControls; it opens the icon library itself. */
	export const IconToolbarButton: ComponentType< IconFieldProps & { icon?: unknown } >;

	/** Raised only when a prop or the global changes in a way a block would notice. */
	export const api: 1;
}
