<?php
/**
 * Title: Program card grid
 * Slug: enterprise-fse/program-card-grid
 * Categories: enterprise-fse
 * Block Types: core/query
 * Description: A responsive grid of Program cards driven by a live query. Demonstrates governed dynamic cards without a custom block build step.
 *
 * @package EnterpriseFSE
 */

?>
<!-- wp:query {"queryId":10,"query":{"perPage":9,"pages":0,"offset":0,"postType":"ep_program","order":"desc","orderBy":"date","inherit":false},"align":"wide","layout":{"type":"default"}} -->
<div class="wp-block-query alignwide">
	<!-- wp:post-template {"layout":{"type":"grid","minimumColumnWidth":"18rem"}} -->
		<!-- wp:group {"style":{"spacing":{"padding":"var:preset|spacing|40"},"border":{"color":"var:preset|color|line","width":"1px","radius":"var:custom|radius|card"}},"backgroundColor":"surface","layout":{"type":"constrained"}} -->
		<div class="wp-block-group has-surface-background-color has-background" style="border-color:var(--wp--preset--color--line);border-width:1px;border-radius:var(--wp--custom--radius--card);padding:var(--wp--preset--spacing--40)">
			<!-- wp:post-featured-image {"isLink":true,"style":{"border":{"radius":"var:custom|radius|control"}},"height":"180px"} /-->
			<!-- wp:post-title {"isLink":true,"fontSize":"large"} /-->
			<!-- wp:post-excerpt {"textColor":"muted","excerptLength":20} /-->
		</div>
		<!-- /wp:group -->
	<!-- /wp:post-template -->

	<!-- wp:query-no-results -->
		<!-- wp:group {"style":{"spacing":{"padding":"var:preset|spacing|60"},"border":{"color":"var:preset|color|line","width":"1px","radius":"var:custom|radius|card"}},"layout":{"type":"constrained","contentSize":"32rem"}} -->
		<div class="wp-block-group" style="border-color:var(--wp--preset--color--line);border-width:1px;border-radius:var(--wp--custom--radius--card);padding:var(--wp--preset--spacing--60)">
			<!-- wp:heading {"level":2,"textAlign":"center","fontSize":"large"} -->
			<h2 class="wp-block-heading has-text-align-center has-large-font-size">No programs yet</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"align":"center","textColor":"muted"} -->
			<p class="has-text-align-center has-muted-color has-text-color">Programs published by editors will appear here as an accessible, responsive grid.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
	<!-- /wp:query-no-results -->
</div>
<!-- /wp:query -->
