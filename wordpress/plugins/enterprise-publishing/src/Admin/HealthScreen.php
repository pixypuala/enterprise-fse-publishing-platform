<?php
/**
 * Admin health/status screen.
 *
 * Surfaces the platform's operational state to administrators: governed models,
 * their post counts, and the current vs. expected schema version. This is the
 * "can I trust this install?" screen the product brief calls for.
 *
 * Presentation is a stylesheet, not inline attributes: the screen's colours,
 * spacing, and radii are tokens in admin/health.css so a state can never be
 * distinguished by a hard-coded hex buried in a printf(). The stylesheet is
 * registered against this page's own hook suffix, so no other admin screen
 * loads a byte of it.
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

	private const HANDLE = 'enterprise-publishing-health';

	/**
	 * Hook suffix WordPress assigned to this page, or null before the menu is
	 * registered. Assets are gated on it so they load here and on no other screen.
	 *
	 * @var string|null
	 */
	private ?string $hook_suffix = null;

	public function __construct(
		private readonly Registry $registry,
		private readonly SchemaVersion $schema,
	) {}

	/**
	 * Add the health page under the Tools menu. Requires `manage_options`.
	 */
	public function register_menu(): void {
		$hook = add_management_page(
			'Publishing Health',
			'Publishing Health',
			'manage_options',
			'enterprise-publishing-health',
			array( $this, 'render' )
		);

		// False when the current user lacks the capability — then there is no
		// screen to style, and nothing should be enqueued anywhere.
		if ( is_string( $hook ) && '' !== $hook ) {
			$this->hook_suffix = $hook;
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		}
	}

	/**
	 * Enqueue the screen's stylesheet — on this screen only.
	 *
	 * @param string $hook_suffix Hook suffix of the admin page being rendered.
	 */
	public function enqueue_assets( string $hook_suffix ): void {
		if ( $hook_suffix !== $this->hook_suffix ) {
			return;
		}

		/*
		 * The bootstrap's own PLUGIN_FILE, not a path rebuilt from __DIR__:
		 * WordPress maps a plugin file back to its URL through the realpath it
		 * recorded when it loaded that exact file. A reconstructed path misses
		 * that registration on a symlinked install and yields a broken URL.
		 */
		wp_enqueue_style(
			self::HANDLE,
			plugins_url( 'admin/health.css', \Pixypuala\EnterprisePublishing\PLUGIN_FILE ),
			array(),
			\Pixypuala\EnterprisePublishing\VERSION
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

		$installed = (int) get_option( self::OPTION, 0 );
		$pending   = $this->schema->pending( $installed );

		echo '<div class="wrap ep-health">';

		printf(
			'<h1 class="ep-health__title">%s</h1>',
			esc_html__( 'Publishing Health', 'enterprise-publishing' )
		);
		printf(
			'<p class="ep-health__lede">%s</p>',
			esc_html__(
				'The schema version this database is actually at, and every governed content model with its published count.',
				'enterprise-publishing'
			)
		);

		echo '<div class="ep-health__grid">';
		$this->render_schema_card( $installed, $pending );
		$this->render_models_card();
		echo '</div>';

		echo '</div>';
	}

	/**
	 * Render the schema status card.
	 *
	 * Up to date and migration pending are distinct treatments, not one card
	 * with a different word in it: the accent rail, the flag, and the pending
	 * step list all change, so the state is legible before anything is read.
	 *
	 * @param int                $installed Installed schema version.
	 * @param array<int, string> $pending   Migration steps still to run.
	 */
	private function render_schema_card( int $installed, array $pending ): void {
		$is_current = array() === $pending;

		printf(
			'<section class="ep-health__card ep-health__status%s">',
			$is_current ? '' : ' is-pending'
		);

		printf(
			'<span class="ep-health__flag">%s</span>',
			$is_current
				? esc_html__( 'Up to date', 'enterprise-publishing' )
				: esc_html__( 'Migration pending', 'enterprise-publishing' )
		);

		printf(
			'<p class="ep-health__figure">%d<span> / %d</span></p>',
			(int) $installed,
			(int) SchemaVersion::CURRENT
		);

		printf(
			'<p class="ep-health__caption">%s</p>',
			esc_html__(
				'Installed schema version, against the version this build expects.',
				'enterprise-publishing'
			)
		);

		if ( ! $is_current ) {
			echo '<ul class="ep-health__steps">';
			foreach ( $pending as $version => $description ) {
				printf(
					'<li class="ep-health__step"><span><span class="ep-health__stepversion">%s %d</span>%s</span></li>',
					esc_html__( 'Step', 'enterprise-publishing' ),
					(int) $version,
					esc_html( $description )
				);
			}
			echo '</ul>';
		}

		echo '</section>';
	}

	/**
	 * Render the governed models card: one row per model with its published count.
	 */
	private function render_models_card(): void {
		$models = $this->registry->all();
		$counts = array();
		foreach ( $models as $key => $model ) {
			$count          = wp_count_posts( $model->key );
			$counts[ $key ] = isset( $count->publish ) ? (int) $count->publish : 0;
		}

		echo '<section class="ep-health__card">';

		echo '<header class="ep-health__cardhead">';
		printf(
			'<h2 class="ep-health__cardtitle">%s</h2>',
			esc_html__( 'Governed content models', 'enterprise-publishing' )
		);
		printf(
			'<span class="ep-health__cardmeta">%s</span>',
			esc_html(
				sprintf(
					/* translators: %d: number of registered content models. */
					_n(
						'%d model, with its own capability base',
						'%d models, each with its own capability base',
						count( $models ),
						'enterprise-publishing'
					),
					count( $models )
				)
			)
		);
		echo '</header>';

		echo '<table class="ep-health__table"><thead><tr>';
		printf( '<th scope="col">%s</th>', esc_html__( 'Model', 'enterprise-publishing' ) );
		printf( '<th scope="col">%s</th>', esc_html__( 'Post type key', 'enterprise-publishing' ) );
		printf( '<th scope="col">%s</th>', esc_html__( 'Published', 'enterprise-publishing' ) );
		echo '</tr></thead><tbody>';

		foreach ( $models as $key => $model ) {
			$published = $counts[ $key ];
			printf(
				'<tr><td><div class="ep-health__model">%s</div><p class="ep-health__modeldesc">%s</p></td>'
					. '<td><span class="ep-health__key">%s</span></td>'
					. '<td><span class="ep-health__count%s">%d</span><span class="ep-health__countunit">%s</span></td></tr>',
				esc_html( $model->plural ),
				esc_html( $model->description ),
				esc_html( $model->key ),
				0 === $published ? ' is-zero' : '',
				(int) $published,
				esc_html__( 'published', 'enterprise-publishing' )
			);
		}

		echo '</tbody></table>';

		if ( 0 === array_sum( $counts ) ) {
			$this->render_empty_state();
		}

		echo '</section>';
	}

	/**
	 * Render the empty state shown while nothing has been published yet.
	 *
	 * A fresh install is not a fault, so it is not dressed as one: the table
	 * above still stands, and this only adds the next useful action.
	 */
	private function render_empty_state(): void {
		$first = $this->registry->get( 'ep_program' );
		if ( null === $first ) {
			return;
		}

		echo '<div class="ep-health__empty">';
		printf(
			'<p class="ep-health__emptytitle">%s</p>',
			esc_html__( 'Nothing published yet', 'enterprise-publishing' )
		);
		printf(
			'<p class="ep-health__emptytext">%s</p>',
			esc_html__(
				'The models are registered and their capabilities installed — there is simply no published content in them yet. Publishing the first record starts filling the counts above.',
				'enterprise-publishing'
			)
		);
		printf(
			'<a class="ep-health__action" href="%s">%s</a>',
			esc_url( admin_url( 'post-new.php?post_type=' . rawurlencode( $first->key ) ) ),
			esc_html(
				sprintf(
					/* translators: %s: singular content model name, e.g. "Program". */
					__( 'Add the first %s', 'enterprise-publishing' ),
					$first->singular
				)
			)
		);
		echo '</div>';
	}
}
