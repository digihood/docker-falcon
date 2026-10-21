<?php
/**
 * Blok: Hero.
 *
 * @var array $block
 */

defined( 'ABSPATH' ) || exit;

$variant = get_field( 'variant' ) ?: 'page';
$eyebrow = get_field( 'eyebrow' );
$title   = get_field( 'title' );
$text    = get_field( 'text' );
$image   = get_field( 'image' );
$trust   = get_field( 'trust' ) ?: [];
$tags    = get_field( 'tags' ) ?: [];
$buttons = fc_button( get_field( 'btn_primary' ), 'primary', true ) . fc_button( get_field( 'btn_secondary' ), 'ghost' );
?>
<section <?php echo fc_block_attrs( $block, 'fc-hero fc-hero--' . $variant . ( $image ? ' has-image' : '' ) ); ?>>
	<?php
	if ( $image ) {
		echo wp_get_attachment_image(
			$image,
			'full',
			false,
			[
				'class'         => 'fc-hero__bg',
				'loading'       => 'eager',
				'fetchpriority' => 'high',
				'sizes'         => '100vw',
				'alt'           => '',
			]
		);
	}
	?>
	<div class="fc-hero__overlay" aria-hidden="true"></div>
	<div class="fc-container fc-hero__inner">
		<?php if ( $eyebrow ) : ?>
			<p class="fc-eyebrow fc-eyebrow--light"><?php echo esc_html( $eyebrow ); ?></p>
		<?php endif; ?>

		<h1 class="fc-hero__title"><?php echo $title ? wp_kses( $title, [ 'br' => [] ] ) : esc_html( get_the_title() ); ?></h1>

		<?php if ( $text ) : ?>
			<p class="fc-hero__text"><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>

		<?php if ( $tags ) : ?>
			<ul class="fc-hero__tags" aria-label="Typy objektů">
				<?php foreach ( $tags as $tag ) : ?>
					<?php if ( ! empty( $tag['text'] ) ) : ?>
						<li><?php echo esc_html( $tag['text'] ); ?></li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if ( $buttons ) : ?>
			<div class="fc-hero__actions"><?php echo $buttons; // phpcs:ignore ?></div>
		<?php endif; ?>

		<?php if ( $trust ) : ?>
			<ul class="fc-hero__trust">
				<?php foreach ( $trust as $item ) : ?>
					<?php if ( ! empty( $item['text'] ) ) : ?>
						<li><?php fc_icon( 'check' ); ?><?php echo esc_html( $item['text'] ); ?></li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
