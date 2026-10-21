<?php
/**
 * Záložní šablona.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<?php get_template_part( 'template-parts/page-head', null, [ 'title' => is_singular() ? get_the_title() : wp_get_document_title() ] ); ?>
<div class="fc-content">
	<?php
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	?>
</div>
<?php
get_footer();
