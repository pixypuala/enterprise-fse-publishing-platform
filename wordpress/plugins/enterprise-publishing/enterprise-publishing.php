<?php
/**
 * Plugin Name:       Enterprise Publishing
 * Plugin URI:        https://github.com/pixypuala/enterprise-fse-publishing-platform
 * Description:        Governed content models, server-authoritative capabilities, and migration tracking for the Enterprise FSE Publishing Platform. Owns durable data and business rules so the theme stays purely presentational.
 * Version:           0.1.0
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Author:            Pixypuala
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       enterprise-publishing
 *
 * @package Pixypuala\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixypuala\EnterprisePublishing;

// Abort if accessed directly — plugin files must only run inside WordPress.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const VERSION     = '0.1.0';
const PLUGIN_FILE = __FILE__;

/**
 * Register the class autoloader.
 *
 * Prefers a Composer autoloader shipped inside the plugin; the plugin has no
 * runtime Composer dependencies, so in production the built-in PSR-4 fallback
 * is the loading path. Only the plugin's own directory is consulted — reaching
 * outside it would make the plugin depend on the layout of the site around it.
 *
 * Wrapped in a function so no variable from this file leaks into the global
 * scope, where it could collide with another plugin's.
 *
 * @return void
 */
function register_autoloader(): void {
	$autoload = __DIR__ . '/vendor/autoload.php';

	if ( is_readable( $autoload ) ) {
		require_once $autoload;

		return;
	}

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

register_autoloader();

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
