<?php
/**
 * Enterprise FSE theme setup.
 *
 * Block themes need very little PHP. This file only registers a pattern
 * category and declares editor-facing supports; all styling is in theme.json.
 *
 * @package EnterpriseFSE
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register a pattern category so the theme's governed patterns group together
 * in the inserter, keeping editors on-brand.
 */
add_action(
	'init',
	static function (): void {
		register_block_pattern_category(
			'enterprise-fse',
			array( 'label' => __( 'Enterprise FSE', 'enterprise-fse' ) )
		);
	}
);

/**
 * Load the theme text domain for translation.
 */
add_action(
	'after_setup_theme',
	static function (): void {
		load_theme_textdomain( 'enterprise-fse', get_template_directory() . '/languages' );
	}
);
