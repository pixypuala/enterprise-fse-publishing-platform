/**
 * ESLint flat config for the block sources.
 *
 * Extends the @wordpress/scripts default config and pins the React version so
 * eslint-plugin-react does not attempt runtime detection (React is provided by
 * WordPress at runtime and is not a direct dependency here).
 */

import wpConfig from './node_modules/@wordpress/scripts/config/eslint.config.cjs';

export default [
	...wpConfig,
	{
		settings: {
			react: {
				version: '18',
			},
		},
	},
];
