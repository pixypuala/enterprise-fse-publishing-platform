<?php
/**
 * Unit tests for the ContentModel value object and the Registry.
 *
 * @package Pixyville\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixyville\EnterprisePublishing\Tests;

use Pixyville\EnterprisePublishing\ContentModels\ContentModel;
use Pixyville\EnterprisePublishing\ContentModels\Registry;
use PHPUnit\Framework\TestCase;

final class ContentModelTest extends TestCase {

	public function test_valid_model_exposes_capability_base(): void {
		$model = new ContentModel(
			key: 'ep_program',
			singular: 'Program',
			plural: 'Programs',
			description: 'x',
			supports: array( 'title' ),
			has_archive: true,
			menu_icon: 'dashicons-portfolio',
		);

		$this->assertSame(
			array(
				'singular' => 'ep_program',
				'plural'   => 'ep_programs',
			),
			$model->capability_base()
		);
	}

	public function test_rejects_key_over_twenty_characters(): void {
		$this->expectException( \InvalidArgumentException::class );
		new ContentModel(
			key: 'ep_way_too_long_key_value',
			singular: 'X',
			plural: 'Xs',
			description: 'x',
			supports: array(),
			has_archive: false,
			menu_icon: 'dashicons-admin-post',
		);
	}

	public function test_rejects_key_with_invalid_characters(): void {
		$this->expectException( \InvalidArgumentException::class );
		new ContentModel(
			key: 'EP-Program',
			singular: 'X',
			plural: 'Xs',
			description: 'x',
			supports: array(),
			has_archive: false,
			menu_icon: 'dashicons-admin-post',
		);
	}

	public function test_registry_indexes_models_by_key(): void {
		$registry = new Registry();

		$this->assertNotNull( $registry->get( 'ep_program' ) );
		$this->assertSame( 'Programs', $registry->get( 'ep_program' )->plural );
		$this->assertNull( $registry->get( 'ep_nonexistent' ) );
	}

	public function test_registry_keys_are_unique_and_valid(): void {
		$registry = new Registry();
		$all      = $registry->all();

		$this->assertNotEmpty( $all );
		foreach ( $all as $key => $model ) {
			// The array key must match the model's own key (no drift).
			$this->assertSame( $key, $model->key );
			$this->assertLessThanOrEqual( 20, strlen( $key ) );
		}
	}
}
