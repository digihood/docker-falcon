<?php
/**
 * Blok: Čísla / výhody.
 *
 * @var array $block
 */

defined( 'ABSPATH' ) || exit;

$items = get_field( 'items' ) ?: [];
$note  = get_field( 'note' );
?>
<section <?php echo fc_block_attrs( $block, 'fc-section fc-stats ' . fc_bg_class() ); ?>>
	<div class="fc-container">
		<?php get_template_part( 'template-parts/section-head' ); ?>

		<?php if ( $items ) : ?>
			<dl class="fc-stats__grid" style="--fc-cols: <?php echo (int) min( 4, max( 1, count( $items ) ) ); ?>">
				<?php foreach ( $items as $item ) : ?>
					<div class="fc-stats__item">
						<dt>
							<span class="fc-stats__value"><?php echo esc_html( $item['value'] ); ?></span>
							<span class="fc-stats__label"><?php echo esc_html( $item['label'] ); ?></span>
						</dt>
						<?php if ( ! empty( $item['text'] ) ) : ?>
							<dd class="fc-stats__text"><?php echo esc_html( $item['text'] ); ?></dd>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</dl>
		<?php endif; ?>

		<?php if ( $note ) : ?>
			<p class="fc-note"><?php echo esc_html( $note ); ?></p>
		<?php endif; ?>
	</div>
</section>
