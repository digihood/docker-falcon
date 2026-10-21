<?php
/**
 * Karta s ikonou: ikona vedle textu.
 *
 * @var array $args {
 *     @type array $item Položka repeateru (icon, title, text).
 * }
 */

defined( 'ABSPATH' ) || exit;

$fc_item = $args['item'] ?? [];
?>
<div class="fc-feature">
	<?php if ( ! empty( $fc_item['icon'] ) ) : ?>
		<span class="fc-card__icon"><?php fc_icon( $fc_item['icon'] ); ?></span>
	<?php endif; ?>
	<div>
		<h3 class="fc-h3"><?php echo esc_html( $fc_item['title'] ); ?></h3>
		<?php if ( ! empty( $fc_item['text'] ) ) : ?>
			<p><?php echo esc_html( $fc_item['text'] ); ?></p>
		<?php endif; ?>
	</div>
</div>
