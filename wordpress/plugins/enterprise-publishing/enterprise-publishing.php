<?php
/**
 * Plugin Name:       Enterprise Publishing
 * Plugin URI:        https://github.com/pixypuala/enterprise-fse-publishing-platform
 * Description:        Governed content models, server-authoritative capabilities, and migration tracking for the Enterprise FSE Publishing Platform. Owns durable data and business rules so the theme stays purely presentational.
 * Version:           0.1.0
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Author:            Pixyville
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       enterprise-publishing
 *
 * @package Pixyville\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixyville\EnterprisePublishing;

// Abort if accessed directly — plugin files must only run inside WordPress.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const VERSION     = '0.1.0';
const PLUGIN_FILE = __FILE__;

/**
 * Autoloader.
 *
 * Uses Composer's PSR-4 autoloader when the project has been installed with
 * `composer install` (the normal path). Falls back to a tiny built-in PSR-4
 * loader so the plugin still runs from a plain checkout without a vendor dir.
 */
$composer_autoload = __DIR__ . '/../../../vendor/autoload.php';
if ( is_readable( $composer_autoload ) ) {
	require_once $composer_autoload;
} else {
	spl_autoload_register(
		static function ( string $class_name ): void {
			$prefix = 'Pixyville\\EnterprisePublishing\\';
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

// Boot the plugin once WordPress has loaded plugins.
add_action(
	'plugins_loaded',
	static function (): void {
		( new Plugin() )->register();
	}
);

// Activation/deactivation must be registered against the main plugin file.
register_activation_hook( __FILE__, array( Plugin::class, 'on_activate' ) );
register_deactivation_hook( __FILE__, array( Plugin::class, 'on_deactivate' ) );
