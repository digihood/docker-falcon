<?php
/**
 * Blok: Karty s ikonami.
 *
 * @var array $block
 */

defined( 'ABSPATH' ) || exit;

$items   = get_field( 'items' ) ?: [];
$columns = get_field( 'columns' ) ?: '3';
?>
<section <?php echo fc_block_attrs( $block, 'fc-section fc-features ' . fc_bg_class() ); ?>>
	<div class="fc-container">
		<?php get_template_part( 'template-parts/section-head' ); ?>

		<?php if ( $items ) : ?>
			<div class="fc-grid fc-grid--<?php echo esc_attr( $columns ); ?>">
				<?php foreach ( $items as $item ) : ?>
					<?php get_template_part( 'template-parts/card-feature', null, [ 'item' => $item ] ); ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
