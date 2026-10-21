<?php
/**
 * SEO: popisek, kanonická URL, Open Graph, Twitter a strukturovaná data.
 *
 * Šablona si SEO řeší sama, aby web fungoval i bez SEO pluginu. Když je zapnutý
 * Rank Math, vlastní meta tagy se vypnou a místo nich se Rank Mathu dodá to,
 * co si sám odvodit neumí – popisek z ACF bloků a firemní údaje do schématu.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Který SEO plugin je zapnutý ('' = žádný).
 */
function fc_seo_plugin() {
	if ( defined( 'RANK_MATH_VERSION' ) ) {
		return 'rankmath';
	}
	if ( defined( 'WPSEO_VERSION' ) ) {
		return 'yoast';
	}
	if ( defined( 'AIOSEO_VERSION' ) ) {
		return 'aioseo';
	}
	if ( defined( 'SEOPRESS_VERSION' ) ) {
		return 'seopress';
	}
	return '';
}

/**
 * Vypíná se, když SEO řeší plugin – jinak by byly meta tagy dvakrát.
 */
function fc_seo_active() {
	return '' === fc_seo_plugin();
}

/**
 * Popisek stránky: ruční zkrácený popis, jinak text z Hero bloku, jinak popis webu.
 */
function fc_meta_description( $post = null ) {
	$post = get_post( $post );

	if ( $post && ! is_front_page() ) {
		$excerpt = has_excerpt( $post ) ? get_the_excerpt( $post ) : '';
		if ( $excerpt ) {
			return wp_strip_all_tags( $excerpt );
		}
	}

	if ( $post ) {
		$blocks = parse_blocks( $post->post_content );

		// Text z prvního ACF bloku, který ho má (hero, perex sekce).
		foreach ( $blocks as $block ) {
			if ( empty( $block['blockName'] ) ) {
				continue;
			}
			$data = $block['attrs']['data'] ?? [];
			foreach ( [ 'text', 'lead' ] as $key ) {
				if ( ! empty( $data[ $key ] ) && is_string( $data[ $key ] ) ) {
					$text = wp_strip_all_tags( $data[ $key ] );
					if ( mb_strlen( $text ) > 50 ) {
						return $text;
					}
				}
			}
		}

		// Stránky psané běžnými bloky (např. GDPR): první skutečný odstavec.
		foreach ( $blocks as $block ) {
			if ( 'core/paragraph' !== ( $block['blockName'] ?? '' ) ) {
				continue;
			}
			// Shortcody s firemními údaji musí být vyhodnocené, ne v popisku syrové.
			$text = trim( wp_strip_all_tags( strip_shortcodes( do_shortcode( $block['innerHTML'] ) ) ) );
			// Redakční poznámky typu „[DOPLNIT]“ do popisku nepatří.
			if ( mb_strlen( $text ) > 50 && ! str_starts_with( $text, '[' ) ) {
				return $text;
			}
		}
	}

	return get_bloginfo( 'description' );
}

/**
 * Zkrácení na délku, která se vejde do výsledků vyhledávání.
 */
function fc_shorten( $text, $limit = 158 ) {
	$text = trim( preg_replace( '/\s+/u', ' ', (string) $text ) );
	if ( mb_strlen( $text ) <= $limit ) {
		return $text;
	}
	$cut = mb_substr( $text, 0, $limit );
	$pos = mb_strrpos( $cut, ' ' );
	return rtrim( $pos ? mb_substr( $cut, 0, $pos ) : $cut, ' ,.;:' ) . '…';
}

/**
 * Obrázek pro sdílení: logo, jinak ikona webu.
 */
function fc_share_image() {
	$logo_id = (int) get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		return wp_get_attachment_image_url( $logo_id, 'full' );
	}
	$icon = get_site_icon_url( 512 );
	return $icon ?: FC_URI . '/assets/img/falcon-core-logo.png';
}

