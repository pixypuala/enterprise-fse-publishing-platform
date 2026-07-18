<?php
/**
 * Admin health/status screen.
 *
 * Surfaces the platform's operational state to administrators: governed models,
 * their post counts, and the current vs. expected schema version. This is the
 * "can I trust this install?" screen the product brief calls for.
 *
 * @package Pixypuala\EnterprisePublishing
 */

declare( strict_types=1 );

namespace Pixypuala\EnterprisePublishing\Admin;

use Pixypuala\EnterprisePublishing\ContentModels\Registry;
use Pixypuala\EnterprisePublishing\Migrations\SchemaVersion;

/**
 * Renders the Publishing Health admin page.
 */
final class HealthScreen {

	private const OPTION = 'enterprise_publishing_schema_version';

	public function __construct(
		private readonly Registry $registry,
		private readonly SchemaVersion $schema,
	) {}

	/**
	 * Add the health page under the Tools menu. Requires `manage_options`.
	 */
	public function register_menu(): void {
		add_management_page(
			'Publishing Health',
			'Publishing Health',
			'manage_options',
			'enterprise-publishing-health',
			array( $this, 'render' )
		);
	}

	/**
	 * Render the page. All dynamic values are escaped on output.
	 */
	public function render(): void {
		// Server-authoritative gate: never render on capability the menu implies.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to view this page.', 'enterprise-publishing' ) );
		}

		$installed  = (int) get_option( self::OPTION, 0 );
		$is_current = $this->schema->is_current( $installed );

		echo '<div class="wrap">';
		echo '<h1>' . esc_html__( 'Publishing Health', 'enterprise-publishing' ) . '</h1>';

		// Schema status.
		echo '<h2>' . esc_html__( 'Schema', 'enterprise-publishing' ) . '</h2>';
		printf(
			'<p>%s <strong>%d</strong> / %s <strong>%d</strong> — %s</p>',
			esc_html__( 'Installed version:', 'enterprise-publishing' ),
			(int) $installed,
			esc_html__( 'expected:', 'enterprise-publishing' ),
			(int) SchemaVersion::CURRENT,
			$is_current
				? '<span style="color:#137333;">' . esc_html__( 'up to date', 'enterprise-publishing' ) . '</span>'
				: '<span style="color:#b32d2e;">' . esc_html__( 'migration pending', 'enterprise-publishing' ) . '</span>'
		);

		// Governed models and their counts.
		echo '<h2>' . esc_html__( 'Governed content models', 'enterprise-publishing' ) . '</h2>';
		echo '<table class="widefat striped"><thead><tr>';
		echo '<th>' . esc_html__( 'Model', 'enterprise-publishing' ) . '</th>';
		echo '<th>' . esc_html__( 'Key', 'enterprise-publishing' ) . '</th>';
		echo '<th>' . esc_html__( 'Published', 'enterprise-publishing' ) . '</th>';
		echo '</tr></thead><tbody>';

		foreach ( $this->registry->all() as $model ) {
			$count     = wp_count_posts( $model->key );
			$published = isset( $count->publish ) ? (int) $count->publish : 0;
			printf(
				'<tr><td>%s</td><td><code>%s</code></td><td>%d</td></tr>',
				esc_html( $model->plural ),
				esc_html( $model->key ),
				(int) $published
			);
		}

		echo '</tbody></table>';
		echo '</div>';
	}
}
