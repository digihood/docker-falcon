<?php
/**
 * Hlavička.
 */

defined( 'ABSPATH' ) || exit;

$fc_phone = fc_company( 'phone' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="fc-skip" href="#obsah">Přeskočit na obsah</a>

<header class="fc-header">
	<div class="fc-container fc-header__inner">
		<a class="fc-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Falcon Core - úvodní stránka">
			<?php fc_logo( 'light' ); ?>
		</a>

		<nav id="fc-nav" class="fc-nav" aria-label="Hlavní menu">
			<?php
			wp_nav_menu(
				[
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'fc-nav__list',
					'depth'          => 2,
					'fallback_cb'    => 'fc_nav_fallback',
				]
			);
			?>
			<div class="fc-nav__mobile">
				<?php if ( $fc_phone ) : ?>
					<a class="fc-btn fc-btn--outline" href="<?php echo esc_attr( fc_phone_href( $fc_phone ) ); ?>"><?php fc_icon( 'phone' ); ?><span><?php echo esc_html( $fc_phone ); ?></span></a>
				<?php endif; ?>
				<a class="fc-btn fc-btn--primary" href="<?php echo esc_url( fc_contact_url() ); ?>"><span>Nezávazná poptávka</span></a>
				<p class="fc-nav__note">Termíny na sezónu 2027 už rezervujeme.</p>
			</div>
		</nav>

		<div class="fc-header__actions">
			<?php if ( $fc_phone ) : ?>
				<a class="fc-header__phone" href="<?php echo esc_attr( fc_phone_href( $fc_phone ) ); ?>" aria-label="Zavolat <?php echo esc_attr( $fc_phone ); ?>">
					<?php fc_icon( 'phone' ); ?><span><?php echo esc_html( $fc_phone ); ?></span>
				</a>
			<?php endif; ?>
			<a class="fc-btn fc-btn--primary fc-btn--sm fc-header__cta" href="<?php echo esc_url( fc_contact_url() ); ?>"><span>Poptávka</span></a>
			<button class="fc-nav-toggle" type="button" aria-controls="fc-nav" aria-expanded="false">
				<span class="screen-reader-text">Menu</span>
				<span class="fc-nav-toggle__open"><?php fc_icon( 'menu' ); ?></span>
				<span class="fc-nav-toggle__close"><?php fc_icon( 'close' ); ?></span>
			</button>
		</div>
	</div>
</header>

<main id="obsah" class="fc-main">
