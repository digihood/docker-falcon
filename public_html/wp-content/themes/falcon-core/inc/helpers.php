<?php
/**
 * Pomocné funkce pro šablony a bloky.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Hodnota z „Nastavení firmy“ (ACF options page).
 */
function fc_opt( $name, $default = '' ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}
	$value = get_field( $name, 'option' );
	return ( null === $value || '' === $value || false === $value ) ? $default : $value;
}

/**
 * Firemní údaj s viditelným zástupným textem, pokud chybí.
 */
function fc_company( $name ) {
	$labels = [
		'company_name' => 'Falcon Core',
		'address'      => '[doplnit sídlo]',
		'ico'          => '[doplnit IČO]',
		'dic'          => '[doplnit DIČ]',
		'registry'     => '[doplnit spisovou značku]',
		'phone'        => '[doplnit telefon]',
		'email'        => '[doplnit e-mail]',
	];
	return fc_opt( $name, $labels[ $name ] ?? '' );
}

/**
 * Bod seznamu: e-mailová adresa v textu se zobrazí jako proklikávací odkaz na novém řádku.
 */
function fc_point_html( $point ) {
	if ( ! preg_match( '/[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}/', $point, $m ) ) {
		return esc_html( $point );
	}
	$text = trim( str_replace( $m[0], '', $point ), " ,;()" );
	return '<span>' . esc_html( $text ) . '<a class="fc-checklist__mail" href="mailto:' . esc_attr( $m[0] ) . '">' . esc_html( $m[0] ) . '</a></span>';
}

function fc_phone_href( $phone ) {
	return 'tel:' . preg_replace( '/[^0-9+]/', '', (string) $phone );
}

function fc_has_real( $name ) {
	return '' !== fc_opt( $name, '' );
}

/**
 * URL stránky podle slugu (s fallbackem).
 */
function fc_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
}

function fc_contact_url() {
	return fc_page_url( 'kontakt' );
}

/**
 * Atributy obalu bloku (class + anchor).
 */
function fc_block_attrs( $block, $class = '' ) {
	$classes = trim( 'fc-block ' . $class . ' ' . ( $block['className'] ?? '' ) );
	$out     = 'class="' . esc_attr( $classes ) . '"';
	if ( ! empty( $block['anchor'] ) ) {
		$out .= ' id="' . esc_attr( $block['anchor'] ) . '"';
	}
	return $out;
}

function fc_bg_class() {
	$bg = function_exists( 'get_field' ) ? get_field( 'bg' ) : '';
	return 'fc-bg-' . ( $bg ? $bg : 'white' );
}

/**
 * Tlačítko z ACF link pole.
 */
function fc_button( $link, $variant = 'primary', $icon = false ) {
	if ( empty( $link['url'] ) ) {
		return '';
	}
	$target = ! empty( $link['target'] ) ? ' target="' . esc_attr( $link['target'] ) . '" rel="noopener"' : '';
	$label  = $link['title'] ? $link['title'] : $link['url'];
	return sprintf(
		'<a class="fc-btn fc-btn--%s" href="%s"%s><span>%s</span>%s</a>',
		esc_attr( $variant ),
		esc_url( $link['url'] ),
		$target,
		esc_html( $label ),
		$icon ? fc_icon( 'arrow', false ) : ''
	);
}

/**
 * Text po řádcích → pole.
 */
function fc_lines( $text ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $text ) ) ) );
}

/**
 * Sada ikon (vlastní jednoduché 24×24 stroke ikony).
 */
