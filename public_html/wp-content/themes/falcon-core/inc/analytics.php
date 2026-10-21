<?php
/**
 * Měření konverzí přes Google Tag Manager.
 *
 * Do dataLayeru posíláme tři události, které jsou pro web konverzí:
 * generate_lead (odeslaná poptávka), phone_click a email_click.
 * Vyhodnocení (GA4, Ads, …) se nastavuje v GTM, ne v šabloně.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Dočasné ID kontejneru, než klient dodá vlastní.
 * Vlastní se vyplní v Nastavení firmy → Měření.
 */
const FC_GTM_PLACEHOLDER = 'GTM-XXXXXXX';

function fc_gtm_id() {
	return trim( (string) fc_opt( 'gtm_id', FC_GTM_PLACEHOLDER ) );
}

// Kontejner GTM co nejvýš v <head>, ať stihne načíst značky.
add_action(
	'wp_head',
	function () {
		$id = fc_gtm_id();
		if ( ! $id ) {
			return;
		}
		?>
<script>window.dataLayer=window.dataLayer||[];</script>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?php echo esc_js( $id ); ?>');</script>
<!-- End Google Tag Manager -->
		<?php
	},
	2
);

add_action(
	'wp_body_open',
	function () {
		$id = fc_gtm_id();
		if ( ! $id ) {
			return;
		}
		printf(
			'<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=%s" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>' . "\n",
			rawurlencode( $id )
		);
	}
);

/**
 * Konverze: odeslaná poptávka. Stav pozná šablona z adresy, na kterou
 * formulář po úspěšném odeslání přesměruje (viz inc/lead-form.php).
 */
add_action(
	'wp_footer',
	function () {
		$status = isset( $_GET['poptavka'] ) ? sanitize_key( wp_unslash( $_GET['poptavka'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		if ( 'odeslano' !== $status ) {
			return;
		}
		printf(
			'<script>window.dataLayer=window.dataLayer||[];window.dataLayer.push({event:"generate_lead",form_name:"poptavka",page_path:%s});</script>' . "\n",
			wp_json_encode( wp_make_link_relative( get_permalink() ?: home_url( '/' ) ) )
		);
	}
);
