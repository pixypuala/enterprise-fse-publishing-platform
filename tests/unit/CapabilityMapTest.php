<?php
/**
 * Unit tests for the role -> capability policy.
 *
 * These tests are the guard rail on authorization: they assert the exact
 * privilege boundaries so no future edit can silently hand a lower role a
 * destructive capability.
 *
 * @package Pixyville\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixyville\EnterprisePublishing\Tests;

use Pixyville\EnterprisePublishing\Capabilities\CapabilityMap;
use Pixyville\EnterprisePublishing\ContentModels\Registry;
use PHPUnit\Framework\TestCase;

final class CapabilityMapTest extends TestCase {

	private function grants(): array {
		return ( new CapabilityMap( new Registry() ) )->grants();
	}

	public function test_contributor_cannot_publish_programs(): void {
		$grants = $this->grants();
		$this->assertArrayNotHasKey(
			'publish_ep_programs',
			$grants[ CapabilityMap::ROLE_CONTRIBUTOR ],
			'Contributors must go through approval; they cannot publish.'
		);
	}

	public function test_contributor_cannot_edit_others_programs(): void {
		$grants = $this->grants();
		$this->assertArrayNotHasKey(
			'edit_others_ep_programs',
			$grants[ CapabilityMap::ROLE_CONTRIBUTOR ]
		);
	}

	public function test_editor_can_publish_but_cannot_delete_published(): void {
		$grants = $this->grants();
		$editor = $grants[ CapabilityMap::ROLE_EDITOR ];

		$this->assertArrayHasKey( 'publish_ep_programs', $editor );
		$this->assertArrayNotHasKey(
			'delete_published_ep_programs',
			$editor,
			'Editors must not be able to delete published content (brand-loss guard).'
		);
	}

	public function test_administrator_can_delete_published(): void {
		$grants = $this->grants();
		$this->assertArrayHasKey(
			'delete_published_ep_programs',
			$grants[ CapabilityMap::ROLE_ADMIN ]
		);
	}

	public function test_privilege_is_monotonic_admin_superset_of_editor(): void {
		$grants = $this->grants();
		$editor = array_keys( $grants[ CapabilityMap::ROLE_EDITOR ] );
		$admin  = array_keys( $grants[ CapabilityMap::ROLE_ADMIN ] );

		// Every capability an editor holds, an administrator also holds.
		$this->assertEmpty( array_diff( $editor, $admin ) );
	}

	public function test_every_governed_model_has_capabilities(): void {
		$grants   = $this->grants();
		$registry = new Registry();

		foreach ( $registry->all() as $model ) {
			$plural = $model->capability_base()['plural'];
			$this->assertArrayHasKey(
				"publish_{$plural}",
				$grants[ CapabilityMap::ROLE_EDITOR ],
				"Model {$model->key} is missing editor publish capability."
			);
		}
	}
}