add_action(
	'wp_head',
	function () {
		if ( ! fc_seo_active() ) {
			return;
		}

		$title = wp_get_document_title();
		$desc  = fc_shorten( fc_meta_description() );
		$url   = is_singular() ? get_permalink() : home_url( add_query_arg( [] ) );
		$image = fc_share_image();

		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );

		printf( '<meta property="og:type" content="%s">' . "\n", is_front_page() ? 'website' : 'article' );
		printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
		printf( '<meta property="og:locale" content="%s">' . "\n", esc_attr( get_locale() ) );
		printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desc ) );
		printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
		if ( $image ) {
			printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
			printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $image ) );
		}

		printf( '<meta name="twitter:card" content="%s">' . "\n", 'summary_large_image' );
		printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
		printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $desc ) );
	},
	2
);

/**
 * Firemní údaje pro schema.org – jediný zdroj je „Nastavení firmy“.
 * Používá se jak ve vlastním výstupu, tak při doplnění schématu Rank Mathu.
 */
function fc_organization_data() {
	$org = [
		'@type' => 'Organization',
		'name'  => 'Falcon Core',
		'url'   => home_url( '/' ),
	];
	if ( fc_has_real( 'company_name' ) ) {
		$org['legalName'] = fc_opt( 'company_name' );
	}
	if ( fc_has_real( 'ico' ) ) {
		$org['taxID']      = fc_opt( 'ico' );
		$org['identifier'] = fc_opt( 'ico' );
	}
	if ( fc_has_real( 'dic' ) ) {
		$org['vatID'] = fc_opt( 'dic' );
	}
	if ( fc_has_real( 'phone' ) ) {
		$org['telephone'] = fc_opt( 'phone' );
	}
	if ( fc_has_real( 'email' ) ) {
		$org['email'] = fc_opt( 'email' );
	}
	if ( fc_has_real( 'address' ) ) {
		$lines          = fc_lines( fc_opt( 'address' ) );
		$org['address'] = [
			'@type'           => 'PostalAddress',
			'streetAddress'   => $lines[0] ?? '',
			'addressLocality' => isset( $lines[1] ) ? trim( preg_replace( '/^\d{3}\s?\d{2}\s*/u', '', $lines[1] ) ) : '',
			'postalCode'      => isset( $lines[1] ) && preg_match( '/\d{3}\s?\d{2}/u', $lines[1], $m ) ? $m[0] : '',
			'addressCountry'  => 'CZ',
		];
	}
	$image = fc_share_image();
	if ( $image ) {
		$org['logo'] = $image;
	}
	return $org;
}

/**
 * Strukturovaná data: organizace a web na úvodní stránce,
 * drobečky a časté dotazy na podstránkách.
 */
add_action(
	'wp_head',
	function () {
		if ( ! fc_seo_active() || ! fc_is_public() ) {
			return;
		}

		$graph = [];

		if ( is_front_page() ) {
			$graph[] = [ '@id' => home_url( '/#organization' ) ] + fc_organization_data();

			$graph[] = [
				'@type'     => 'WebSite',
				'@id'       => home_url( '/#website' ),
				'url'       => home_url( '/' ),
				'name'      => get_bloginfo( 'name' ),
				'publisher' => [ '@id' => home_url( '/#organization' ) ],
				'inLanguage' => get_locale(),
			];
		}

		// Drobečky (úvod → stránka) kvůli zobrazení cesty ve výsledcích.
		if ( is_singular() && ! is_front_page() ) {
			$graph[] = [
				'@type'           => 'BreadcrumbList',
				'itemListElement' => [
					[
						'@type'    => 'ListItem',
						'position' => 1,
						'name'     => 'Úvod',
						'item'     => home_url( '/' ),
					],
					[
						'@type'    => 'ListItem',
						'position' => 2,
						'name'     => get_the_title(),
						'item'     => get_permalink(),
					],
				],
			];
		}

		// Časté dotazy z bloků falcon/faq na aktuální stránce.
		$faq = fc_collect_faq();
		if ( $faq ) {
			$graph[] = [
				'@type'      => 'FAQPage',
				'mainEntity' => $faq,
			];
		}

		if ( ! $graph ) {
			return;
		}

		echo '<script type="application/ld+json">'
			. wp_json_encode(
				[
					'@context' => 'https://schema.org',
					'@graph'   => $graph,
				],
				JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
			)
			. '</script>' . "\n";
	},
	3
);

