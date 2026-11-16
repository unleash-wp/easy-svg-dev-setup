/**
 * @wordpress/scripts' own config, with one more answer for its dependency
 * extraction: `@unleash-wp/easy-svg-pro` is Easy SVG Pro's script, already on
 * the page, the same way `@wordpress/components` is wp.components. Nothing is
 * downloaded or bundled; the import becomes window.EasySvgPro and the handle
 * easy-svg-pro-icon-control lands in build/index.asset.php.
 */
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const DependencyExtractionWebpackPlugin = require( '@wordpress/dependency-extraction-webpack-plugin' );

const EASY_SVG_PRO = '@unleash-wp/easy-svg-pro';

module.exports = {
	...defaultConfig,
	plugins: [
		...defaultConfig.plugins.filter( ( plugin ) => ! ( plugin instanceof DependencyExtractionWebpackPlugin ) ),
		new DependencyExtractionWebpackPlugin( {
			requestToExternal: ( request ) => ( EASY_SVG_PRO === request ? 'EasySvgPro' : undefined ),
			requestToHandle: ( request ) => ( EASY_SVG_PRO === request ? 'easy-svg-pro-icon-control' : undefined ),
		} ),
	],
};
