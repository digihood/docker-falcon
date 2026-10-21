<?php
/**
 * Blok: Výzva k akci.
 *
 * @var array $block
 */

defined( 'ABSPATH' ) || exit;

$style = get_field( 'style' ) ?: 'dark';
$text  = get_field( 'text' );
$btn   = fc_button( get_field( 'btn' ), 'dark' === $style ? 'primary' : 'white', true );
$phone = get_field( 'show_phone' ) ? fc_opt( 'phone' ) : '';
?>
<section <?php echo fc_block_attrs( $block, 'fc-cta fc-cta--' . $style ); ?>>
	<div class="fc-container fc-cta__inner">
		<div class="fc-cta__body">
			<h2 class="fc-h2"><?php echo esc_html( get_field( 'title' ) ); ?></h2>
			<?php if ( $text ) : ?>
				<p><?php echo esc_html( $text ); ?></p>
			<?php endif; ?>
		</div>
		<div class="fc-cta__actions">
			<?php echo $btn; // phpcs:ignore ?>
			<?php if ( $phone ) : ?>
				<a class="fc-cta__phone" href="<?php echo esc_attr( fc_phone_href( $phone ) ); ?>">
					<?php fc_icon( 'phone' ); ?><span><small>nebo volejte</small><?php echo esc_html( $phone ); ?></span>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>
