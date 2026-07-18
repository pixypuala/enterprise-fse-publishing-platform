<?php
/**
 * Unit tests for the personal-data exporter shape.
 *
 * @package Pixypuala\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixypuala\EnterprisePublishing\Tests;

use Pixypuala\EnterprisePublishing\Privacy\OwnedRecord;
use Pixypuala\EnterprisePublishing\Privacy\PersonalDataExporter;
use PHPUnit\Framework\TestCase;

final class PersonalDataExporterTest extends TestCase {

	public function test_empty_records_produce_done_with_no_data(): void {
		$result = ( new PersonalDataExporter() )->export( array() );

		$this->assertSame( array(), $result['data'] );
		$this->assertTrue( $result['done'] );
	}

	public function test_record_maps_to_wordpress_group_shape(): void {
		$record = new OwnedRecord(
			id: '42',
			label: 'MSc Data Science',
			fields: array(
				'Type'   => 'ep_program',
				'Status' => 'draft',
			),
		);

		$result = ( new PersonalDataExporter() )->export( array( $record ) );

		$this->assertCount( 1, $result['data'] );
		$group = $result['data'][0];

		$this->assertSame( 'enterprise-publishing', $group['group_id'] );
		$this->assertSame( 'Enterprise Publishing', $group['group_label'] );
		$this->assertSame( 'enterprise-publishing-42', $group['item_id'] );
		$this->assertSame(
			array(
				array(
					'name'  => 'Record',
					'value' => 'MSc Data Science',
				),
				array(
					'name'  => 'Type',
					'value' => 'ep_program',
				),
				array(
					'name'  => 'Status',
					'value' => 'draft',
				),
			),
			$group['data']
		);
	}

	public function test_done_flag_is_forwarded_for_pagination(): void {
		$result = ( new PersonalDataExporter() )->export( array(), false );

		$this->assertFalse( $result['done'] );
	}
}
