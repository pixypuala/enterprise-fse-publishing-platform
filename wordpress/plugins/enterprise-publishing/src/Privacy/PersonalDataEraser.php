<?php
/**
 * Shapes owned records into the WordPress personal-data eraser return value.
 *
 * Erasure is a governance decision, not a blanket delete: published editorial
 * content is a business record and must be retained (with an honest explanation
 * to the person), while unpublished personal traces may be removed. This class
 * makes that decision in pure PHP and produces both the WordPress-shaped return
 * value and an explicit removal plan (the ids the glue must delete). Because it
 * is framework-free, the retention policy is unit-tested rather than trusted.
 *
 * @package Pixypuala\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixypuala\EnterprisePublishing\Privacy;

/**
 * Builds the WordPress eraser data structure and its removal plan.
 */
final class PersonalDataEraser {

	/**
	 * Decide which owned records to remove and which to retain.
	 *
	 * @param OwnedRecord[] $records Records owned by the person for this page.
	 * @param bool          $done    Whether this is the final page (no more data).
	 *
	 * @return array{
	 *     items_removed: bool,
	 *     items_retained: bool,
	 *     messages: list<string>,
	 *     ids_to_remove: list<string>,
	 *     done: bool
	 * } WordPress eraser fields plus `ids_to_remove` — the plan the glue executes.
	 */
	public function plan( array $records, bool $done = true ): array {
		$ids_to_remove = array();
		$messages      = array();
		$retained      = false;

		foreach ( $records as $record ) {
			if ( $record->is_removable() ) {
				$ids_to_remove[] = $record->id();
				continue;
			}
			$retained   = true;
			$messages[] = $record->retention_message();
		}

		return array(
			'items_removed'  => array() !== $ids_to_remove,
			'items_retained' => $retained,
			'messages'       => $messages,
			'ids_to_remove'  => $ids_to_remove,
			'done'           => $done,
		);
	}
}
