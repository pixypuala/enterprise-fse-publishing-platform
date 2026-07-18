<?php
/**
 * A single piece of plugin-owned personal data belonging to one user.
 *
 * WordPress' privacy tools ask each plugin two questions about a person: "what
 * of theirs do you hold?" (export) and "what of theirs can you remove?" (erase).
 * This value object answers both for one record. It is deliberately
 * framework-free — it calls no WordPress functions — so the export/erase shapes
 * built from it are fully unit-testable without a WordPress install. The thin
 * WordPress glue is responsible only for collecting these records and executing
 * the removal plan.
 *
 * @package Pixypuala\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixypuala\EnterprisePublishing\Privacy;

/**
 * Immutable description of one owned personal-data record.
 */
final class OwnedRecord {

	/**
	 * @param string                $id               Stable identifier for the record within this plugin.
	 * @param string                $label            Human label describing what the record is.
	 * @param array<string, string> $fields           Name => value pairs disclosed on export.
	 * @param bool                  $removable        Whether erasure may remove this record.
	 * @param string                $retention_reason Why the record is retained (required when not removable).
	 *
	 * @throws \InvalidArgumentException When the id is empty, or a retained record gives no reason.
	 */
	public function __construct(
		private readonly string $id,
		private readonly string $label,
		private readonly array $fields,
		private readonly bool $removable = true,
		private readonly string $retention_reason = '',
	) {
		if ( '' === trim( $id ) ) {
			throw new \InvalidArgumentException( 'An owned record requires a non-empty id.' );
		}
		if ( ! $removable && '' === trim( $retention_reason ) ) {
			throw new \InvalidArgumentException(
				'A retained record must state why it is retained, so the person receives an honest answer.'
			);
		}
	}

	/**
	 * The record's identifier, used by the erase plan to target removal.
	 */
	public function id(): string {
		return $this->id;
	}

	/**
	 * Whether erasure is permitted to remove this record.
	 */
	public function is_removable(): bool {
		return $this->removable;
	}

	/**
	 * Message explaining why a retained record was kept (empty when removable).
	 */
	public function retention_message(): string {
		return $this->retention_reason;
	}

	/**
	 * Namespaced item id for the WordPress exporter group.
	 */
	public function export_item_id(): string {
		return 'enterprise-publishing-' . $this->id;
	}

	/**
	 * The disclosed fields in WordPress exporter shape.
	 *
	 * @return list<array{name:string, value:string}>
	 */
	public function as_export_fields(): array {
		$rows = array(
			array(
				'name'  => 'Record',
				'value' => $this->label,
			),
		);
		foreach ( $this->fields as $name => $value ) {
			$rows[] = array(
				'name'  => (string) $name,
				'value' => (string) $value,
			);
		}
		return $rows;
	}
}
