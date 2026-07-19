/**
 * Editor interface for the Program List block.
 *
 * Presents the governed setting (how many programs to show) in the inspector
 * and previews the real server output with ServerSideRender, so editors see
 * exactly what the front end renders — no divergent editor markup to drift.
 */

import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, RangeControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import type { BlockEditProps } from '@wordpress/blocks';

export interface ProgramListAttributes {
	maxItems: number;
	[ key: string ]: unknown;
}

const MIN_ITEMS = 1;
const MAX_ITEMS = 50;
const DEFAULT_ITEMS = 6;

export default function Edit( {
	attributes,
	setAttributes,
}: BlockEditProps< ProgramListAttributes > ) {
	const blockProps = useBlockProps();

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<PanelBody title={ __( 'Program list', 'enterprise-publishing' ) }>
					<RangeControl
						label={ __( 'Maximum programs to show', 'enterprise-publishing' ) }
						min={ MIN_ITEMS }
						max={ MAX_ITEMS }
						value={ attributes.maxItems }
						onChange={ ( value?: number ) =>
							setAttributes( { maxItems: value ?? DEFAULT_ITEMS } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<ServerSideRender
				block="enterprise-publishing/program-list"
				attributes={ attributes }
			/>
		</div>
	);
}
