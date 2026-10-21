<?php
/**
 * Blok: Srovnání (tabulka).
 *
 * @var array $block
 */

defined( 'ABSPATH' ) || exit;

$rows  = get_field( 'rows' ) ?: [];
$col_a = get_field( 'col_a' ) ?: 'Falcon Core';
$col_b = get_field( 'col_b' ) ?: 'Alternativa';
$col_c = get_field( 'col_c' );
$note  = get_field( 'note' );
$more  = get_field( 'more' );
?>
<section <?php echo fc_block_attrs( $block, 'fc-section fc-compare' . ( $col_c ? ' fc-compare--3' : '' ) . ' ' . fc_bg_class() ); ?>>
	<div class="fc-container">
		<?php get_template_part( 'template-parts/section-head' ); ?>

		<?php if ( $rows ) : ?>
			<div class="fc-compare__table" role="table" aria-label="<?php echo esc_attr( $col_a . ' vs. ' . $col_b . ( $col_c ? ' vs. ' . $col_c : '' ) ); ?>">
				<div class="fc-compare__row fc-compare__row--head" role="row">
					<span role="columnheader" class="fc-compare__param"><span class="screen-reader-text">Parametr</span></span>
					<span role="columnheader" class="fc-compare__a"><?php echo esc_html( $col_a ); ?></span>
					<span role="columnheader" class="fc-compare__b"><?php echo esc_html( $col_b ); ?></span>
					<?php if ( $col_c ) : ?>
						<span role="columnheader" class="fc-compare__b"><?php echo esc_html( $col_c ); ?></span>
					<?php endif; ?>
				</div>
				<?php foreach ( $rows as $row ) : ?>
					<div class="fc-compare__row" role="row">
						<span role="rowheader" class="fc-compare__param"><?php echo esc_html( $row['param'] ); ?></span>
						<span role="cell" class="fc-compare__a" data-label="<?php echo esc_attr( $col_a ); ?>"><?php fc_icon( 'check' ); ?><span><?php echo esc_html( $row['a'] ); ?></span></span>
						<span role="cell" class="fc-compare__b" data-label="<?php echo esc_attr( $col_b ); ?>"><span><?php echo esc_html( $row['b'] ); ?></span></span>
						<?php if ( $col_c ) : ?>
							<span role="cell" class="fc-compare__b" data-label="<?php echo esc_attr( $col_c ); ?>"><span><?php echo esc_html( $row['c'] ?? '' ); ?></span></span>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( $note ) : ?>
			<p class="fc-note"><?php echo esc_html( $note ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $more['url'] ) ) : ?>
			<p class="fc-compare__more"><a class="fc-link" href="<?php echo esc_url( $more['url'] ); ?>"><?php echo esc_html( $more['title'] ?: $more['url'] ); ?><?php fc_icon( 'arrow' ); ?></a></p>
		<?php endif; ?>
	</div>
</section>
