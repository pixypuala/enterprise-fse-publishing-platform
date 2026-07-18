<?php
/**
 * Renders a JSON-LD document into a script-safe `<script>` string.
 *
 * A JSON-LD document echoed inside `<script type="application/ld+json">` is a
 * hostile sink: an unescaped `<` (as in `</script>`) breaks out of the tag, and
 * `&` can start an entity. This renderer encodes with `JSON_HEX_TAG` and
 * `JSON_HEX_AMP` so every `<`, `>`, and `&` becomes a `\uXXXX` escape, making the
 * output impossible to break out of. It is framework-free (native `json_encode`,
 * no `wp_json_encode`) so the escaping guarantee is unit-tested without
 * WordPress; the `wp_head` glue only echoes the returned string.
 *
 * @package Pixypuala\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixypuala\EnterprisePublishing\Seo;

/**
 * Encodes and wraps a JSON-LD document for safe HTML output.
 */
final class JsonLdScript {

	private const OPEN  = '<script type="application/ld+json">';
	private const CLOSE = '</script>';

	/**
	 * Render a document as a script-safe JSON-LD block.
	 *
	 * @param array<string, mixed> $document JSON-LD data (e.g. from ProgramSchema).
	 *
	 * @return string The complete `<script type="application/ld+json">…</script>` string.
	 *
	 * @throws \JsonException When the document cannot be encoded (fail loudly, never emit half a tag).
	 */
	public function render( array $document ): string {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode -- framework-free: wp_json_encode is unavailable without WordPress and this class is unit-tested standalone.
		$json = json_encode(
			$document,
			JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
		);

		return self::OPEN . $json . self::CLOSE;
	}
}
