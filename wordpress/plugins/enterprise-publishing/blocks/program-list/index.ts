/**
 * Editor registration entry for the Program List block.
 *
 * Registers the block type from its own metadata. The block is dynamic: markup
 * comes from the server `render.php`, so `save` returns null and no markup is
 * persisted in post content.
 */

import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';
import Edit, { type ProgramListAttributes } from './edit';
import metadata from './block.json';

// Imported for its side effect: the bundler emits it as `style-index.css`,
// which block.json declares as the block's front-end `style`.
import './style.scss';

// block.json is the block configuration; the JSON import widens `supports.align`
// to `string[]`, so it is asserted back to the precise configuration type.
registerBlockType< ProgramListAttributes >(
	metadata as BlockConfiguration< ProgramListAttributes >,
	{
		edit: Edit,
		save: () => null,
	},
);
