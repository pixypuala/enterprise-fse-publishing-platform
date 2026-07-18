<?php
/**
 * Example adapter shape for a real content assistant.
 *
 * This shows what a concrete assistant looks like without binding the platform
 * to any provider or making a single network call. It is gated by two
 * independent conditions — an explicit enable flag AND a caller-supplied
 * permission decision — so it stays off unless both are deliberately true. When
 * active it returns a deterministic, locally-derived suggestion; a production
 * adapter would replace only the body of {@see suggest()} with a provider call,
 * keeping the same disabled-by-default, permission-bound contract.
 *
 * @package Pixypuala\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixypuala\EnterprisePublishing\Ai;

/**
 * Illustrative, offline content assistant.
 */
final class ExampleContentAssistant implements ContentAssistant {

	/**
	 * @param bool $enabled        Operator opt-in. Defaults to false (off).
	 * @param bool $has_permission Whether the current actor is permitted to use it.
	 */
	public function __construct(
		private readonly bool $enabled = false,
		private readonly bool $has_permission = false,
	) {}

	/**
	 * Enabled only when opted in AND the actor is permitted.
	 */
	public function is_enabled(): bool {
		return $this->enabled && $this->has_permission;
	}

	/**
	 * Return a deterministic, offline suggestion, or null when unavailable.
	 *
	 * Performs no I/O: a real adapter would delegate to a provider here.
	 *
	 * @param string               $prompt  Editor-supplied text to summarise.
	 * @param array<string, mixed> $context Optional contextual hints (unused here).
	 */
	public function suggest( string $prompt, array $context = array() ): ?string {
		if ( ! $this->is_enabled() ) {
			return null;
		}

		$prompt = trim( $prompt );
		if ( '' === $prompt ) {
			return null;
		}

		return sprintf( 'Suggested summary: %s.', rtrim( $prompt, '.' ) );
	}
}
