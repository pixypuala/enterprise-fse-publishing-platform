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

	/**
	 * Option holding the installed schema version.
	 *
	 * Public so activation can guarantee it stays autoloaded; it is read on
	 * every request.
	 */
	public const OPTION = 'enterprise_publishing_schema_version';

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
			/*
			 * Persist after each step so an interrupted run resumes correctly,
			 * and autoload it: this option is read on `init` for every request,
			 * so anything else costs the whole site an uncached query per page
			 * view to learn that no migration is pending.
			 */
			update_option( self::OPTION, $version, true );
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
