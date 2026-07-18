<?php
/**
 * Thin WordPress glue that emits a program's JSON-LD into the document head.
 *
 * This is the boundary layer for structured data: on a singular program view it
 * reads the post's durable fields, delegates the document shape to the
 * framework-free ProgramSchema and the safe encoding to JsonLdScript, then
 * echoes the result on `wp_head`. All correctness (shape, required fields,
 * escaping) lives in the unit-tested domain classes; this class only wires them.
 *
 * @package Pixypuala\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixypuala\EnterprisePublishing\Seo;

/**
 * Prints program JSON-LD structured data in the head.
 */
final class ProgramSchemaHead {

	public function __construct(
		private readonly ProgramSchema $schema = new ProgramSchema(),
		private readonly JsonLdScript $script = new JsonLdScript(),
	) {}

	/**
	 * Attach the head output hook.
	 */
	public function register(): void {
		add_action( 'wp_head', array( $this, 'render' ) );
	}

	/**
	 * Echo the JSON-LD block on a singular program view.
	 */
	public function render(): void {
		if ( ! is_singular( 'ep_program' ) ) {
			return;
		}

		$post = get_post();
		if ( ! $post instanceof \WP_Post ) {
			return;
		}

		$title = get_the_title( $post );
		if ( '' === trim( $title ) ) {
			// A program with no title cannot be described; emit nothing rather
			// than invalid structured data. This is not an error path — a titled
			// program (the normal case) always renders.
			return;
		}

		$document = $this->schema->build(
			array(
				'name'          => $title,
				'url'           => (string) get_permalink( $post ),
				'description'   => wp_strip_all_tags( get_the_excerpt( $post ) ),
				'start_date'    => (string) get_post_meta( $post->ID, '_ep_start_date', true ),
				'end_date'      => (string) get_post_meta( $post->ID, '_ep_end_date', true ),
				'provider_name' => (string) get_bloginfo( 'name' ),
				'provider_url'  => home_url(),
			)
		);

		// JsonLdScript returns a script-safe string (JSON_HEX_TAG | JSON_HEX_AMP);
		// WordPress has no core escaper for a JSON-LD sink, so it is echoed as-is.
		echo $this->script->render( $document ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
