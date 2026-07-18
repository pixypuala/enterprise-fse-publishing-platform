<?php
/**
 * Schema.org JSON-LD builder for the "program" content model.
 *
 * Enterprise publishing sites live or die by how machines read them: search
 * engines, aggregators, and assistants all consume structured data. This builder
 * turns a program's durable fields into a Schema.org `EducationalOccupationalProgram`
 * document. It is deliberately framework-free — it emits a plain data array, so it
 * is fully unit-testable without WordPress, and the WordPress glue is responsible
 * for encoding and escaping it at output (`wp_json_encode` inside a script tag).
 *
 * @package Pixypuala\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixypuala\EnterprisePublishing\Seo;

/**
 * Builds Schema.org JSON-LD for a program.
 */
final class ProgramSchema {

	private const CONTEXT = 'https://schema.org';
	private const TYPE    = 'EducationalOccupationalProgram';

	/**
	 * Build the JSON-LD document for one program.
	 *
	 * Required fields are asserted at the boundary so a malformed program fails
	 * loudly rather than emitting invalid structured data. Optional fields are
	 * omitted entirely when absent (empty string), because emitting empty
	 * Schema.org properties is worse than omitting them.
	 *
	 * @param array{
	 *     name:string,
	 *     url?:string,
	 *     description?:string,
	 *     provider_name?:string,
	 *     provider_url?:string,
	 *     start_date?:string,
	 *     end_date?:string
	 * } $program Program fields.
	 *
	 * @return array<string, mixed> Ordered, deterministic JSON-LD data.
	 *
	 * @throws \InvalidArgumentException When the required name is missing.
	 */
	public function build( array $program ): array {
		$name = trim( (string) ( $program['name'] ?? '' ) );
		if ( '' === $name ) {
			throw new \InvalidArgumentException( 'A program requires a non-empty name for structured data.' );
		}

		$document = array(
			'@context' => self::CONTEXT,
			'@type'    => self::TYPE,
			'name'     => $name,
		);

		$this->add_optional( $document, 'url', $program['url'] ?? '' );
		$this->add_optional( $document, 'description', $program['description'] ?? '' );
		$this->add_optional( $document, 'startDate', $program['start_date'] ?? '' );
		$this->add_optional( $document, 'endDate', $program['end_date'] ?? '' );

		$provider = $this->build_provider( $program );
		if ( array() !== $provider ) {
			$document['provider'] = $provider;
		}

		return $document;
	}

	/**
	 * Build the nested Organization provider node, omitting it when unknown.
	 *
	 * @param array<string, string> $program Program fields.
	 *
	 * @return array<string, string> Provider node, or empty when no provider name.
	 */
	private function build_provider( array $program ): array {
		$provider_name = trim( (string) ( $program['provider_name'] ?? '' ) );
		if ( '' === $provider_name ) {
			return array();
		}
		$provider = array(
			'@type' => 'Organization',
			'name'  => $provider_name,
		);
		$this->add_optional( $provider, 'url', $program['provider_url'] ?? '' );
		return $provider;
	}

	/**
	 * Append a property only when its trimmed value is non-empty.
	 *
	 * @param array<string, string> $target   Document being built (by reference).
	 * @param string                $property Schema.org property name.
	 * @param string                $value    Candidate value.
	 */
	private function add_optional( array &$target, string $property, string $value ): void {
		$value = trim( $value );
		if ( '' !== $value ) {
			$target[ $property ] = $value;
		}
	}
}
