<?php
/**
 * Uninstall routine.
 *
 * Removes what the plugin *installed* and nothing else: the schema-version
 * option and the custom capabilities it granted to roles. Editorial content —
 * the programs, events, and stories stored as custom post types — is the site
 * owner's data and is deliberately left in the database. Removing a plugin must
 * never be a content-destroying operation; export or delete that content
 * explicitly if it is no longer wanted.
 *
 * The capability list is not restated here: it is recomputed from the same pure
 * CapabilityMap the installer used, so the two can never drift apart.
 *
 * @package Pixypuala\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixypuala\EnterprisePublishing;

use Pixypuala\EnterprisePublishing\Capabilities\CapabilityMap;
use Pixypuala\EnterprisePublishing\ContentModels\Registry;

// Only WordPress' uninstall flow may execute this file.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

const UNINSTALL_OPTION = 'enterprise_publishing_schema_version';

/**
 * Register the plugin's PSR-4 loader.
 *
 * Uninstall runs without the plugin bootstrap, so the classes this file needs
 * must be made loadable here. Wrapped in a function so nothing leaks to global
 * scope.
 *
 * @return void
 */
function register_uninstall_autoloader(): void {
	spl_autoload_register(
		static function ( string $class_name ): void {
			$prefix = 'Pixypuala\\EnterprisePublishing\\';
			if ( ! str_starts_with( $class_name, $prefix ) ) {
				return;
			}
			$relative = substr( $class_name, strlen( $prefix ) );
			$path     = __DIR__ . '/src/' . str_replace( '\\', '/', $relative ) . '.php';
			if ( is_readable( $path ) ) {
				require_once $path;
			}
		}
	);
}

/**
 * Delete the option and revoke the granted capabilities on the current site.
 *
 * @return void
 */
function uninstall_current_site(): void {
	delete_option( UNINSTALL_OPTION );

	$grants = ( new CapabilityMap( new Registry() ) )->grants();

	foreach ( $grants as $role_name => $capabilities ) {
		$role = get_role( $role_name );
		if ( null === $role ) {
			continue;
		}
		foreach ( array_keys( $capabilities ) as $capability ) {
			$role->remove_cap( $capability );
		}
	}
}

/**
 * Run the uninstall against every site of the install.
 *
 * On multisite each blog carries its own options table and role definitions, so
 * the removal has to be repeated per site rather than done once on the network.
 *
 * @return void
 */
function uninstall_everywhere(): void {
	if ( ! is_multisite() ) {
		uninstall_current_site();

		return;
	}

	foreach ( get_sites( array( 'fields' => 'ids' ) ) as $site_id ) {
		switch_to_blog( (int) $site_id );
		uninstall_current_site();
		restore_current_blog();
	}
}

register_uninstall_autoloader();
uninstall_everywhere();