/**
 * Otázky a odpovědi z bloků falcon/faq na aktuální stránce.
 */
function fc_collect_faq() {
	if ( ! is_singular() ) {
		return [];
	}
	$post = get_post();
	if ( ! $post ) {
		return [];
	}

	$out = [];
	foreach ( parse_blocks( $post->post_content ) as $block ) {
		if ( 'falcon/faq' !== ( $block['blockName'] ?? '' ) ) {
			continue;
		}
		$data  = $block['attrs']['data'] ?? [];
		$count = (int) ( $data['items'] ?? 0 );
		for ( $i = 0; $i < $count; $i++ ) {
			$q = $data[ 'items_' . $i . '_q' ] ?? '';
			$a = $data[ 'items_' . $i . '_a' ] ?? '';
			if ( ! $q || ! $a ) {
				continue;
			}
			$out[] = [
				'@type'          => 'Question',
				'name'           => wp_strip_all_tags( $q ),
				'acceptedAnswer' => [
					'@type' => 'Answer',
					'text'  => wp_strip_all_tags( $a ),
				],
			];
		}
	}
	return $out;
}

/**
 * Poptávky do mapy webu nepatří (neveřejný typ obsahu).
 */
add_filter(
	'wp_sitemaps_post_types',
	function ( $types ) {
		unset( $types['fc_lead'] );
		return $types;
	}
);

/* ==========================================================================
   Most do Rank Mathu
   --------------------------------------------------------------------------
   Rank Math neumí přečíst obsah ACF bloků (texty jsou v komentářích bloků,
   ne v post_content jako text), takže by stránky zůstaly bez popisku.
   Dodáme mu proto popisek i firemní údaje ze stejného zdroje jako zbytek webu.
   ========================================================================== */

if ( 'rankmath' === fc_seo_plugin() ) {

	// Popisek stránky, když si ho redaktor nevyplnil ručně v Rank Mathu.
	add_filter(
		'rank_math/frontend/description',
		function ( $description ) {
			$description = trim( (string) $description );
			return '' !== $description ? $description : fc_shorten( fc_meta_description() );
		},
		20
	);

	// Doplnění schématu: firemní údaje, časté dotazy, úklid nesmyslných entit.
	add_filter(
		'rank_math/json_ld',
		function ( $data ) {
			if ( ! is_array( $data ) ) {
				return $data;
			}

			$company = fc_organization_data();

			foreach ( $data as $key => $entity ) {
				if ( ! is_array( $entity ) || empty( $entity['@type'] ) ) {
					continue;
				}
				$types = (array) $entity['@type'];

				// Organizaci doplníme o IČO, telefon, e-mail a sídlo.
				if ( in_array( 'Organization', $types, true ) ) {
					$data[ $key ] = array_merge( $entity, $company, [ '@type' => 'Organization' ] );
					continue;
				}

				// Firemní stránka není článek a nemá autora s gravatarem.
				if ( is_page() && ( in_array( 'Article', $types, true ) || in_array( 'BlogPosting', $types, true ) || in_array( 'Person', $types, true ) ) ) {
					unset( $data[ $key ] );
				}
			}

			$faq = fc_collect_faq();
			if ( $faq ) {
				$data['fcFaq'] = [
					'@type'      => 'FAQPage',
					'@id'        => get_permalink() . '#faq',
					'mainEntity' => $faq,
				];
			}

			return $data;
		},
		20
	);
}
