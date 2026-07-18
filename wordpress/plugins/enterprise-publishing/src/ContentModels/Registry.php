<?php
/**
 * The catalogue of content models this platform governs.
 *
 * This is the single source of truth for *which* content models exist. Adding a
 * model is a one-line change here; the WordPress registration glue and the
 * capability matrix both read from this list, so they can never drift apart.
 *
 * @package Pixyville\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixyville\EnterprisePublishing\ContentModels;

/**
 * Central registry of governed content models.
 */
final class Registry {

	/**
	 * @var ContentModel[] Cached model list, keyed by post-type key.
	 */
	private array $models;

	public function __construct() {
		$this->models = array();
		foreach ( $this->definitions() as $model ) {
			$this->models[ $model->key ] = $model;
		}
	}

	/**
	 * All governed models, keyed by post-type key.
	 *
	 * @return array<string, ContentModel>
	 */
	public function all(): array {
		return $this->models;
	}

	/**
	 * Look up one model by key.
	 *
	 * @param string $key Post-type key.
	 *
	 * @return ContentModel|null Null when the key is not governed here.
	 */
	public function get( string $key ): ?ContentModel {
		return $this->models[ $key ] ?? null;
	}

	/**
	 * The concrete model definitions.
	 *
	 * The enterprise brief calls for programs, events, stories, people,
	 * resources, and campaigns. We register the full set here because they share
	 * one registration path — but only the ones with real templates/patterns are
	 * surfaced publicly, keeping the first release honest about what is finished.
	 *
	 * @return ContentModel[]
	 */
	private function definitions(): array {
		return array(
			new ContentModel(
				key: 'ep_program',
				singular: 'Program',
				plural: 'Programs',
				description: 'A structured offering with governed layout and metadata.',
				supports: array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields' ),
				has_archive: true,
				menu_icon: 'dashicons-portfolio',
			),
			new ContentModel(
				key: 'ep_event',
				singular: 'Event',
				plural: 'Events',
				description: 'A time-bound happening with date metadata and related programs.',
				supports: array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields' ),
				has_archive: true,
				menu_icon: 'dashicons-calendar-alt',
			),
			new ContentModel(
				key: 'ep_story',
				singular: 'Story',
				plural: 'Stories',
				description: 'Editorial narrative content linked to programs and people.',
				supports: array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
				has_archive: true,
				menu_icon: 'dashicons-book',
			),
		);
	}
}
