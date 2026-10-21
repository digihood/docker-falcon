<?php
/**
 * Patička: widgetové oblasti a čtyři firemní widgety.
 *
 * Obsah patičky se spravuje ve Vzhled → Widgety. Widgety berou data
 * z „Nastavení firmy“, takže se telefon ani IČO nikde nepíše dvakrát.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Čtyři sloupce patičky.
 */
function fc_footer_sidebars() {
	return [
		'footer-1' => [ 'Patička 1 – značka', 'První sloupec. Doporučený widget: „Falcon Core – značka“.' ],
		'footer-2' => [ 'Patička 2 – navigace', 'Druhý sloupec. Doporučený widget: „Falcon Core – navigace“.' ],
		'footer-3' => [ 'Patička 3 – kontakt', 'Třetí sloupec. Doporučený widget: „Falcon Core – kontakt“.' ],
		'footer-4' => [ 'Patička 4 – firemní údaje', 'Čtvrtý sloupec. Doporučený widget: „Falcon Core – firemní údaje“.' ],
	];
}

add_action(
	'widgets_init',
	function () {
		foreach ( fc_footer_sidebars() as $id => $meta ) {
			register_sidebar(
				[
					'id'            => $id,
					'name'          => $meta[0],
					'description'   => $meta[1],
					'before_widget' => '<div class="fc-footer__col %2$s" id="%1$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<p class="fc-footer__title">',
					'after_title'   => '</p>',
				]
			);
		}

		register_widget( 'FC_Widget_Brand' );
		register_widget( 'FC_Widget_Nav' );
		register_widget( 'FC_Widget_Contact' );
		register_widget( 'FC_Widget_Company' );
	}
);

/**
 * Společný základ: titulek se vypisuje jen když ho uživatel vyplní.
 */
abstract class FC_Widget_Base extends WP_Widget {

	protected $default_title = '';

	public function form( $instance ) {
		$title = $instance['title'] ?? $this->default_title;
		printf(
			'<p><label for="%1$s">Nadpis</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%3$s"></p>',
			esc_attr( $this->get_field_id( 'title' ) ),
			esc_attr( $this->get_field_name( 'title' ) ),
			esc_attr( $title )
		);
		echo '<p class="description">Obsah widgetu se bere z <strong>Nastavení firmy</strong>.</p>';
	}

	public function update( $new_instance, $old_instance ) {
		return [ 'title' => sanitize_text_field( $new_instance['title'] ?? '' ) ];
	}

	protected function open( $args, $instance ) {
		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput
		$title = apply_filters( 'widget_title', $instance['title'] ?? $this->default_title, $instance, $this->id_base );
		if ( $title ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput
		}
	}
}

/**
 * Sloupec se značkou, popisem a pojištěním.
 */
class FC_Widget_Brand extends FC_Widget_Base {

	public function __construct() {
		parent::__construct( 'fc_brand', 'Falcon Core – značka', [ 'description' => 'Logo, krátký popis a pojištění z Nastavení firmy.' ] );
	}

