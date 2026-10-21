<?php
/**
 * Základní nastavení šablony.
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'html5', [ 'search-form', 'gallery', 'caption', 'style', 'script' ] );
		add_theme_support(
			'custom-logo',
			[
				'height'      => 80,
				'width'       => 320,
				'flex-height' => true,
				'flex-width'  => true,
			]
		);
		add_theme_support( 'editor-styles' );
		// Pro editor varianta bez kaskádových vrstev – jinak by styly editoru
		// přebily vzhled bloků v náhledu (viz build/flatten-layers.js).
		add_editor_style( 'assets/css/editor.css' );
		remove_theme_support( 'core-block-patterns' );

		register_nav_menus(
			[
				'primary' => 'Hlavní menu',
				'footer'  => 'Menu v patičce (právní odkazy)',
			]
		);
	}
);

// CSS pro web i editor (ACF náhledy bloků vypadají stejně jako na webu).
// V editoru se použije varianta bez @layer, aby ji styly editoru nepřebily.
add_action(
	'enqueue_block_assets',
	function () {
		$file = is_admin() ? 'editor' : 'main';
		wp_enqueue_style( 'falcon-core', FC_URI . '/assets/css/' . $file . '.css', [], FC_VERSION );
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_script(
			'falcon-core',
			FC_URI . '/assets/js/main.js',
			[],
			FC_VERSION,
			[
				'in_footer' => true,
				'strategy'  => 'defer',
			]
		);
		// Na firemním webu nepotřebujeme styly core bloků ani globální styly z theme.json –
		// vzhled řeší Tailwind. Ušetří to ~90 kB CSS na každém načtení.
		wp_dequeue_style( 'classic-theme-styles' );
		wp_dequeue_style( 'wp-block-library' );
	},
	20
);

// Globální styly z theme.json (presety barev a velikostí core bloků) web nepoužívá.
add_action(
	'init',
	function () {
		if ( is_admin() ) {
			return;
		}
		remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
		remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
	}
);

// Úklid <head>: odkazy, které firemní web nepotřebuje.
remove_action( 'wp_head', 'rel_canonical' ); // Vlastní kanonická je v inc/seo.php.
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

// Font se načítá hned, ať se text nepřekresluje.
add_action(
	'wp_head',
	function () {
		foreach ( [ 'inter-latin-wght-normal', 'inter-latin-ext-wght-normal' ] as $fc_font ) {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
				esc_url( FC_URI . '/assets/fonts/inter/' . $fc_font . '.woff2' )
			);
		}
	},
	1
);

// Úklid hlavičky.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );

// Komentáře nepoužíváme.
add_action(
	'init',
	function () {
		remove_post_type_support( 'page', 'comments' );
		remove_post_type_support( 'post', 'comments' );
	}
);
add_filter( 'comments_open', '__return_false', 20 );
add_action(
	'admin_menu',
	function () {
		remove_menu_page( 'edit-comments.php' );
	}
);

// Kategorie bloků v editoru.
add_filter(
	'block_categories_all',
	function ( $categories ) {
		array_unshift(
			$categories,
			[
				'slug'  => 'falcon-core',
				'title' => 'Falcon Core',
				'icon'  => null,
			]
		);
		return $categories;
	}
);

// Na stránkách povolíme jen naše bloky + základní textové bloky (drží to web konzistentní).
add_filter(
	'allowed_block_types_all',
	function ( $allowed, $context ) {
		if ( empty( $context->post ) || 'page' !== $context->post->post_type ) {
			return $allowed;
		}
		$blocks = [ 'core/paragraph', 'core/heading', 'core/list', 'core/list-item', 'core/separator', 'core/table' ];
		foreach ( fc_block_names() as $name ) {
			$blocks[] = 'falcon/' . $name;
		}
		return $blocks;
	},
	10,
	2
);

add_filter(
	'body_class',
	function ( $classes ) {
		if ( is_singular() && fc_starts_with_hero() ) {
			$classes[] = 'fc-has-hero';
		}
		return $classes;
	}
);

/**
 * Začíná obsah stránky blokem Hero?
 */
function fc_starts_with_hero( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return false;
	}
	foreach ( parse_blocks( $post->post_content ) as $block ) {
		if ( empty( $block['blockName'] ) ) {
			continue;
		}
		return 'falcon/hero' === $block['blockName'];
	}
	return false;
}

// Texty webu používají obyčejný spojovník „-“, ne dlouhou pomlčku: v titulku záložky
// i bez automatické typografie WordPressu (ta mění „ - “ na pomlčku).
add_filter( 'document_title_separator', fn() => '-' );
add_filter( 'run_wptexturize', '__return_false' );