function fc_icons() {
	return [
		'hall'     => [ 'Hala', '<path d="M3 21V10l9-5 9 5v11"/><path d="M2 21h20M7 21v-6h10v6M7 18h10"/>' ],
		'facade'   => [ 'Fasáda / budova', '<rect x="4" y="3" width="16" height="18" rx="1"/><path d="M8 7h2M14 7h2M8 11h2M14 11h2M8 15h2M14 15h2M10 21v-3h4v3"/>' ],
		'roof'     => [ 'Střecha', '<path d="M2 11l10-6 10 6"/><path d="M5 10v10h14V10M3 20h18"/><path d="M8 14h8M8 17h8"/>' ],
		'solar'    => [ 'Fotovoltaika', '<circle cx="17.5" cy="4.5" r="2"/><path d="M3 20 5.5 9h11L19 20z"/><path d="M4.2 14.5h13.6M9.2 9l-1 11M12.8 9l1 11"/>' ],
		'drone'    => [ 'Dron', '<circle cx="6" cy="6" r="2.5"/><circle cx="18" cy="6" r="2.5"/><circle cx="6" cy="18" r="2.5"/><circle cx="18" cy="18" r="2.5"/><rect x="9.5" y="9.5" width="5" height="5" rx="1"/><path d="M8 8l1.5 1.5M16 8l-1.5 1.5M8 16l1.5-1.5M16 16l-1.5-1.5"/>' ],
		'shield'   => [ 'Štít / pojištění', '<path d="M12 3l8 3v6c0 4.5-3.4 8.2-8 9-4.6-.8-8-4.5-8-9V6z"/><path d="M8.5 12l2.5 2.5 4.5-5"/>' ],
		'badge'    => [ 'Certifikát', '<circle cx="12" cy="9" r="5.5"/><path d="M9.2 14 8 21l4-2 4 2-1.2-7"/><path d="M10 9l1.5 1.5L14.5 7.5"/>' ],
		'height'   => [ 'Výška', '<path d="M12 3v18M8 7l4-4 4 4M8 17l4 4 4-4"/><path d="M3 21h4M17 3h4"/>' ],
		'target'   => [ 'Přesnost (RTK)', '<circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/>' ],
		'drop'     => [ 'Kapka / mytí', '<path d="M12 3s6 6.5 6 11a6 6 0 0 1-12 0c0-4.5 6-11 6-11z"/><path d="M9.5 14.5a2.5 2.5 0 0 0 2.5 2.5"/>' ],
		'flame'    => [ 'Ohřev', '<path d="M12 3c1 3 5 5 5 10a5 5 0 0 1-10 0c0-2.5 1.5-4 2.5-5 .3 2 1.5 3 2.5 3 0-3-1-5 0-8z"/>' ],
		'clock'    => [ 'Čas', '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>' ],
		'coins'    => [ 'Cena', '<ellipse cx="12" cy="6" rx="7" ry="3"/><path d="M5 6v6c0 1.7 3.1 3 7 3s7-1.3 7-3V6M5 12v6c0 1.7 3.1 3 7 3s7-1.3 7-3v-6"/>' ],
		'truck'    => [ 'Rampa / doprava', '<path d="M2 6h11v10H2zM13 10h4l3 3v3h-7z"/><circle cx="6" cy="18" r="1.8"/><circle cx="17" cy="18" r="1.8"/>' ],
		'person'   => [ 'Osoba', '<circle cx="12" cy="8" r="3.5"/><path d="M5 20c.8-3.5 3.6-5.5 7-5.5s6.2 2 7 5.5"/>' ],
		'camera'   => [ 'Fotodokumentace', '<path d="M4 8h3l2-3h6l2 3h3v11H4z"/><circle cx="12" cy="13" r="3.5"/>' ],
		'check'    => [ 'Fajfka', '<path d="M5 12.5l4.5 4.5L19 7.5"/>' ],
		'phone'    => [ 'Telefon', '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a1 1 0 0 1-1 1A16 16 0 0 1 4 5a1 1 0 0 1 1-1z"/>' ],
		'mail'     => [ 'E-mail', '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>' ],
		'pin'      => [ 'Adresa', '<path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>' ],
		'doc'      => [ 'Dokument', '<path d="M6 3h8l4 4v14H6z"/><path d="M14 3v4h4M9 12h6M9 16h6"/>' ],
		'arrow'    => [ 'Šipka', '<path d="M5 12h14M13 6l6 6-6 6"/>' ],
		'menu'     => [ 'Menu', '<path d="M4 7h16M4 12h16M4 17h16"/>' ],
		'close'    => [ 'Zavřít', '<path d="M6 6l12 12M18 6 6 18"/>' ],
	];
}

function fc_icon_choices() {
	$choices = [];
	foreach ( fc_icons() as $key => $icon ) {
		if ( ! in_array( $key, [ 'arrow', 'menu', 'close' ], true ) ) {
			$choices[ $key ] = $icon[0];
		}
	}
	return $choices;
}

function fc_icon( $name, $echo = true ) {
	$icons = fc_icons();
	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}
	$svg = '<svg class="fc-icon fc-icon--' . esc_attr( $name ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $icons[ $name ][1] . '</svg>';
	if ( $echo ) {
		echo $svg; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	return $svg;
}

/**
 * Logo. Na světlém podkladu logo z Přizpůsobit → Identita webu,
 * na tmavém (patička, „Připravujeme“) světlá varianta ze šablony.
 */
function fc_logo( $context = 'light' ) {
	if ( 'dark' === $context ) {
		printf(
			'<img class="fc-logo__img" src="%s" alt="Falcon Core" width="624" height="138" loading="lazy" decoding="async">',
			esc_url( FC_URI . '/assets/img/falcon-core-logo-light.png' )
		);
		return;
	}

	$logo_id = (int) get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		echo wp_get_attachment_image(
			$logo_id,
			'medium',
			false,
			[
				'class'         => 'fc-logo__img',
				'alt'           => 'Falcon Core',
				'loading'       => 'eager',
				'fetchpriority' => 'high',
			]
		);
		return;
	}

	printf(
		'<img class="fc-logo__img" src="%s" alt="Falcon Core" width="624" height="138" fetchpriority="high">',
		esc_url( FC_URI . '/assets/img/falcon-core-logo.png' )
	);
}

/**
 * Fallback menu, když ještě není vytvořené.
 */
function fc_nav_fallback() {
	echo '<ul class="fc-nav__list">';
	foreach ( [ '' => 'Úvod', 'sluzby' => 'Služby', 'proc-myt-dronem' => 'Proč mýt dronem', 'o-nas' => 'O nás', 'kontakt' => 'Kontakt' ] as $slug => $label ) {
		$url = $slug ? fc_page_url( $slug ) : home_url( '/' );
		echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
	}
	echo '</ul>';
}

/**
 * [fc_firma pole="nazev|sidlo|ico|dic|spis|email|telefon"] – pro texty stránek (např. GDPR).
 */
add_shortcode(
	'fc_firma',
	function ( $atts ) {
		$map = [
			'nazev'   => 'company_name',
			'sidlo'   => 'address',
			'ico'     => 'ico',
			'dic'     => 'dic',
			'spis'    => 'registry',
			'email'   => 'email',
			'telefon' => 'phone',
		];
		$atts = shortcode_atts( [ 'pole' => 'nazev' ], $atts );
		$key  = $map[ $atts['pole'] ] ?? 'company_name';
		return esc_html( str_replace( "\n", ', ', fc_company( $key ) ) );
	}
);
