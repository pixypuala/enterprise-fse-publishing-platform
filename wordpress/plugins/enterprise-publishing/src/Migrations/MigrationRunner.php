<?php
/**
 * WordPress adapter that runs pending migration steps and persists the version.
 *
 * The SchemaVersion ledger decides *what* must run (pure, tested); this runner
 * performs the side effects for each step and stores the new version in an
 * option. Steps are matched by version number so the mapping stays explicit.
 *
 * @package Pixypuala\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixypuala\EnterprisePublishing\Migrations;

use Pixypuala\EnterprisePublishing\Capabilities\CapabilityInstaller;

/**
 * Applies pending migrations.
 */
final class MigrationRunner {

	private const OPTION = 'enterprise_publishing_schema_version';

	public function __construct(
		private readonly SchemaVersion $schema,
		private readonly CapabilityInstaller $capabilities,
	) {}

	/**
	 * Run any migration steps the installed version is behind on.
	 */
	public function run_if_needed(): void {
		$installed = (int) get_option( self::OPTION, 0 );

		if ( $this->schema->is_current( $installed ) ) {
			return;
		}

		foreach ( array_keys( $this->schema->pending( $installed ) ) as $version ) {
			$this->apply_step( $version );
			// Persist after each step so an interrupted run resumes correctly.
			update_option( self::OPTION, $version, false );
		}
	}

	/**
	 * Perform the side effects for one migration step.
	 *
	 * @param int $version Target version this step reaches.
	 */
	private function apply_step( int $version ): void {
		switch ( $version ) {
			case 1:
				// Content types are registered on `init`; ensure rewrite rules
				// reflect them on first install.
				flush_rewrite_rules();
				break;
			case 2:
				// Seed editorial capabilities onto existing roles.
				$this->capabilities->install();
				break;
		}
	}
}
