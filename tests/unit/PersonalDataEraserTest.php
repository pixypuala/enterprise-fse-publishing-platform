<?php
/**
 * Unit tests for the personal-data eraser plan and retention policy.
 *
 * @package Pixypuala\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixypuala\EnterprisePublishing\Tests;

use Pixypuala\EnterprisePublishing\Privacy\OwnedRecord;
use Pixypuala\EnterprisePublishing\Privacy\PersonalDataEraser;
use PHPUnit\Framework\TestCase;

final class PersonalDataEraserTest extends TestCase {

	public function test_empty_records_remove_and_retain_nothing(): void {
		$plan = ( new PersonalDataEraser() )->plan( array() );

		$this->assertFalse( $plan['items_removed'] );
		$this->assertFalse( $plan['items_retained'] );
		$this->assertSame( array(), $plan['messages'] );
		$this->assertSame( array(), $plan['ids_to_remove'] );
		$this->assertTrue( $plan['done'] );
	}

	public function test_removable_record_is_planned_for_removal(): void {
		$record = new OwnedRecord(
			id: '7',
			label: 'Draft program',
			fields: array(),
		);

		$plan = ( new PersonalDataEraser() )->plan( array( $record ) );

		$this->assertTrue( $plan['items_removed'] );
		$this->assertFalse( $plan['items_retained'] );
		$this->assertSame( array( '7' ), $plan['ids_to_remove'] );
	}

	public function test_retained_record_is_reported_with_reason_and_not_removed(): void {
		$record = new OwnedRecord(
			id: '9',
			label: 'Published program',
			fields: array(),
			removable: false,
			retention_reason: 'Published editorial content is a business record.',
		);

		$plan = ( new PersonalDataEraser() )->plan( array( $record ) );

		$this->assertFalse( $plan['items_removed'] );
		$this->assertTrue( $plan['items_retained'] );
		$this->assertSame( array(), $plan['ids_to_remove'] );
		$this->assertSame(
			array( 'Published editorial content is a business record.' ),
			$plan['messages']
		);
	}

	public function test_mixed_records_partition_correctly(): void {
		$plan = ( new PersonalDataEraser() )->plan(
			array(
				new OwnedRecord( id: '1', label: 'Draft', fields: array() ),
				new OwnedRecord(
					id: '2',
					label: 'Published',
					fields: array(),
					removable: false,
					retention_reason: 'Retained as a business record.',
				),
			)
		);

		$this->assertTrue( $plan['items_removed'] );
		$this->assertTrue( $plan['items_retained'] );
		$this->assertSame( array( '1' ), $plan['ids_to_remove'] );
	}

	public function test_retained_record_without_reason_is_rejected(): void {
		$this->expectException( \InvalidArgumentException::class );
		new OwnedRecord(
			id: '3',
			label: 'Published',
			fields: array(),
			removable: false,
		);
	}

	public function test_empty_id_is_rejected(): void {
		$this->expectException( \InvalidArgumentException::class );
		new OwnedRecord( id: '  ', label: 'Anything', fields: array() );
	}
}
