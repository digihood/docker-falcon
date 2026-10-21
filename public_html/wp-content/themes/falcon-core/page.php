<?php
/**
 * Stránka – obsah je složený z bloků.
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	if ( ! fc_starts_with_hero() ) :
		?>
		<?php get_template_part( 'template-parts/page-head' ); ?>
	<?php endif; ?>
	<div class="fc-content">
		<?php the_content(); ?>
	</div>
	<?php
endwhile;

get_footer();
