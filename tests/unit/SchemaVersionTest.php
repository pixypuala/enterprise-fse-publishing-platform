<?php
/**
 * Unit tests for the migration ledger.
 *
 * @package Pixyville\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixyville\EnterprisePublishing\Tests;

use Pixyville\EnterprisePublishing\Migrations\SchemaVersion;
use PHPUnit\Framework\TestCase;

final class SchemaVersionTest extends TestCase {

	public function test_fresh_install_runs_every_step(): void {
		$schema  = new SchemaVersion();
		$pending = $schema->pending( 0 );

		$this->assertSame( array( 1, 2 ), array_keys( $pending ) );
		$this->assertFalse( $schema->is_current( 0 ) );
	}

	public function test_partial_install_runs_only_remaining_steps(): void {
		$schema = new SchemaVersion();
		$this->assertSame( array( 2 ), array_keys( $schema->pending( 1 ) ) );
	}

	public function test_current_install_has_no_pending_steps(): void {
		$schema = new SchemaVersion();
		$this->assertTrue( $schema->is_current( SchemaVersion::CURRENT ) );
		$this->assertSame( array(), $schema->pending( SchemaVersion::CURRENT ) );
	}

	public function test_downgrade_is_refused_loudly(): void {
		$schema = new SchemaVersion();
		$this->expectException( \OutOfRangeException::class );
		$schema->pending( SchemaVersion::CURRENT + 1 );
	}
}
