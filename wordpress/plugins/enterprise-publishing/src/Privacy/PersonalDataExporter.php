<?php
/**
 * Shapes owned records into the WordPress personal-data exporter return value.
 *
 * A WordPress exporter callback must return an array of grouped data rows plus a
 * `done` flag (WordPress paginates by calling the callback repeatedly). This
 * class performs that shaping in pure PHP, so the exact structure a data-subject
 * receives is asserted by unit tests rather than discovered in production. The
 * WordPress glue supplies the records and forwards this return value verbatim.
 *
 * @package Pixypuala\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixypuala\EnterprisePublishing\Privacy;

/**
 * Builds the WordPress exporter data structure.
 */
final class PersonalDataExporter {

	private const GROUP_ID    = 'enterprise-publishing';
	private const GROUP_LABEL = 'Enterprise Publishing';

	/**
	 * Shape a page of owned records into the exporter return value.
	 *
	 * @param OwnedRecord[] $records Records owned by the person for this page.
	 * @param bool          $done    Whether this is the final page (no more data).
	 *
	 * @return array{
	 *     data: list<array{group_id:string, group_label:string, item_id:string, data:list<array{name:string, value:string}>}>,
	 *     done: bool
	 * }
	 */
	public function export( array $records, bool $done = true ): array {
		$data = array();
		foreach ( $records as $record ) {
			$data[] = array(
				'group_id'    => self::GROUP_ID,
				'group_label' => self::GROUP_LABEL,
				'item_id'     => $record->export_item_id(),
				'data'        => $record->as_export_fields(),
			);
		}

		return array(
			'data' => $data,
			'done' => $done,
		);
	}
}
