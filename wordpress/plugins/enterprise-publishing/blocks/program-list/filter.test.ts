/**
 * Unit tests for the framework-free filter rule.
 */

import { describe, expect, it } from '@jest/globals';
import { matchesQuery } from './filter';

describe( 'matchesQuery', () => {
	it( 'keeps every item when the query is empty', () => {
		expect( matchesQuery( 'data science', '' ) ).toBe( true );
		expect( matchesQuery( 'data science', '   ' ) ).toBe( true );
	} );

	it( 'matches case-insensitively on a substring', () => {
		expect( matchesQuery( 'data science', 'DATA' ) ).toBe( true );
		expect( matchesQuery( 'data science', 'Science' ) ).toBe( true );
	} );

	it( 'hides an item when the query is not a substring', () => {
		expect( matchesQuery( 'data science', 'history' ) ).toBe( false );
	} );

	it( 'ignores surrounding whitespace in the query', () => {
		expect( matchesQuery( 'data science', '  data  ' ) ).toBe( true );
	} );
} );
