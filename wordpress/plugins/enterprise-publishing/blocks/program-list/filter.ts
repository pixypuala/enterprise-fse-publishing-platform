/**
 * Framework-free matching rule shared by the front-end filter.
 *
 * Kept free of the Interactivity API and the DOM so the rule is deterministic
 * and unit-testable in isolation. The server writes a lower-cased, tag-stripped
 * `searchText` into each item's context; this decides whether a typed query
 * keeps that item visible.
 */

/**
 * Whether an item whose searchable text is `haystack` should stay visible for
 * the given `query`. An empty or whitespace-only query keeps every item.
 *
 * @param haystack Lower-cased searchable text for one item.
 * @param query    Raw query typed by the visitor.
 * @return True when the item matches and should remain visible.
 */
export function matchesQuery( haystack: string, query: string ): boolean {
	const needle = query.trim().toLowerCase();

	if ( '' === needle ) {
		return true;
	}

	return haystack.toLowerCase().includes( needle );
}
