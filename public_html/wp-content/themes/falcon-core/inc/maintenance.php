<?php
/**
 * Neveřejný režim: dokud není v Nastavení firmy zapnuto „Web je veřejný“,
 * nepřihlášení vidí stránku „Připravujeme“ (503 + noindex).
 */

defined( 'ABSPATH' ) || exit;

function fc_is_public() {
	return (bool) fc_opt( 'site_public', false );
}

add_action(
	'template_redirect',
	function () {
		if ( fc_is_public() || is_user_logged_in() ) {
			return;
		}
		status_header( 503 );
		header( 'Retry-After: 86400' );
		nocache_headers();
		get_template_part( 'template-parts/coming-soon' );
		exit;
	},
	0
);

add_filter(
	'wp_robots',
	function ( $robots ) {
		if ( ! fc_is_public() ) {
			$robots['noindex']  = true;
			$robots['nofollow'] = true;
		}
		return $robots;
	}
);

// Připomínka v horní liště, že web je skrytý.
add_action(
	'admin_bar_menu',
	function ( $bar ) {
		if ( fc_is_public() || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$bar->add_node(
			[
				'id'    => 'fc-hidden',
				'title' => '● Web je skrytý',
				'href'  => admin_url( 'admin.php?page=fc-firma' ),
				'meta'  => [ 'title' => 'Zveřejnit v Nastavení firmy' ],
			]
		);
	},
	100
);

add_action(
	'wp_head',
	function () {
		if ( ! fc_is_public() && is_admin_bar_showing() ) {
			echo '<style>#wp-admin-bar-fc-hidden > .ab-item{background:#b32d2e!important;color:#fff!important}</style>';
		}
	}
);
