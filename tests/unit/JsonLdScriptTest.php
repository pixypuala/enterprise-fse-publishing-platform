<?php
/**
 * Unit tests for the script-safe JSON-LD renderer.
 *
 * These tests are the guard rail on a hostile output sink: they prove the
 * renderer cannot break out of a `<script>` tag and always wraps valid JSON.
 *
 * @package Pixypuala\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixypuala\EnterprisePublishing\Tests;

use Pixypuala\EnterprisePublishing\Seo\JsonLdScript;
use Pixypuala\EnterprisePublishing\Seo\ProgramSchema;
use PHPUnit\Framework\TestCase;

final class JsonLdScriptTest extends TestCase {

	public function test_wraps_output_in_json_ld_script_tags(): void {
		$html = ( new JsonLdScript() )->render( array( '@type' => 'Thing' ) );

		$this->assertStringStartsWith( '<script type="application/ld+json">', $html );
		$this->assertStringEndsWith( '</script>', $html );
	}

	public function test_escapes_angle_brackets_to_prevent_script_breakout(): void {
		$html = ( new JsonLdScript() )->render(
			array( 'name' => 'Break</script><script>alert(1)</script>' )
		);

		// The raw closing tag must never appear inside the encoded payload.
		$inner = substr( $html, strlen( '<script type="application/ld+json">' ), -strlen( '</script>' ) );
		$this->assertStringNotContainsString( '</script>', $inner );
		$this->assertStringNotContainsString( '<', $inner );
		// The `<` survives only as its `\uXXXX` escape, never as a literal.
		$this->assertStringContainsStringIgnoringCase( '\u003c', $inner );
	}

	public function test_escapes_ampersand(): void {
		$html  = ( new JsonLdScript() )->render( array( 'name' => 'Arts & Sciences' ) );
		$inner = substr( $html, strlen( '<script type="application/ld+json">' ), -strlen( '</script>' ) );

		$this->assertStringNotContainsString( '&', $inner );
		// The `&` survives only as its `\uXXXX` escape, never as a literal.
		$this->assertStringContainsStringIgnoringCase( '\u0026', $inner );
	}

	public function test_inner_payload_is_valid_json_that_round_trips(): void {
		$document = array(
			'@context' => 'https://schema.org',
			'name'     => 'Arts & <Sciences>',
		);

		$html  = ( new JsonLdScript() )->render( $document );
		$inner = substr( $html, strlen( '<script type="application/ld+json">' ), -strlen( '</script>' ) );

		$decoded = json_decode( $inner, true, 512, JSON_THROW_ON_ERROR );
		$this->assertSame( $document, $decoded, 'Escaping must not corrupt the decoded values.' );
	}

	public function test_wraps_a_real_program_schema_document(): void {
		$document = ( new ProgramSchema() )->build(
			array(
				'name'          => 'MSc Data Science',
				'provider_name' => 'Example & Co',
			)
		);

		$html  = ( new JsonLdScript() )->render( $document );
		$inner = substr( $html, strlen( '<script type="application/ld+json">' ), -strlen( '</script>' ) );

		$decoded = json_decode( $inner, true, 512, JSON_THROW_ON_ERROR );
		$this->assertSame( 'EducationalOccupationalProgram', $decoded['@type'] );
		$this->assertSame( 'Example & Co', $decoded['provider']['name'] );
	}
}
