<?php
/**
 * Blok: Postup (kroky).
 *
 * @var array $block
 */

defined( 'ABSPATH' ) || exit;

$steps = get_field( 'steps' ) ?: [];
?>
<section <?php echo fc_block_attrs( $block, 'fc-section fc-process ' . fc_bg_class() ); ?>>
	<div class="fc-container">
		<?php get_template_part( 'template-parts/section-head' ); ?>

		<?php if ( $steps ) : ?>
			<ol class="fc-process__list" style="--fc-steps: <?php echo (int) count( $steps ); ?>">
				<?php foreach ( $steps as $i => $step ) : ?>
					<li class="fc-process__step">
						<span class="fc-process__num" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<h3 class="fc-h3"><?php echo esc_html( $step['title'] ); ?></h3>
						<?php if ( ! empty( $step['text'] ) ) : ?>
							<p><?php echo esc_html( $step['text'] ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
	</div>
</section>
