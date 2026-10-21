<?php
/**
 * Hlavička sekce: nadtitulek, H2, perex.
 * Pole čte z právě vykreslovaného bloku, proto se nepředávají argumenty.
 */

defined( 'ABSPATH' ) || exit;

$fc_eyebrow = get_field( 'eyebrow' );
$fc_title   = get_field( 'title' );
$fc_lead    = get_field( 'lead' );

if ( ! $fc_eyebrow && ! $fc_title && ! $fc_lead ) {
	return;
}
?>
<header class="fc-section-head">
	<?php if ( $fc_eyebrow ) : ?>
		<p class="fc-eyebrow"><?php echo esc_html( $fc_eyebrow ); ?></p>
	<?php endif; ?>
	<?php if ( $fc_title ) : ?>
		<h2 class="fc-h2"><?php echo esc_html( $fc_title ); ?></h2>
	<?php endif; ?>
	<?php if ( $fc_lead ) : ?>
		<p class="fc-lead"><?php echo esc_html( $fc_lead ); ?></p>
	<?php endif; ?>
</header>
