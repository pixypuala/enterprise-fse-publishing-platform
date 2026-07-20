<?php
/**
 * Server render for the Program List block.
 *
 * Dynamic block: markup is authored here, never persisted in post content. It
 * reuses the domain Registry to resolve the governed program post type, queries
 * published programs, and emits an accessible list wired to the Interactivity
 * API. Every dynamic value is escaped at output. The client-side name filter is
 * declared with `data-wp-*` attributes and driven by the block's view module.
 *
 * WordPress includes this file at file scope, so every local it declared used to
 * become a global. The body therefore lives in one prefixed function, leaving no
 * file-scope variable behind to collide with another plugin's state.
 *
 * @package Pixypuala\EnterprisePublishing
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner block markup (unused; dynamic block).
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

// Abort if accessed directly — block render files must only run inside WordPress.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'enterprise_publishing_render_program_list' ) ) {
	/**
	 * Echo the Program List markup for a set of block attributes.
	 *
	 * Echoes rather than returns so each dynamic value stays escaped at its own
	 * point of output, which is what keeps the escaping auditable.
	 *
	 * @param array<string, mixed> $attributes Block attributes.
	 * @return void
	 */
	function enterprise_publishing_render_program_list( array $attributes ): void {
		$max_items = isset( $attributes['maxItems'] ) ? (int) $attributes['maxItems'] : 6;
		$max_items = max( 1, min( 50, $max_items ) );

		$registry  = new \Pixypuala\EnterprisePublishing\ContentModels\Registry();
		$model     = $registry->get( 'ep_program' );
		$post_type = $model instanceof \Pixypuala\EnterprisePublishing\ContentModels\ContentModel
			? $model->key
			: 'ep_program';

		$query = new WP_Query(
			array(
				'post_type'           => $post_type,
				'post_status'         => 'publish',
				'posts_per_page'      => $max_items,
				'orderby'             => 'title',
				'order'               => 'ASC',
				'no_found_rows'       => true,
				'ignore_sticky_posts' => true,
			)
		);

		$wrapper  = get_block_wrapper_attributes(
			array( 'data-wp-interactive' => 'enterprise-publishing/program-list' )
		);
		$input_id = wp_unique_id( 'ep-program-filter-' );

		// phpcs:disable Generic.WhiteSpace.ScopeIndent -- Leading whitespace in the
		// template below is emitted markup, not code indentation; indenting it to
		// the function's scope would change the HTML this block outputs.
		?>
<div <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() returns pre-escaped attributes. ?>>
	<?php if ( $query->have_posts() ) : ?>
		<div class="wp-block-enterprise-publishing-program-list__controls">
			<label class="wp-block-enterprise-publishing-program-list__label" for="<?php echo esc_attr( $input_id ); ?>">
				<?php esc_html_e( 'Filter programs by name', 'enterprise-publishing' ); ?>
			</label>
			<input
				id="<?php echo esc_attr( $input_id ); ?>"
				class="wp-block-enterprise-publishing-program-list__search"
				type="search"
				inputmode="search"
				autocomplete="off"
				data-wp-on--input="actions.updateQuery"
			/>
		</div>
		<ul class="wp-block-enterprise-publishing-program-list__items" role="list">
			<?php
			while ( $query->have_posts() ) :
				$query->the_post();
				$title   = get_the_title();
				$context = wp_json_encode(
					array( 'searchText' => strtolower( wp_strip_all_tags( $title ) ) )
				);
				?>
				<li
					class="wp-block-enterprise-publishing-program-list__item"
					data-wp-context="<?php echo esc_attr( (string) $context ); ?>"
					data-wp-bind--hidden="state.isHidden"
				>
					<a class="wp-block-enterprise-publishing-program-list__link" href="<?php echo esc_url( (string) get_permalink() ); ?>">
						<?php echo esc_html( $title ); ?>
					</a>
				</li>
				<?php
			endwhile;
			?>
		</ul>
	<?php else : ?>
		<p class="wp-block-enterprise-publishing-program-list__empty">
			<?php esc_html_e( 'No programs have been published yet.', 'enterprise-publishing' ); ?>
		</p>
	<?php endif; ?>
</div>
<?php
		// phpcs:enable Generic.WhiteSpace.ScopeIndent
		wp_reset_postdata();
	}
}

enterprise_publishing_render_program_list( is_array( $attributes ) ? $attributes : array() );
