<?php
/**
 * Blok: Časté dotazy.
 *
 * @var array $block
 */

defined( 'ABSPATH' ) || exit;

$items = get_field( 'items' ) ?: [];
?>
<section <?php echo fc_block_attrs( $block, 'fc-section fc-faq ' . fc_bg_class() ); ?>>
	<div class="fc-container fc-faq__grid">
		<?php get_template_part( 'template-parts/section-head' ); ?>

		<?php if ( $items ) : ?>
			<div class="fc-faq__list">
				<?php foreach ( $items as $item ) : ?>
					<details class="fc-faq__item">
						<summary><span><?php echo esc_html( $item['q'] ); ?></span><span class="fc-faq__toggle" aria-hidden="true"></span></summary>
						<div class="fc-faq__answer"><?php echo wp_kses_post( $item['a'] ); ?></div>
					</details>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
