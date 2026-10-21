<?php
/**
 * Karta služby: ikona, název, popis a odkaz.
 *
 * @var array $args {
 *     @type array $item Položka repeateru (icon, title, text, link).
 * }
 */

defined( 'ABSPATH' ) || exit;

$fc_item = $args['item'] ?? [];
$fc_link = $fc_item['link'] ?? null;
?>
<article class="fc-card fc-service">
	<?php if ( ! empty( $fc_item['icon'] ) ) : ?>
		<span class="fc-card__icon"><?php fc_icon( $fc_item['icon'] ); ?></span>
	<?php endif; ?>
	<h3 class="fc-h3"><?php echo esc_html( $fc_item['title'] ); ?></h3>
	<?php if ( ! empty( $fc_item['text'] ) ) : ?>
		<p><?php echo esc_html( $fc_item['text'] ); ?></p>
	<?php endif; ?>
	<?php if ( ! empty( $fc_link['url'] ) ) : ?>
		<a class="fc-link" href="<?php echo esc_url( $fc_link['url'] ); ?>"<?php echo ! empty( $fc_link['target'] ) ? ' target="_blank" rel="noopener"' : ''; ?>>
			<?php echo esc_html( $fc_link['title'] ?: 'Více' ); ?><?php fc_icon( 'arrow' ); ?>
		</a>
	<?php endif; ?>
</article>
