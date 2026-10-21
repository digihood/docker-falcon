<?php
/**
 * Pole bloků a nastavení firmy (definováno v kódu – verzované, nic se neklikne omylem).
 */

defined( 'ABSPATH' ) || exit;

add_action( 'acf/init', 'fc_register_fields' );

function fc_register_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$icons   = fc_icon_choices();
	$bg      = fc_field(
		'bg',
		'Pozadí sekce',
		'button_group',
		[
			'choices'       => [
				'white' => 'Bílé',
				'light' => 'Světle šedé',
				'dark'  => 'Tmavé',
			],
			'default_value' => 'white',
		]
	);
	$eyebrow = fc_field( 'eyebrow', 'Nadtitulek', 'text', [ 'instructions' => 'Krátký štítek nad nadpisem, např. „Služby“.' ] );
	$title   = fc_field( 'title', 'Nadpis', 'text' );
	$lead    = fc_field( 'lead', 'Úvodní text', 'textarea', [ 'rows' => 3, 'new_lines' => '' ] );

	/* ---------- Nastavení firmy ---------- */
	acf_add_local_field_group(
		[
			'key'      => 'group_fc_company',
			'title'    => 'Firemní údaje',
			'fields'   => fc_keyify(
				[
					fc_field( 'tab_web', 'Zveřejnění', 'tab' ),
					fc_field(
						'site_public',
						'Web je veřejný',
						'true_false',
						[
							'ui'           => 1,
							'instructions' => 'Dokud je vypnuto, nepřihlášení návštěvníci vidí jen stránku „Připravujeme“ (HTTP 503, noindex). Zapnout až po vyplnění údajů níže.',
						]
					),
					fc_field( 'tab_company', 'Firma', 'tab' ),
					fc_field( 'company_name', 'Obchodní firma (jméno podnikatele)', 'text', [ 'placeholder' => 'Matyáš Herman' ] ),
					fc_field( 'address', 'Sídlo', 'textarea', [ 'rows' => 2, 'new_lines' => '', 'placeholder' => "Ulice 123/4\n110 00 Praha 1" ] ),
					fc_field( 'ico', 'IČO', 'text' ),
					fc_field( 'dic', 'DIČ', 'text', [ 'instructions' => 'Pokud nejste plátce DPH, napište „Není plátce DPH“ nebo nechte DIČ bez poznámky – dle skutečnosti.' ] ),
					fc_field( 'registry', 'Zápis v rejstříku (nepovinné)', 'text', [ 'placeholder' => 'Zapsán v živnostenském rejstříku' ] ),
					fc_field( 'insurance', 'Pojištění (text do patičky a kontaktu)', 'text', [ 'placeholder' => 'Pojištění odpovědnosti za škodu u … s limitem … Kč' ] ),
					fc_field( 'footer_about', 'Krátký popis do patičky', 'textarea', [ 'rows' => 2, 'new_lines' => '' ] ),
					fc_field( 'tab_contact', 'Kontakt', 'tab' ),
					fc_field( 'phone', 'Telefon', 'text', [ 'placeholder' => '+420 000 000 000' ] ),
					fc_field( 'email', 'E-mail (veřejný)', 'email', [ 'placeholder' => 'info@falconcore.cz' ] ),
					fc_field( 'lead_email', 'Kam posílat poptávky', 'email', [ 'instructions' => 'Když je prázdné, použije se veřejný e-mail. Poptávky se navíc vždy ukládají do menu „Poptávky“.' ] ),
					fc_field( 'tab_analytics', 'Měření', 'tab' ),
					fc_field(
						'gtm_id',
						'ID kontejneru Google Tag Manager',
						'text',
						[
							'placeholder'  => FC_GTM_PLACEHOLDER,
							'instructions' => 'Ve tvaru GTM-XXXXXXX. Dokud je prázdné, web načítá zástupné ID ' . FC_GTM_PLACEHOLDER . ' – měření začne fungovat až po vyplnění skutečného kontejneru. Konverze, které web posílá do dataLayeru: generate_lead, phone_click, email_click.',
						]
					),
				],
				'field_fc_company'
			),
			'location' => [
				[
					[
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'fc-firma',
					],
				],
			],
		]
	);

	/* ---------- Hero ---------- */
	fc_block_fields(
		'hero',
		[
			fc_field(
				'variant',
				'Varianta',
				'button_group',
				[
					'choices'       => [
						'home' => 'Úvodní (velká)',
						'page' => 'Podstránka',
					],
					'default_value' => 'page',
				]
			),
			$eyebrow,
			fc_field( 'title', 'Nadpis (H1)', 'textarea', [ 'rows' => 2, 'new_lines' => 'br', 'instructions' => 'Enter = zalomení řádku.' ] ),
			fc_field( 'text', 'Text', 'textarea', [ 'rows' => 3, 'new_lines' => '' ] ),
			fc_field(
				'tags',
				'Štítky (např. typy budov)',
				'repeater',
				[
					'layout'       => 'table',
					'button_label' => 'Přidat štítek',
					'max'          => 10,
					'sub_fields'   => [ fc_field( 'text', 'Text', 'text' ) ],
				]
			),
			fc_field( 'btn_primary', 'Hlavní tlačítko', 'link' ),
			fc_field( 'btn_secondary', 'Vedlejší tlačítko', 'link' ),
			fc_field( 'image', 'Fotka na pozadí', 'image', [ 'return_format' => 'id', 'preview_size' => 'medium', 'instructions' => 'Vlastní fotka z realizace (min. 1920 px na šířku). Bez fotky se použije tmavé technické pozadí.' ] ),
			fc_field(
				'trust',
				'Body důvěry pod tlačítky',
				'repeater',
				[
					'layout'       => 'table',
					'button_label' => 'Přidat bod',
					'max'          => 4,
					'sub_fields'   => [ fc_field( 'text', 'Text', 'text' ) ],
				]
			),
		]
	);

	/* ---------- Čísla / výhody ---------- */
	fc_block_fields(
		'stats',
		[
			$bg,
			$eyebrow,
			$title,
			fc_field(
				'items',
				'Položky',
				'repeater',
				[
					'layout'       => 'block',
					'button_label' => 'Přidat položku',
					'max'          => 4,
					'sub_fields'   => [
						fc_field( 'value', 'Hodnota', 'text', [ 'wrapper' => [ 'width' => 25 ] ] ),
						fc_field( 'label', 'Popisek', 'text', [ 'wrapper' => [ 'width' => 25 ] ] ),
						fc_field( 'text', 'Vysvětlení', 'textarea', [ 'rows' => 2, 'new_lines' => '', 'wrapper' => [ 'width' => 50 ] ] ),
					],
				]
			),
			fc_field( 'note', 'Poznámka pod čarou', 'text', [ 'instructions' => 'Např. vysvětlení, z čeho srovnání vychází.' ] ),
		]
	);

	/* ---------- Služby (karty) ---------- */
	fc_block_fields(
		'services',
		[
			$bg,
			$eyebrow,
			$title,
			$lead,
			fc_field(
				'items',
				'Služby',
				'repeater',
				[
					'layout'       => 'block',
					'button_label' => 'Přidat službu',
					'max'          => 6,
					'sub_fields'   => [
						fc_field( 'icon', 'Ikona', 'select', [ 'choices' => $icons, 'wrapper' => [ 'width' => 30 ] ] ),
						fc_field( 'title', 'Název', 'text', [ 'wrapper' => [ 'width' => 70 ] ] ),
						fc_field( 'text', 'Popis', 'textarea', [ 'rows' => 3, 'new_lines' => '' ] ),
						fc_field( 'link', 'Odkaz', 'link' ),
					],
				]
			),
		]
	);

	/* ---------- Srovnání ---------- */
	fc_block_fields(
		'compare',
		[
			$bg,
			$eyebrow,
			$title,
			$lead,
			fc_field( 'col_a', 'Sloupec A (my)', 'text', [ 'wrapper' => [ 'width' => 50 ] ] ),
			fc_field( 'col_b', 'Sloupec B (alternativa)', 'text', [ 'wrapper' => [ 'width' => 50 ] ] ),
			fc_field( 'col_c', 'Sloupec C (další alternativa, nepovinné)', 'text' ),
			fc_field(
				'rows',
				'Řádky',
				'repeater',
				[
					'layout'       => 'table',
					'button_label' => 'Přidat řádek',
					'sub_fields'   => [
						fc_field( 'param', 'Parametr', 'text' ),
						fc_field( 'a', 'Sloupec A', 'textarea', [ 'rows' => 2, 'new_lines' => '' ] ),
						fc_field( 'b', 'Sloupec B', 'textarea', [ 'rows' => 2, 'new_lines' => '' ] ),
						fc_field( 'c', 'Sloupec C', 'textarea', [ 'rows' => 2, 'new_lines' => '' ] ),
					],
				]
			),
			fc_field( 'note', 'Poznámka pod tabulkou', 'text' ),
			fc_field( 'more', 'Odkaz pod tabulkou (nepovinné)', 'link' ),
		]
	);

	/* ---------- Postup ---------- */
	fc_block_fields(
		'process',
		[
			$bg,
			$eyebrow,
			$title,
			$lead,
			fc_field(
				'steps',
				'Kroky',
				'repeater',
				[
					'layout'       => 'table',
					'button_label' => 'Přidat krok',
					'max'          => 6,
					'sub_fields'   => [
						fc_field( 'title', 'Název', 'text' ),
						fc_field( 'text', 'Popis', 'textarea', [ 'rows' => 2, 'new_lines' => '' ] ),
					],
				]
			),
		]
	);

	/* ---------- Text + obrázek ---------- */
	fc_block_fields(
		'textmedia',
		[
			$bg,
			$eyebrow,
			$title,
			fc_field( 'text', 'Text', 'wysiwyg', [ 'tabs' => 'visual', 'toolbar' => 'basic', 'media_upload' => 0 ] ),
			fc_field( 'points_title', 'Nadpis seznamu bodů', 'text' ),
			fc_field( 'points', 'Body (jeden na řádek)', 'textarea', [ 'rows' => 5, 'new_lines' => '' ] ),
			fc_field( 'surface_title', 'Název rozbalovacího odkazu (nepovinné)', 'text', [ 'instructions' => 'Např. „Jak myjeme sendvičové a trapézové panely“. Prázdné = „Jak na to jdeme u tohoto povrchu“.' ] ),
			fc_field( 'material', 'Povrch', 'textarea', [ 'rows' => 2, 'new_lines' => '', 'instructions' => 'Nepovinné. Blok „Povrch → Typické znečištění → Jak myjeme → Výsledek“ pod textem.' ] ),
			fc_field( 'problem', 'Typické znečištění', 'textarea', [ 'rows' => 2, 'new_lines' => '' ] ),
			fc_field( 'method', 'Jak myjeme', 'textarea', [ 'rows' => 2, 'new_lines' => '' ] ),
			fc_field( 'benefit', 'Výsledek', 'textarea', [ 'rows' => 2, 'new_lines' => '' ] ),
			fc_field( 'image', 'Obrázek', 'image', [ 'return_format' => 'id', 'preview_size' => 'medium', 'instructions' => 'Bez obrázku se body zobrazí jako karta vedle textu.' ] ),
			fc_field(
				'image_side',
				'Obrázek / karta',
				'button_group',
				[
					'choices'       => [
						'right' => 'Vpravo',
						'left'  => 'Vlevo',
					],
					'default_value' => 'right',
				]
			),
			fc_field( 'btn', 'Tlačítko', 'link' ),
		]
	);

	/* ---------- Karty s ikonami ---------- */
	fc_block_fields(
		'features',
		[
			$bg,
			$eyebrow,
			$title,
			$lead,
			fc_field(
				'columns',
				'Sloupce',
				'button_group',
				[
					'choices'       => [
						'2' => '2',
						'3' => '3',
						'4' => '4',
					],
					'default_value' => '3',
				]
			),
			fc_field(
				'items',
				'Položky',
				'repeater',
				[
					'layout'       => 'block',
					'button_label' => 'Přidat položku',
					'sub_fields'   => [
						fc_field( 'icon', 'Ikona', 'select', [ 'choices' => $icons, 'wrapper' => [ 'width' => 30 ] ] ),
						fc_field( 'title', 'Nadpis', 'text', [ 'wrapper' => [ 'width' => 70 ] ] ),
						fc_field( 'text', 'Text', 'textarea', [ 'rows' => 3, 'new_lines' => '' ] ),
					],
				]
			),
		]
	);

	/* ---------- FAQ ---------- */
	fc_block_fields(
		'faq',
		[
			$bg,
			$eyebrow,
			$title,
			$lead,
			fc_field(
				'items',
				'Otázky',
				'repeater',
				[
					'layout'       => 'block',
					'button_label' => 'Přidat otázku',
					'sub_fields'   => [
						fc_field( 'q', 'Otázka', 'text' ),
						fc_field( 'a', 'Odpověď', 'textarea', [ 'rows' => 3, 'new_lines' => 'wpautop' ] ),
					],
				]
			),
		]
	);

	/* ---------- CTA ---------- */
	fc_block_fields(
		'cta',
		[
			fc_field(
				'style',
				'Styl',
				'button_group',
				[
					'choices'       => [
						'dark'   => 'Tmavý',
						'accent' => 'Modrý',
					],
					'default_value' => 'dark',
				]
			),
			$title,
			fc_field( 'text', 'Text', 'textarea', [ 'rows' => 2, 'new_lines' => '' ] ),
			fc_field( 'btn', 'Tlačítko', 'link' ),
			fc_field( 'show_phone', 'Zobrazit telefon', 'true_false', [ 'ui' => 1, 'default_value' => 1 ] ),
		]
	);

	/* ---------- Kontakt + formulář ---------- */
	fc_block_fields(
		'contact',
		[
			$eyebrow,
			$title,
			$lead,
			fc_field(
				'types',
				'Typy objektu ve formuláři (jeden na řádek)',
				'textarea',
				[
					'rows'      => 6,
					'new_lines' => '',
				]
			),
			fc_field( 'company_optional', 'Pole „Firma“ nepovinné', 'true_false', [ 'ui' => 1, 'default_value' => 0, 'instructions' => 'Zapnuto: popisek „Firma nebo SVJ (nepovinné)“, aby formulář vyplnil i zástupce SVJ.' ] ),
			fc_field( 'next_steps', 'Co vás čeká (jeden bod na řádek, nepovinné)', 'textarea', [ 'rows' => 3, 'new_lines' => '', 'instructions' => 'Krátký seznam s fajfkami nad formulářem.' ] ),
			fc_field( 'person', 'Kontaktní osoba (nepovinné)', 'text', [ 'instructions' => 'Např. „Matyáš Herman, obchod a obhlídky“. Zobrazí se nad telefonem.' ] ),
			fc_field( 'side_note', 'Poznámka u kontaktů', 'text', [ 'instructions' => 'Např. do kdy se ozvete. Pište jen to, co reálně dodržíte.' ] ),
			fc_field( 'show_company', 'Zobrazit firemní údaje', 'true_false', [ 'ui' => 1, 'default_value' => 1 ] ),
		]
	);
}
