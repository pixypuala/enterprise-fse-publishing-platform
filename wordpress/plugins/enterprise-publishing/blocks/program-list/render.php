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

$ep_max_items = isset( $attributes['maxItems'] ) ? (int) $attributes['maxItems'] : 6;
$ep_max_items = max( 1, min( 50, $ep_max_items ) );

$ep_registry  = new \Pixypuala\EnterprisePublishing\ContentModels\Registry();
$ep_model     = $ep_registry->get( 'ep_program' );
$ep_post_type = $ep_model instanceof \Pixypuala\EnterprisePublishing\ContentModels\ContentModel
	? $ep_model->key
	: 'ep_program';

$ep_query = new WP_Query(
	array(
		'post_type'           => $ep_post_type,
		'post_status'         => 'publish',
		'posts_per_page'      => $ep_max_items,
		'orderby'             => 'title',
		'order'               => 'ASC',
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
	)
);

$ep_wrapper  = get_block_wrapper_attributes(
	array( 'data-wp-interactive' => 'enterprise-publishing/program-list' )
);
$ep_input_id = wp_unique_id( 'ep-program-filter-' );
?>
<div <?php echo $ep_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() returns pre-escaped attributes. ?>>
	<?php if ( $ep_query->have_posts() ) : ?>
		<div class="wp-block-enterprise-publishing-program-list__controls">
			<label class="wp-block-enterprise-publishing-program-list__label" for="<?php echo esc_attr( $ep_input_id ); ?>">
				<?php esc_html_e( 'Filter programs by name', 'enterprise-publishing' ); ?>
			</label>
			<input
				id="<?php echo esc_attr( $ep_input_id ); ?>"
				class="wp-block-enterprise-publishing-program-list__search"
				type="search"
				inputmode="search"
				autocomplete="off"
				data-wp-on--input="actions.updateQuery"
			/>
		</div>
		<ul class="wp-block-enterprise-publishing-program-list__items" role="list">
			<?php
			while ( $ep_query->have_posts() ) :
				$ep_query->the_post();
				$ep_title   = get_the_title();
				$ep_context = wp_json_encode(
					array( 'searchText' => strtolower( wp_strip_all_tags( $ep_title ) ) )
				);
				?>
				<li
					class="wp-block-enterprise-publishing-program-list__item"
					data-wp-context="<?php echo esc_attr( (string) $ep_context ); ?>"
					data-wp-bind--hidden="state.isHidden"
				>
					<a class="wp-block-enterprise-publishing-program-list__link" href="<?php echo esc_url( (string) get_permalink() ); ?>">
						<?php echo esc_html( $ep_title ); ?>
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
wp_reset_postdata();
