<?php
/**
 * WordPress adapter that registers governed content models as custom post types.
 *
 * This is the only place that calls register_post_type(). It translates the
 * framework-free ContentModel definitions into WordPress arguments, applying the
 * custom capability base so each model has its own capability set.
 *
 * @package Pixyville\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixyville\EnterprisePublishing\ContentModels;

/**
 * Registers every governed model with WordPress.
 */
final class ModelRegistrar {

	public function __construct( private readonly Registry $registry ) {}

	/**
	 * Register all governed models. Hooked to `init`.
	 */
	public function register_all(): void {
		foreach ( $this->registry->all() as $model ) {
			register_post_type( $model->key, $this->args_for( $model ) );
		}
	}

	/**
	 * Build register_post_type() arguments for one model.
	 *
	 * @param ContentModel $model Domain model definition.
	 *
	 * @return array<string, mixed>
	 */
	private function args_for( ContentModel $model ): array {
		$base = $model->capability_base();

		return array(
			'labels'          => $this->labels_for( $model ),
			'description'     => $model->description,
			'public'          => true,
			'show_in_rest'    => true, // Required for the block editor.
			'has_archive'     => $model->has_archive,
			'menu_icon'       => $model->menu_icon,
			'supports'        => $model->supports,
			'rewrite'         => array( 'slug' => str_replace( 'ep_', '', $model->key ) ),

			// Custom capabilities so roles can be granted rights per model
			// without inheriting core "post" rights. map_meta_cap must be true
			// for WordPress to resolve the meta capabilities (edit_post, etc.).
			'capability_type' => array( $base['singular'], $base['plural'] ),
			'map_meta_cap'    => true,
		);
	}

	/**
	 * Standard label set derived from singular/plural names.
	 *
	 * @param ContentModel $model Domain model definition.
	 *
	 * @return array<string, string>
	 */
	private function labels_for( ContentModel $model ): array {
		$singular = $model->singular;
		$plural   = $model->plural;

		return array(
			'name'               => $plural,
			'singular_name'      => $singular,
			/* translators: %s: content model plural name. */
			'menu_name'          => $plural,
			'add_new_item'       => sprintf( 'Add New %s', $singular ),
			'edit_item'          => sprintf( 'Edit %s', $singular ),
			'new_item'           => sprintf( 'New %s', $singular ),
			'view_item'          => sprintf( 'View %s', $singular ),
			'search_items'       => sprintf( 'Search %s', $plural ),
			'not_found'          => sprintf( 'No %s found', strtolower( $plural ) ),
			'not_found_in_trash' => sprintf( 'No %s found in Trash', strtolower( $plural ) ),
			'all_items'          => sprintf( 'All %s', $plural ),
		);
	}
}
