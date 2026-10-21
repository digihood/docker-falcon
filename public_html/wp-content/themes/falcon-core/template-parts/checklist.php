<?php
/**
 * Seznam s fajfkami.
 *
 * @var array $args {
 *     @type string[] $points Body seznamu.
 * }
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $args['points'] ) ) {
	return;
}
?>
<ul class="fc-checklist">
	<?php foreach ( $args['points'] as $fc_point ) : ?>
		<li><?php fc_icon( 'check' ); ?><?php echo fc_point_html( $fc_point ); // phpcs:ignore WordPress.Security.EscapeOutput ?></li>
	<?php endforeach; ?>
</ul>
