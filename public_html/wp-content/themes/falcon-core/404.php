<?php
/**
 * 404.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="fc-pagehead fc-404">
	<div class="fc-container">
		<p class="fc-eyebrow">Chyba 404</p>
		<h1 class="fc-h1">Stránka neexistuje</h1>
		<p class="fc-lead">Odkaz je neplatný nebo stránka byla přesunuta.</p>
		<div class="fc-actions">
			<a class="fc-btn fc-btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><span>Na úvodní stránku</span></a>
			<a class="fc-btn fc-btn--outline" href="<?php echo esc_url( fc_contact_url() ); ?>"><span>Kontakt</span></a>
		</div>
	</div>
</section>
<?php
get_footer();