	public function widget( $args, $instance ) {
		$about     = fc_opt( 'footer_about', 'Mytí fasád a opláštění hal dronem. Bez plošin, bez lešení, bez lidí ve výšce.' );
		$insurance = fc_opt( 'insurance' );

		$args['before_widget'] = str_replace( 'fc-footer__col', 'fc-footer__col fc-footer__brand', $args['before_widget'] );
		$this->open( $args, $instance );
		?>
		<a class="fc-logo fc-logo--light" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Falcon Core - úvodní stránka"><?php fc_logo( 'dark' ); ?></a>
		<?php if ( $about ) : ?>
			<p><?php echo esc_html( $about ); ?></p>
		<?php endif; ?>
		<?php if ( $insurance ) : ?>
			<p class="fc-footer__insurance"><?php fc_icon( 'shield' ); ?><span><?php echo esc_html( $insurance ); ?></span></p>
		<?php endif; ?>
		<?php
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/**
 * Sloupec s hlavním menu.
 */
class FC_Widget_Nav extends FC_Widget_Base {

	protected $default_title = 'Navigace';

	public function __construct() {
		parent::__construct( 'fc_nav', 'Falcon Core – navigace', [ 'description' => 'Položky hlavního menu (bez podmenu).' ] );
	}

	public function widget( $args, $instance ) {
		$this->open( $args, $instance );
		wp_nav_menu(
			[
				'theme_location' => 'primary',
				'container'      => 'nav',
				'container_aria_label' => 'Menu v patičce',
				'menu_class'     => 'fc-footer__list',
				'depth'          => 1,
				'fallback_cb'    => 'fc_nav_fallback',
			]
		);
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/**
 * Sloupec s telefonem, e-mailem a sídlem.
 */
class FC_Widget_Contact extends FC_Widget_Base {

	protected $default_title = 'Kontakt';

	public function __construct() {
		parent::__construct( 'fc_contact', 'Falcon Core – kontakt', [ 'description' => 'Telefon, e-mail a sídlo z Nastavení firmy.' ] );
	}

	public function widget( $args, $instance ) {
		$phone = fc_company( 'phone' );
		$email = fc_company( 'email' );
		$this->open( $args, $instance );
		?>
		<ul class="fc-footer__list fc-footer__contact">
			<li><a href="<?php echo esc_attr( fc_phone_href( $phone ) ); ?>"><?php fc_icon( 'phone' ); ?><span><?php echo esc_html( $phone ); ?></span></a></li>
			<li><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php fc_icon( 'mail' ); ?><span><?php echo esc_html( $email ); ?></span></a></li>
			<li><?php fc_icon( 'pin' ); ?><span><?php echo nl2br( esc_html( fc_company( 'address' ) ) ); ?></span></li>
		</ul>
		<?php
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/**
 * Sloupec s fakturačními údaji.
 */
class FC_Widget_Company extends FC_Widget_Base {

	protected $default_title = 'Firemní údaje';

	public function __construct() {
		parent::__construct( 'fc_company', 'Falcon Core – firemní údaje', [ 'description' => 'Obchodní firma, IČO, DIČ a zápis v rejstříku.' ] );
	}

	public function widget( $args, $instance ) {
		$this->open( $args, $instance );
		?>
		<dl class="fc-footer__company">
			<dt class="screen-reader-text">Obchodní firma</dt>
			<dd><strong><?php echo esc_html( fc_company( 'company_name' ) ); ?></strong></dd>
			<dt>IČO</dt>
			<dd><?php echo esc_html( fc_company( 'ico' ) ); ?></dd>
			<?php if ( fc_has_real( 'dic' ) ) : ?>
				<dt>DIČ</dt>
				<dd><?php echo esc_html( fc_opt( 'dic' ) ); ?></dd>
			<?php endif; ?>
			<?php if ( fc_has_real( 'registry' ) ) : ?>
				<dt class="screen-reader-text">Zápis</dt>
				<dd class="fc-footer__registry"><?php echo esc_html( fc_opt( 'registry' ) ); ?></dd>
			<?php endif; ?>
		</dl>
		<?php
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/**
 * Po aktivaci šablony naplní prázdnou patičku čtyřmi firemními widgety,
 * aby web vypadal kompletně hned, bez ručního klikání.
 */
add_action(
	'after_switch_theme',
	function () {
		$sidebars = get_option( 'sidebars_widgets', [] );
		$widgets  = [
			'footer-1' => [ 'fc_brand', [ 'title' => '' ] ],
			'footer-2' => [ 'fc_nav', [ 'title' => 'Navigace' ] ],
			'footer-3' => [ 'fc_contact', [ 'title' => 'Kontakt' ] ],
			'footer-4' => [ 'fc_company', [ 'title' => 'Firemní údaje' ] ],
		];

		foreach ( $widgets as $sidebar => $widget ) {
			if ( ! empty( $sidebars[ $sidebar ] ) ) {
				continue; // Uživatel už si sloupec nastavil, neberu mu ho.
			}
			list( $base, $instance ) = $widget;

			$stored    = get_option( 'widget_' . $base, [] );
			$next      = 1;
			foreach ( array_keys( $stored ) as $key ) {
				if ( is_numeric( $key ) ) {
					$next = max( $next, (int) $key + 1 );
				}
			}
			$stored[ $next ]       = $instance;
			$stored['_multiwidget'] = 1;
			update_option( 'widget_' . $base, $stored );

			$sidebars[ $sidebar ] = [ $base . '-' . $next ];
		}

		update_option( 'sidebars_widgets', $sidebars );
	}
);
