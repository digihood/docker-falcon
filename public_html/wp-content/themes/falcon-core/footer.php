<?php
/**
 * Patička: čtyři widgetové sloupce, právní odkazy, podpis a copyright klienta.
 */

defined( 'ABSPATH' ) || exit;

$fc_has_widgets = false;
foreach ( array_keys( fc_footer_sidebars() ) as $fc_sidebar ) {
	if ( is_active_sidebar( $fc_sidebar ) ) {
		$fc_has_widgets = true;
		break;
	}
}
?>
</main>

<footer class="fc-footer">
	<div class="fc-container">
		<?php if ( $fc_has_widgets ) : ?>
			<div class="fc-footer__grid">
				<?php
				foreach ( array_keys( fc_footer_sidebars() ) as $fc_sidebar ) {
					dynamic_sidebar( $fc_sidebar );
				}
				?>
			</div>
		<?php endif; ?>

		<div class="fc-footer__bottom">
			<p>Falcon Core - mytí fasád, opláštění a prosklení dronem</p>
			<?php
			wp_nav_menu(
				[
					'theme_location'       => 'footer',
					'container'            => 'nav',
					'container_aria_label' => 'Právní odkazy',
					'menu_class'           => 'fc-footer__legal',
					'depth'                => 1,
					'fallback_cb'          => false,
				]
			);
			?>
		</div>

		<div class="fc-footer__signature">
			<p>Vytvořil Digihood</p>
		</div>
	</div>

	<div class="fc-footer__copyright">
		<div class="fc-container">
			<p>© <?php echo esc_html( gmdate( 'Y' ) ); ?> Falcon Core</p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
