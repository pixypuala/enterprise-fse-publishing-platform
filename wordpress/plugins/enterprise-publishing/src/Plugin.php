<?php
/**
 * Plugin wiring.
 *
 * Composes the domain (Registry, CapabilityMap, SchemaVersion) with the thin
 * WordPress adapters that register post types, install capabilities, run
 * migrations, and expose a health screen. All WordPress-specific side effects
 * live in the adapters; this class only wires them to hooks.
 *
 * @package Pixyville\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixyville\EnterprisePublishing;

use Pixyville\EnterprisePublishing\Admin\HealthScreen;
use Pixyville\EnterprisePublishing\Capabilities\CapabilityInstaller;
use Pixyville\EnterprisePublishing\Capabilities\CapabilityMap;
use Pixyville\EnterprisePublishing\ContentModels\ModelRegistrar;
use Pixyville\EnterprisePublishing\ContentModels\Registry;
use Pixyville\EnterprisePublishing\Migrations\MigrationRunner;
use Pixyville\EnterprisePublishing\Migrations\SchemaVersion;

/**
 * Root object graph for the plugin.
 */
final class Plugin {

	private Registry $registry;

	public function __construct() {
		$this->registry = new Registry();
	}

	/**
	 * Attach runtime hooks (runs on every request, cheaply).
	 */
	public function register(): void {
		$registrar = new ModelRegistrar( $this->registry );
		add_action( 'init', array( $registrar, 'register_all' ) );

		// Run any pending migrations after content types exist. Cheap no-op when
		// already current (a single option read).
		$runner = new MigrationRunner( new SchemaVersion(), new CapabilityInstaller( new CapabilityMap( $this->registry ) ) );
		add_action( 'init', array( $runner, 'run_if_needed' ), 20 );

		// Admin-only health/status screen.
		if ( is_admin() ) {
			$health = new HealthScreen( $this->registry, new SchemaVersion() );
			add_action( 'admin_menu', array( $health, 'register_menu' ) );
		}
	}

	/**
	 * Activation: register types then flush rewrite rules so archive/single URLs
	 * work immediately, and force migrations to run.
	 */
	public static function on_activate(): void {
		$registry = new Registry();
		( new ModelRegistrar( $registry ) )->register_all();

		$runner = new MigrationRunner(
			new SchemaVersion(),
			new CapabilityInstaller( new CapabilityMap( $registry ) )
		);
		$runner->run_if_needed();

		flush_rewrite_rules();
	}

	/**
	 * Deactivation: only flush rewrite rules. Capabilities and data are left
	 * intact so deactivating does not destroy editorial access or content.
	 */
	public static function on_deactivate(): void {
		flush_rewrite_rules();
	}
}
