<?php
/**
 * Blok: Text + obrázek (nebo karta bodů).
 *
 * @var array $block
 */

defined( 'ABSPATH' ) || exit;

$image        = get_field( 'image' );
$side         = get_field( 'image_side' ) ?: 'right';
$points       = fc_lines( get_field( 'points' ) );
$points_title = get_field( 'points_title' );
$btn          = fc_button( get_field( 'btn' ), 'primary', true );
?>
<section <?php echo fc_block_attrs( $block, 'fc-section fc-textmedia fc-textmedia--' . $side . ' ' . fc_bg_class() ); ?>>
	<div class="fc-container fc-textmedia__grid">
		<div class="fc-textmedia__body">
			<?php get_template_part( 'template-parts/section-head' ); ?>
			<div class="fc-prose"><?php echo wp_kses_post( (string) get_field( 'text' ) ); ?></div>
			<?php
			$fc_result  = get_field( 'benefit' );
			$fc_surface = array_filter(
				[
					'Povrch'             => get_field( 'material' ),
					'Typické znečištění' => get_field( 'problem' ),
					'Jak myjeme'         => get_field( 'method' ),
				]
			);
			?>
			<?php if ( $fc_result && ( $image || ! $points ) ) : // bez karty vpravo zůstává výsledek v textu ?>
				<p class="fc-result"><span class="fc-result__label">Výsledek</span><?php echo esc_html( $fc_result ); ?></p>
			<?php endif; ?>
			<?php if ( $fc_surface ) : ?>
				<details class="fc-surface">
					<summary><?php echo esc_html( get_field( 'surface_title' ) ?: 'Jak na to jdeme u tohoto povrchu' ); ?></summary>
					<dl>
						<?php foreach ( $fc_surface as $fc_label => $fc_value ) : ?>
							<?php $fc_steps = fc_lines( $fc_value ); // víc řádků = očíslovaný postup ?>
							<div class="fc-surface__row"><dt><?php echo esc_html( $fc_label ); ?></dt><dd>
								<?php if ( count( $fc_steps ) > 1 ) : ?>
									<ol class="fc-surface__steps">
										<?php foreach ( $fc_steps as $fc_step ) : ?>
											<li><?php echo esc_html( $fc_step ); ?></li>
										<?php endforeach; ?>
									</ol>
								<?php else : ?>
									<?php echo esc_html( $fc_value ); ?>
								<?php endif; ?>
							</dd></div>
						<?php endforeach; ?>
					</dl>
				</details>
			<?php endif; ?>
			<?php if ( $image && $points ) : ?>
				<?php get_template_part( 'template-parts/checklist', null, [ 'points' => $points ] ); ?>
			<?php endif; ?>
			<?php if ( $btn ) : ?>
				<div class="fc-actions"><?php echo $btn; // phpcs:ignore ?></div>
			<?php endif; ?>
		</div>

		<div class="fc-textmedia__aside">
			<?php if ( $image ) : ?>
				<figure class="fc-textmedia__media">
					<?php echo wp_get_attachment_image( $image, 'large', false, [ 'sizes' => '(min-width: 960px) 560px, 100vw' ] ); ?>
				</figure>
			<?php elseif ( $points ) : ?>
				<div class="fc-panel">
					<?php if ( $fc_result ) : ?>
						<div class="fc-panel__result">
							<p class="fc-panel__title">Výsledek</p>
							<p class="fc-panel__result-text"><?php echo esc_html( $fc_result ); ?></p>
						</div>
					<?php endif; ?>
					<?php if ( $points_title ) : ?>
						<p class="fc-panel__title"><?php echo esc_html( $points_title ); ?></p>
					<?php endif; ?>
					<?php get_template_part( 'template-parts/checklist', null, [ 'points' => $points ] ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
