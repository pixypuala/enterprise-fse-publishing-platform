<?php
/**
 * WordPress adapter that registers the plugin's server-rendered blocks.
 *
 * Blocks are compiled by `@wordpress/scripts` into the plugin `build/` directory
 * (each block folder holding its `block.json`, asset manifest, and `render.php`).
 * Registration is guarded on the presence of a built `block.json` so a plain
 * checkout without a build step simply registers nothing rather than fataling.
 *
 * @package Pixypuala\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixypuala\EnterprisePublishing\Blocks;

/**
 * Registers built block types with WordPress.
 */
final class BlockRegistrar {

	/**
	 * Absolute path to the compiled block build directory.
	 *
	 * @var string
	 */
	private string $build_dir;

	/**
	 * Block folder names expected under the build directory.
	 *
	 * @var string[]
	 */
	private const BLOCKS = array( 'program-list' );

	/**
	 * @param string|null $build_dir Build directory; defaults to the plugin's `build/`.
	 */
	public function __construct( ?string $build_dir = null ) {
		$this->build_dir = $build_dir ?? dirname( __DIR__, 2 ) . '/build';
	}

	/**
	 * Attach the block registration hook.
	 */
	public function register(): void {
		add_action( 'init', array( $this, 'register_blocks' ) );
	}

	/**
	 * Register each built block. A missing build is a no-op, not an error.
	 */
	public function register_blocks(): void {
		foreach ( self::BLOCKS as $block ) {
			$metadata = $this->build_dir . '/' . $block . '/block.json';
			if ( is_readable( $metadata ) ) {
				register_block_type( $metadata );
			}
		}
	}
}
