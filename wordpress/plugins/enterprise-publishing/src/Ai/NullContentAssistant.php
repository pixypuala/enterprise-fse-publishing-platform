<?php
/**
 * The default content assistant: permanently disabled, always a no-op.
 *
 * This is the assistant the platform uses unless an operator deliberately wires
 * a real one. It encodes "AI off by default" as a type, not a config flag: it
 * can never be enabled and never produces output, so no code path can
 * accidentally invoke a provider. Depending on this by default makes the safe
 * state the default state.
 *
 * @package Pixypuala\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixypuala\EnterprisePublishing\Ai;

/**
 * Null-object assistant that is always off.
 */
final class NullContentAssistant implements ContentAssistant {

	/**
	 * Always disabled.
	 */
	public function is_enabled(): bool {
		return false;
	}

	/**
	 * Always a no-op; returns null without any side effect.
	 *
	 * @param string               $prompt  Unused.
	 * @param array<string, mixed> $context Unused.
	 */
	public function suggest( string $prompt, array $context = array() ): ?string {
		return null;
	}
}
