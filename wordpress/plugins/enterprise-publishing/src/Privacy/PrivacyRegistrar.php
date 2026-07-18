<?php
/**
 * Thin WordPress glue for the personal-data exporter and eraser.
 *
 * WordPress core already authenticates and orchestrates data-subject requests
 * (admins act on a confirmed request); these callbacks only run inside that
 * gated flow. This class registers them, collects the person's owned records
 * across governed models, delegates the shape/plan to the framework-free
 * PersonalDataExporter/PersonalDataEraser, and executes the removal plan.
 *
 * @package Pixypuala\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixypuala\EnterprisePublishing\Privacy;

use Pixypuala\EnterprisePublishing\ContentModels\Registry;

/**
 * Registers and services the WordPress privacy callbacks.
 */
final class PrivacyRegistrar {

	/**
	 * Plugin-owned meta key recording who submitted a draft for approval. This is
	 * the personal trace erasure removes; the content itself follows core rules.
	 */
	private const APPROVAL_META = '_ep_approval_submitted_by';

	private const EXPORTER_KEY = 'enterprise-publishing';

	public function __construct(
		private readonly Registry $registry,
		private readonly PersonalDataExporter $exporter = new PersonalDataExporter(),
		private readonly PersonalDataEraser $eraser = new PersonalDataEraser(),
	) {}

	/**
	 * Attach the exporter/eraser registration filters.
	 */
	public function register(): void {
		add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_exporter' ) );
		add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_eraser' ) );
	}

	/**
	 * Register this plugin's exporter with WordPress.
	 *
	 * @param array<string, array<string, mixed>> $exporters Registered exporters.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public function register_exporter( array $exporters ): array {
		$exporters[ self::EXPORTER_KEY ] = array(
			'exporter_friendly_name' => __( 'Enterprise Publishing', 'enterprise-publishing' ),
			'callback'               => array( $this, 'export' ),
		);
		return $exporters;
	}

	/**
	 * Register this plugin's eraser with WordPress.
	 *
	 * @param array<string, array<string, mixed>> $erasers Registered erasers.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public function register_eraser( array $erasers ): array {
		$erasers[ self::EXPORTER_KEY ] = array(
			'eraser_friendly_name' => __( 'Enterprise Publishing', 'enterprise-publishing' ),
			'callback'             => array( $this, 'erase' ),
		);
		return $erasers;
	}

	/**
	 * Export the person's owned data (WordPress exporter callback).
	 *
	 * All of a person's records fit one page, so the paginating `$page` argument
	 * WordPress passes is intentionally not accepted.
	 *
	 * @param string $email Person's email address, provided by WordPress core.
	 *
	 * @return array{data:list<array<string, mixed>>, done:bool}
	 */
	public function export( string $email ): array {
		return $this->exporter->export( $this->records_for( $email ) );
	}

	/**
	 * Erase the person's removable owned data (WordPress eraser callback).
	 *
	 * All of a person's records fit one page, so the paginating `$page` argument
	 * WordPress passes is intentionally not accepted.
	 *
	 * @param string $email Person's email address, provided by WordPress core.
	 *
	 * @return array{items_removed:bool, items_retained:bool, messages:list<string>, done:bool}
	 */
	public function erase( string $email ): array {
		$plan = $this->eraser->plan( $this->records_for( $email ) );

		foreach ( $plan['ids_to_remove'] as $post_id ) {
			delete_post_meta( (int) $post_id, self::APPROVAL_META );
		}

		return array(
			'items_removed'  => $plan['items_removed'],
			'items_retained' => $plan['items_retained'],
			'messages'       => $plan['messages'],
			'done'           => $plan['done'],
		);
	}

	/**
	 * Collect the person's owned records across every governed model.
	 *
	 * @param string $email Person's email address.
	 *
	 * @return OwnedRecord[]
	 */
	private function records_for( string $email ): array {
		$user = get_user_by( 'email', $email );
		if ( ! $user instanceof \WP_User ) {
			return array();
		}

		$posts = get_posts(
			array(
				'author'         => $user->ID,
				'post_type'      => array_keys( $this->registry->all() ),
				'post_status'    => 'any',
				'posts_per_page' => 100,
			)
		);

		$records = array();
		foreach ( $posts as $post ) {
			$is_published = 'publish' === $post->post_status;
			$records[]    = new OwnedRecord(
				id: (string) $post->ID,
				label: $post->post_title,
				fields: array(
					'Type'   => $post->post_type,
					'Status' => $post->post_status,
				),
				removable: ! $is_published,
				retention_reason: $is_published
					? 'Published editorial content is retained as a business record; author attribution is anonymised by WordPress core, not deleted here.'
					: '',
			);
		}

		return $records;
	}
}
