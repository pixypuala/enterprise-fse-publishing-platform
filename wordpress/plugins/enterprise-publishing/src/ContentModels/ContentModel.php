<?php
/**
 * A single governed content model (e.g. "program", "event").
 *
 * This is a plain, framework-free value object. It holds the *durable*
 * definition of a content type — the part that must survive a theme swap or a
 * WordPress version bump — separately from the WordPress glue that registers it.
 * Keeping it free of WordPress function calls is what makes the domain rules
 * unit-testable without booting WordPress.
 *
 * @package Pixypuala\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixypuala\EnterprisePublishing\ContentModels;

/**
 * Immutable description of one content model.
 */
final class ContentModel {

	/**
	 * @param string   $key          Post-type key, e.g. "ep_program". Must be
	 *                                <= 20 chars (WordPress post-type limit) and
	 *                                lowercase alphanumeric/underscore.
	 * @param string   $singular     Human singular label, e.g. "Program".
	 * @param string   $plural       Human plural label, e.g. "Programs".
	 * @param string   $description  One-line editor-facing description.
	 * @param string[] $supports     WordPress "supports" features, e.g. title, editor.
	 * @param bool     $has_archive  Whether the model exposes a public archive.
	 * @param string   $menu_icon    Dashicon slug for the admin menu.
	 */
	public function __construct(
		public readonly string $key,
		public readonly string $singular,
		public readonly string $plural,
		public readonly string $description,
		public readonly array $supports,
		public readonly bool $has_archive,
		public readonly string $menu_icon,
	) {
		$this->assert_valid_key( $key );
	}

	/**
	 * Reject an invalid post-type key at the boundary rather than letting
	 * WordPress silently truncate or ignore it later.
	 *
	 * @param string $key Candidate post-type key.
	 *
	 * @throws \InvalidArgumentException When the key breaks WordPress' rules.
	 */
	private function assert_valid_key( string $key ): void {
		if ( '' === $key || strlen( $key ) > 20 ) {
			throw new \InvalidArgumentException(
				// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Developer-facing exception message from the framework-free domain; the key is source-declared, never user input, and never rendered.
				sprintf( 'Content model key "%s" must be 1-20 characters.', $key )
			);
		}
		if ( ! preg_match( '/^[a-z][a-z0-9_]*$/', $key ) ) {
			throw new \InvalidArgumentException(
				// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Developer-facing exception message from the framework-free domain; the key is source-declared, never user input, and never rendered.
				sprintf( 'Content model key "%s" must be lowercase [a-z0-9_] starting with a letter.', $key )
			);
		}
	}

	/**
	 * The capability "base" WordPress derives granular capabilities from.
	 *
	 * We use a custom base per model (rather than the shared "post" base) so an
	 * editor can be granted rights to Programs without inheriting rights to core
	 * Posts. This is the hinge of the governance model.
	 *
	 * @return array{singular:string, plural:string} Capability singular/plural bases.
	 */
	public function capability_base(): array {
		return array(
			'singular' => $this->key,
			'plural'   => $this->key . 's',
		);
	}
}
