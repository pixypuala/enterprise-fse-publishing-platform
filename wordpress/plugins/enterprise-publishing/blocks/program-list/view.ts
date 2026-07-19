/**
 * Front-end behaviour for the Program List block (Interactivity API).
 *
 * Registers a small store whose only state is the current query string. Each
 * rendered item carries its own searchable text in context; the `isHidden`
 * getter reuses the framework-free {@link matchesQuery} rule to decide, per
 * item, whether the current query hides it. No manual DOM writes: the server
 * markup binds `hidden` to this derived state via `data-wp-bind--hidden`.
 */

import { getContext, store } from '@wordpress/interactivity';
import { matchesQuery } from './filter';

interface ProgramItemContext {
	searchText: string;
}

const { state } = store( 'enterprise-publishing/program-list', {
	state: {
		query: '',
		get isHidden(): boolean {
			const context = getContext< ProgramItemContext >();
			return ! matchesQuery( context.searchText, state.query );
		},
	},
	actions: {
		updateQuery( event: Event ): void {
			const target = event.target as HTMLInputElement | null;
			state.query = target ? target.value : '';
		},
	},
} );
