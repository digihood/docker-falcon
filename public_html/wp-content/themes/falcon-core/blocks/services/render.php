<?php
/**
 * Blok: Služby – karty.
 *
 * @var array $block
 */

defined( 'ABSPATH' ) || exit;

$items = get_field( 'items' ) ?: [];
?>
<section <?php echo fc_block_attrs( $block, 'fc-section fc-services ' . fc_bg_class() ); ?>>
	<div class="fc-container">
		<?php get_template_part( 'template-parts/section-head' ); ?>

		<?php if ( $items ) : ?>
			<?php $fc_count = count( $items ); ?>
			<div class="fc-grid fc-grid--<?php echo 4 === $fc_count ? '4' : ( 0 === $fc_count % 3 || $fc_count > 4 ? '3' : '2' ); ?>">
				<?php foreach ( $items as $item ) : ?>
					<?php get_template_part( 'template-parts/card-service', null, [ 'item' => $item ] ); ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
