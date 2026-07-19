/**
 * Ambient declaration for `@wordpress/block-editor`.
 *
 * The package ships runtime modules without bundled type declarations, so the
 * editor-only imports it provides (useBlockProps, InspectorControls) are treated
 * as untyped here. Kept narrow to a single module so the rest of the block stays
 * fully type-checked.
 */

declare module '@wordpress/block-editor';
