<?php
/**
 * The role -> capability matrix for governed content models.
 *
 * Permission decisions in this platform are *server authoritative* (see ADR 6).
 * This class computes, in pure PHP, exactly which capabilities each role should
 * hold for each content model. The WordPress installer then applies the result.
 * Because it is pure, the entire authorization policy is unit-tested — no role
 * can silently gain a destructive capability without a test noticing.
 *
 * @package Pixypuala\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixypuala\EnterprisePublishing\Capabilities;

use Pixypuala\EnterprisePublishing\ContentModels\Registry;

/**
 * Computes capability grants per role.
 */
final class CapabilityMap {

	/**
	 * Roles this platform governs, from least to most privileged.
	 */
	public const ROLE_CONTRIBUTOR = 'contributor';
	public const ROLE_EDITOR      = 'editor';
	public const ROLE_ADMIN       = 'administrator';

	public function __construct( private readonly Registry $registry ) {}

	/**
	 * Build the full capability grant table.
	 *
	 * @return array<string, array<string, bool>> role => (capability => granted).
	 */
	public function grants(): array {
		$grants = array(
			self::ROLE_CONTRIBUTOR => array(),
			self::ROLE_EDITOR      => array(),
			self::ROLE_ADMIN       => array(),
		);

		foreach ( $this->registry->all() as $model ) {
			$base = $model->capability_base();
			$this->apply_model_grants( $grants, $base['singular'], $base['plural'] );
		}

		return $grants;
	}

	/**
	 * Apply the standardized grant pattern for a single model to every role.
	 *
	 * The pattern encodes the governance intent:
	 *  - Contributors may create and edit *their own* drafts, but never publish
	 *    and never touch others' or published content (approval workflow).
	 *  - Editors may publish and manage everyone's content, but not delete
	 *    published content (guards against accidental brand loss).
	 *  - Administrators hold every capability, including destructive ones.
	 *
	 * @param array<string, array<string, bool>> $grants   Grant table, by reference.
	 * @param string                             $singular Singular capability base.
	 * @param string                             $plural   Plural capability base.
	 */
	private function apply_model_grants( array &$grants, string $singular, string $plural ): void {
		// Capabilities WordPress derives from a custom capability base.
		$read             = "read_{$singular}";
		$edit             = "edit_{$singular}";
		$edit_posts       = "edit_{$plural}";
		$edit_others      = "edit_others_{$plural}";
		$edit_published   = "edit_published_{$plural}";
		$publish          = "publish_{$plural}";
		$delete           = "delete_{$singular}";
		$delete_posts     = "delete_{$plural}";
		$delete_others    = "delete_others_{$plural}";
		$delete_published = "delete_published_{$plural}";

		// Contributor: author own drafts only.
		foreach ( array( $read, $edit, $edit_posts, $delete, $delete_posts ) as $cap ) {
			$grants[ self::ROLE_CONTRIBUTOR ][ $cap ] = true;
		}

		// Editor: contributor rights plus publish and manage others' content,
		// but NOT deleting published content.
		foreach (
			array(
				$read,
				$edit,
				$edit_posts,
				$edit_others,
				$edit_published,
				$publish,
				$delete,
				$delete_posts,
				$delete_others,
			) as $cap
		) {
			$grants[ self::ROLE_EDITOR ][ $cap ] = true;
		}

		// Administrator: every capability including deleting published content.
		foreach (
			array(
				$read,
				$edit,
				$edit_posts,
				$edit_others,
				$edit_published,
				$publish,
				$delete,
				$delete_posts,
				$delete_others,
				$delete_published,
			) as $cap
		) {
			$grants[ self::ROLE_ADMIN ][ $cap ] = true;
		}
	}
}
