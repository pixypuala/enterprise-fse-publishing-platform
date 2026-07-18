<?php
/**
 * Unit tests for the optional AI content assistant.
 *
 * These tests are the guard rail on the "AI off by default, permission-bound"
 * promise: no assistant may act unless it is both enabled and permitted.
 *
 * @package Pixypuala\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixypuala\EnterprisePublishing\Tests;

use Pixypuala\EnterprisePublishing\Ai\ExampleContentAssistant;
use Pixypuala\EnterprisePublishing\Ai\NullContentAssistant;
use PHPUnit\Framework\TestCase;

final class ContentAssistantTest extends TestCase {

	public function test_null_assistant_is_disabled_and_never_suggests(): void {
		$assistant = new NullContentAssistant();

		$this->assertFalse( $assistant->is_enabled() );
		$this->assertNull( $assistant->suggest( 'Summarise this program' ) );
	}

	public function test_example_assistant_is_disabled_by_default(): void {
		$assistant = new ExampleContentAssistant();

		$this->assertFalse( $assistant->is_enabled() );
		$this->assertNull( $assistant->suggest( 'Summarise this program' ) );
	}

	public function test_example_assistant_needs_both_enable_and_permission(): void {
		$enabled_no_permission  = new ExampleContentAssistant( enabled: true, has_permission: false );
		$permission_not_enabled = new ExampleContentAssistant( enabled: false, has_permission: true );

		$this->assertFalse( $enabled_no_permission->is_enabled() );
		$this->assertNull( $enabled_no_permission->suggest( 'Summarise this program' ) );

		$this->assertFalse( $permission_not_enabled->is_enabled() );
		$this->assertNull( $permission_not_enabled->suggest( 'Summarise this program' ) );
	}

	public function test_example_assistant_suggests_when_enabled_and_permitted(): void {
		$assistant = new ExampleContentAssistant( enabled: true, has_permission: true );

		$this->assertTrue( $assistant->is_enabled() );
		$this->assertSame(
			'Suggested summary: Summarise this program.',
			$assistant->suggest( 'Summarise this program.' )
		);
	}

	public function test_enabled_assistant_returns_null_for_empty_prompt(): void {
		$assistant = new ExampleContentAssistant( enabled: true, has_permission: true );

		$this->assertNull( $assistant->suggest( '   ' ) );
	}
}
