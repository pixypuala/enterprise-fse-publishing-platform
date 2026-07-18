<?php
/**
 * WordPress adapter that applies the computed capability map to real roles.
 *
 * The policy itself lives in the pure CapabilityMap; this class only writes the
 * result onto WordPress role objects. Applying is idempotent, so it is safe to
 * run on every migration.
 *
 * @package Pixypuala\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixypuala\EnterprisePublishing\Capabilities;

/**
 * Grants computed capabilities to WordPress roles.
 */
final class CapabilityInstaller {

	public function __construct( private readonly CapabilityMap $map ) {}

	/**
	 * Apply every grant. Existing capabilities are left untouched; only the
	 * governed model capabilities are added.
	 */
	public function install(): void {
		foreach ( $this->map->grants() as $role_name => $capabilities ) {
			$role = get_role( $role_name );
			if ( null === $role ) {
				// Role does not exist on this install (e.g. multisite variance).
				// Skip rather than fabricate a role the site did not define.
				continue;
			}
			foreach ( $capabilities as $capability => $granted ) {
				if ( $granted ) {
					$role->add_cap( $capability );
				}
			}
		}
	}
}
