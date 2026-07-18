<?php
/**
 * Unit tests for the Schema.org JSON-LD program builder.
 *
 * @package Pixypuala\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixypuala\EnterprisePublishing\Tests;

use Pixypuala\EnterprisePublishing\Seo\ProgramSchema;
use PHPUnit\Framework\TestCase;

final class ProgramSchemaTest extends TestCase {

	public function test_builds_minimal_document_with_context_and_type(): void {
		$doc = ( new ProgramSchema() )->build( array( 'name' => 'MSc Data Science' ) );

		$this->assertSame( 'https://schema.org', $doc['@context'] );
		$this->assertSame( 'EducationalOccupationalProgram', $doc['@type'] );
		$this->assertSame( 'MSc Data Science', $doc['name'] );
		$this->assertArrayNotHasKey( 'provider', $doc, 'Provider is omitted when no name is given.' );
		$this->assertArrayNotHasKey( 'description', $doc );
	}

	public function test_missing_name_is_rejected(): void {
		$this->expectException( \InvalidArgumentException::class );
		( new ProgramSchema() )->build( array( 'name' => '   ' ) );
	}

	public function test_optional_fields_are_included_when_present(): void {
		$doc = ( new ProgramSchema() )->build(
			array(
				'name'          => 'MSc Data Science',
				'url'           => 'https://example.edu/programs/msc-ds',
				'description'   => 'A two-year programme.',
				'start_date'    => '2026-09-01',
				'end_date'      => '2028-06-30',
				'provider_name' => 'Example University',
				'provider_url'  => 'https://example.edu',
			)
		);

		$this->assertSame( 'https://example.edu/programs/msc-ds', $doc['url'] );
		$this->assertSame( '2026-09-01', $doc['startDate'] );
		$this->assertSame( '2028-06-30', $doc['endDate'] );
		$this->assertSame(
			array(
				'@type' => 'Organization',
				'name'  => 'Example University',
				'url'   => 'https://example.edu',
			),
			$doc['provider']
		);
	}

	public function test_provider_without_url_omits_url(): void {
		$doc = ( new ProgramSchema() )->build(
			array(
				'name'          => 'Diploma',
				'provider_name' => 'Example College',
			)
		);

		$this->assertSame(
			array(
				'@type' => 'Organization',
				'name'  => 'Example College',
			),
			$doc['provider']
		);
	}

	public function test_output_is_deterministic_and_encodable(): void {
		$builder = new ProgramSchema();
		$program = array(
			'name'        => 'Certificate',
			'description' => 'Short course.',
		);

		$first  = $builder->build( $program );
		$second = $builder->build( $program );

		$this->assertSame( $first, $second, 'Same input must produce identical output.' );
	}
}
