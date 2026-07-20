/**
 * Ambient declaration for stylesheet imports.
 *
 * The block's front-end stylesheet is imported for its side effect so the
 * bundler emits it as `style-index.css`, which block.json then declares as the
 * block's `style`. The import has no value, so it is declared as `unknown`
 * rather than `any` to keep the rest of the block fully type-checked.
 */

declare module '*.scss' {
	const styles: unknown;
	export default styles;
}
