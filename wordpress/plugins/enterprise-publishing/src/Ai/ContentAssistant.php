<?php
/**
 * Contract for an optional editorial content assistant.
 *
 * The product brief requires that any AI capability be disabled by default,
 * permission-bound, and replaceable. Expressing it as an interface keeps the
 * seam explicit: the platform depends on this contract, never on a concrete
 * provider, so a provider can be swapped (or removed) without touching callers.
 * Implementations must treat "not enabled" and "no permission" as producing no
 * suggestion at all.
 *
 * @package Pixypuala\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixypuala\EnterprisePublishing\Ai;

/**
 * A replaceable, permission-bound editorial assistant.
 */
interface ContentAssistant {

	/**
	 * Whether the assistant may act (enabled AND permitted).
	 */
	public function is_enabled(): bool;

	/**
	 * Produce a suggestion for the given prompt, or null when unavailable.
	 *
	 * Implementations MUST return null whenever {@see is_enabled()} is false, and
	 * MUST NOT perform any side effect in that case.
	 *
	 * @param string               $prompt  Editor-supplied text to act on.
	 * @param array<string, mixed> $context Optional contextual hints.
	 *
	 * @return string|null Suggested text, or null when the assistant is unavailable.
	 */
	public function suggest( string $prompt, array $context = array() ): ?string;
}
