<?php
/**
 * Blok: Kontakt + poptávkový formulář.
 *
 * @var array $block
 * @var bool  $is_preview
 */

defined( 'ABSPATH' ) || exit;

$types = fc_lines( get_field( 'types' ) );
if ( ! $types ) {
	$types = fc_default_object_types();
}
$person       = get_field( 'person' );
$next_steps   = fc_lines( get_field( 'next_steps' ) );
$side_note    = get_field( 'side_note' );
$show_company = get_field( 'show_company' );
$company_opt  = (bool) get_field( 'company_optional' );
$phone        = fc_company( 'phone' );
$email        = fc_company( 'email' );
$insurance    = fc_opt( 'insurance' );
$status       = isset( $_GET['poptavka'] ) ? sanitize_key( wp_unslash( $_GET['poptavka'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
$privacy_url  = get_privacy_policy_url() ?: fc_page_url( 'zasady-zpracovani-osobnich-udaju' );
$uid          = 'fc-' . substr( md5( $block['id'] ?? 'contact' ), 0, 6 );
?>
<section <?php echo fc_block_attrs( $block, 'fc-section fc-contact fc-bg-light' ); ?>>
	<div class="fc-container fc-contact__grid">
		<div class="fc-contact__form">
			<?php get_template_part( 'template-parts/section-head' ); ?>

			<?php if ( $next_steps && 'odeslano' !== $status ) : ?>
				<ul class="fc-next">
					<?php foreach ( $next_steps as $step ) : ?>
						<li><?php fc_icon( 'check' ); ?><span><?php echo esc_html( $step ); ?></span></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( 'odeslano' === $status ) : ?>
				<div class="fc-alert fc-alert--ok" role="status" id="poptavka">
					<?php fc_icon( 'check' ); ?>
					<div><strong>Děkujeme, poptávka je odeslaná.</strong><br>Ozveme se vám na uvedený telefon.</div>
				</div>
			<?php else : ?>
				<?php if ( 'chyba' === $status ) : ?>
					<div class="fc-alert fc-alert--error" role="alert">
						Poptávku se nepodařilo odeslat. Zkontrolujte prosím vyplněná pole, nebo nám rovnou zavolejte<?php echo fc_has_real( 'phone' ) ? ' na ' . esc_html( $phone ) : ''; ?>.
					</div>
				<?php endif; ?>

				<form class="fc-form" id="poptavka" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"<?php echo ! empty( $is_preview ) ? ' inert' : ''; ?>>
					<input type="hidden" name="action" value="fc_lead">
					<input type="hidden" name="fc_ts" value="<?php echo esc_attr( time() ); ?>">
					<input type="hidden" name="fc_ref" value="<?php echo esc_url( get_permalink() ); ?>">
					<?php wp_nonce_field( 'fc_lead', 'fc_nonce' ); ?>
					<div class="fc-hp" aria-hidden="true">
						<label>Nevyplňujte <input type="text" name="fc_web" tabindex="-1" autocomplete="off"></label>
					</div>

					<div class="fc-form__grid">
						<div class="fc-field">
							<label for="<?php echo esc_attr( $uid ); ?>-name">Jméno a příjmení <span aria-hidden="true">*</span></label>
							<input id="<?php echo esc_attr( $uid ); ?>-name" name="fc_name" type="text" autocomplete="name" required maxlength="120">
						</div>
						<div class="fc-field">
							<?php if ( $company_opt ) : ?>
								<label for="<?php echo esc_attr( $uid ); ?>-company">Firma nebo SVJ <small>(nepovinné)</small></label>
							<?php else : ?>
								<label for="<?php echo esc_attr( $uid ); ?>-company">Firma <span aria-hidden="true">*</span></label>
							<?php endif; ?>
							<input id="<?php echo esc_attr( $uid ); ?>-company" name="fc_company" type="text" autocomplete="organization"<?php echo $company_opt ? '' : ' required'; ?> maxlength="160">
						</div>
						<div class="fc-field">
							<label for="<?php echo esc_attr( $uid ); ?>-phone">Telefon <span aria-hidden="true">*</span></label>
							<input id="<?php echo esc_attr( $uid ); ?>-phone" name="fc_phone" type="tel" autocomplete="tel" required maxlength="30" pattern="[0-9+ ()\-]{9,}" title="Telefon ve formátu např. +420 777 123 456">
						</div>
						<div class="fc-field" data-fc-type data-other="Jiný objekt">
							<label for="<?php echo esc_attr( $uid ); ?>-type">Typ objektu <span aria-hidden="true">*</span></label>
							<select id="<?php echo esc_attr( $uid ); ?>-type" name="fc_type" required>
								<option value="">Vyberte…</option>
								<?php foreach ( $types as $type ) : ?>
									<option value="<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $type ); ?></option>
								<?php endforeach; ?>
							</select>
							<?php // Po výběru „Jiný objekt“ JS nahradí výběr tímto polem. ?>
							<div class="fc-type-other" hidden>
								<input id="<?php echo esc_attr( $uid ); ?>-type-other" name="fc_type_other" type="text" maxlength="120" placeholder="O jaký objekt jde?">
								<button type="button" class="fc-type-back" aria-label="Zpět na výběr typu objektu">zpět</button>
							</div>
						</div>
						<div class="fc-field fc-field--full">
							<label for="<?php echo esc_attr( $uid ); ?>-area">Přibližná plocha k mytí <small>(m², nepovinné)</small></label>
							<input id="<?php echo esc_attr( $uid ); ?>-area" name="fc_area" type="text" maxlength="60" placeholder="např. 2 000">
						</div>
					</div>

					<div class="fc-form__footer">
						<button class="fc-btn fc-btn--primary" type="submit"><span>Nezávazně poptat</span><?php fc_icon( 'arrow' ); ?></button>
						<p class="fc-form__legal">
							Údaje použijeme jen k vyřízení vaší poptávky. Více v <a href="<?php echo esc_url( $privacy_url ); ?>">zásadách zpracování osobních údajů</a>.
						</p>
					</div>
				</form>
			<?php endif; ?>
		</div>

		<aside class="fc-contact__info" aria-label="Kontaktní údaje">
			<ul class="fc-contact__list">
				<?php if ( $person ) : ?>
					<li>
						<?php fc_icon( 'person' ); ?>
						<div><small>Váš kontakt</small><span><?php echo esc_html( $person ); ?></span></div>
					</li>
				<?php endif; ?>
				<li>
					<?php fc_icon( 'phone' ); ?>
					<div><small>Telefon</small><a href="<?php echo esc_attr( fc_phone_href( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></div>
				</li>
				<li>
					<?php fc_icon( 'mail' ); ?>
					<div><small>E-mail</small><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></div>
				</li>
				<li>
					<?php fc_icon( 'pin' ); ?>
					<div><small>Sídlo</small><span><?php echo nl2br( esc_html( fc_company( 'address' ) ) ); ?></span></div>
				</li>
			</ul>

			<?php if ( $side_note ) : ?>
				<p class="fc-contact__note"><?php fc_icon( 'clock' ); ?><?php echo esc_html( $side_note ); ?></p>
			<?php endif; ?>

			<?php if ( $show_company ) : ?>
				<dl class="fc-company">
					<div><dt>Firma</dt><dd><?php echo esc_html( fc_company( 'company_name' ) ); ?></dd></div>
					<div><dt>IČO</dt><dd><?php echo esc_html( fc_company( 'ico' ) ); ?></dd></div>
					<?php if ( fc_has_real( 'dic' ) ) : ?>
						<div><dt>DIČ</dt><dd><?php echo esc_html( fc_opt( 'dic' ) ); ?></dd></div>
					<?php endif; ?>
					<?php if ( fc_has_real( 'registry' ) ) : ?>
						<div><dt>Zápis</dt><dd><?php echo esc_html( fc_opt( 'registry' ) ); ?></dd></div>
					<?php endif; ?>
				</dl>
			<?php endif; ?>

			<?php if ( $insurance ) : ?>
				<p class="fc-contact__insurance"><?php fc_icon( 'shield' ); ?><?php echo esc_html( $insurance ); ?></p>
			<?php endif; ?>
		</aside>
	</div>
</section>
