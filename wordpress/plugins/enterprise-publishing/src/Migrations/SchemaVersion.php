<?php
/**
 * Schema/migration version tracking.
 *
 * Long-lived content needs a migration story *before* it is stored (ADR 4).
 * This class owns the ordered list of migration steps and can decide, given the
 * version currently installed, which steps still need to run. It performs no I/O
 * so the ordering/decision logic is unit-testable; the WordPress runner applies
 * the steps and persists the new version.
 *
 * @package Pixypuala\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixypuala\EnterprisePublishing\Migrations;

/**
 * Ordered, idempotent migration ledger.
 */
final class SchemaVersion {

	/**
	 * The version this build of the plugin expects the database to be at.
	 * Bump this (and add a step) whenever stored data shape changes.
	 */
	public const CURRENT = 2;

	/**
	 * Ordered migration steps: version number => human description.
	 *
	 * Each key is the schema version reached *after* the step runs. Keys must be
	 * contiguous starting at 1 so we can reason about "what is pending".
	 *
	 * @var array<int, string>
	 */
	private const STEPS = array(
		1 => 'Register governed content models and flush rewrite rules.',
		2 => 'Add per-model approval-state meta and seed default editorial roles.',
	);

	/**
	 * Which steps must run to move from $installed to CURRENT.
	 *
	 * @param int $installed Version currently stored (0 for a fresh install).
	 *
	 * @return array<int, string> Pending steps, in ascending version order.
	 *
	 * @throws \OutOfRangeException When the installed version is newer than this
	 *                              build knows about (a downgrade — refuse loudly
	 *                              rather than silently corrupt data).
	 */
	public function pending( int $installed ): array {
		if ( $installed > self::CURRENT ) {
			// phpcs:disable WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Developer-facing exception message from the framework-free domain; both interpolated values are integers and the message is never rendered.
			throw new \OutOfRangeException(
				sprintf(
					'Installed schema version %d is newer than this build (%d); refusing to downgrade.',
					$installed,
					self::CURRENT
				)
			);
			// phpcs:enable WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}

		$pending = array();
		foreach ( self::STEPS as $version => $description ) {
			if ( $version > $installed ) {
				$pending[ $version ] = $description;
			}
		}

		return $pending;
	}

	/**
	 * Whether the store is fully migrated.
	 *
	 * @param int $installed Version currently stored.
	 *
	 * @return bool
	 */
	public function is_current( int $installed ): bool {
		return array() === $this->pending( $installed );
	}
}
