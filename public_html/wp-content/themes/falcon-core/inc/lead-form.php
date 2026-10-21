<?php
/**
 * Poptávkový formulář: zpracování, uložení do administrace („Poptávky“), e-mail.
 */

defined( 'ABSPATH' ) || exit;

function fc_default_object_types() {
	return [
		'Kancelářská budova',
		'Průmyslová / logistická hala',
		'Sportovní hala',
		'Obchodní centrum',
		'Hotel',
		'Autosalon',
		'Bytový dům / SVJ',
		'Výrobní areál',
		'Fotovoltaická elektrárna',
		'Jiný objekt',
	];
}

/* ---------- Úložiště poptávek ---------- */
add_action(
	'init',
	function () {
		register_post_type(
			'fc_lead',
			[
				'labels'          => [
					'name'          => 'Poptávky',
					'singular_name' => 'Poptávka',
					'edit_item'     => 'Poptávka',
					'all_items'     => 'Všechny poptávky',
					'not_found'     => 'Zatím žádné poptávky.',
				],
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => true,
				'menu_position'   => 4,
				'menu_icon'       => 'dashicons-email-alt',
				'supports'        => [ 'title' ],
				'capability_type' => 'post',
				'capabilities'    => [ 'create_posts' => 'do_not_allow' ],
				'map_meta_cap'    => true,
			]
		);
	}
);

function fc_lead_fields() {
	return [
		'fc_name'    => 'Jméno',
		'fc_company' => 'Firma',
		'fc_phone'   => 'Telefon',
		'fc_type'    => 'Typ objektu',
		'fc_area'    => 'Plocha',
	];
}

add_action( 'admin_post_nopriv_fc_lead', 'fc_handle_lead' );
add_action( 'admin_post_fc_lead', 'fc_handle_lead' );

function fc_handle_lead() {
	$back = isset( $_POST['fc_ref'] ) ? wp_validate_redirect( esc_url_raw( wp_unslash( $_POST['fc_ref'] ) ), home_url( '/' ) ) : home_url( '/' );
	$fail = function () use ( $back ) {
		wp_safe_redirect( add_query_arg( 'poptavka', 'chyba', $back ) . '#poptavka' );
		exit;
	};

	if ( ! isset( $_POST['fc_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fc_nonce'] ) ), 'fc_lead' ) ) {
		$fail();
	}

	// Anti-spam: honeypot + minimální doba vyplnění. Bot dostane „úspěch“, ať nezkouší dál.
	$ts = isset( $_POST['fc_ts'] ) ? (int) $_POST['fc_ts'] : 0;
	if ( ! empty( $_POST['fc_web'] ) || ( time() - $ts ) < 3 ) {
		wp_safe_redirect( add_query_arg( 'poptavka', 'odeslano', $back ) . '#poptavka' );
		exit;
	}

	// Limit: max. 5 poptávek za hodinu z jedné IP.
	$ip_key = 'fc_lead_' . md5( isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '' );
	$count  = (int) get_transient( $ip_key );
	if ( $count >= 5 ) {
		$fail();
	}

	$data = [];
	foreach ( array_keys( fc_lead_fields() ) as $key ) {
		$data[ $key ] = isset( $_POST[ $key ] ) ? mb_substr( sanitize_text_field( wp_unslash( $_POST[ $key ] ) ), 0, 160 ) : '';
	}

	// „Jiný objekt“ + vlastní text z doplňkového pole.
	$other = isset( $_POST['fc_type_other'] ) ? mb_substr( sanitize_text_field( wp_unslash( $_POST['fc_type_other'] ) ), 0, 120 ) : '';
	if ( 'Jiný objekt' === $data['fc_type'] && '' !== $other ) {
		$data['fc_type'] = 'Jiný: ' . $other;
	}

	$digits = preg_replace( '/\D/', '', $data['fc_phone'] );
	// Firma je na V2 nepovinná (SVJ, soukromé osoby) – povinné jsou jméno, telefon a typ objektu.
	if ( ! $data['fc_name'] || ! $data['fc_type'] || strlen( $digits ) < 9 ) {
		$fail();
	}

	$post_id = wp_insert_post(
		[
			'post_type'   => 'fc_lead',
			'post_status' => 'private',
			'post_title'  => $data['fc_company'] ? $data['fc_company'] . ' – ' . $data['fc_name'] : $data['fc_name'],
		],
		true
	);
	if ( is_wp_error( $post_id ) ) {
		$fail();
	}
	foreach ( $data as $key => $value ) {
		update_post_meta( $post_id, $key, $value );
	}
	update_post_meta( $post_id, 'fc_source', $back );

	set_transient( $ip_key, $count + 1, HOUR_IN_SECONDS );

	// E-mail.
	$to   = fc_opt( 'lead_email', fc_opt( 'email', get_option( 'admin_email' ) ) );
	$body = "Nová poptávka z webu:\n\n";
	foreach ( fc_lead_fields() as $key => $label ) {
		$body .= $label . ': ' . ( '' !== $data[ $key ] ? $data[ $key ] : '—' ) . "\n";
	}
	$body .= "\nStránka: " . $back . "\nV administraci: " . admin_url( 'post.php?post=' . $post_id . '&action=edit' ) . "\n";
	$sent  = wp_mail( $to, 'Nová poptávka: ' . ( $data['fc_company'] ?: $data['fc_name'] ), $body );
	update_post_meta( $post_id, 'fc_mail_sent', $sent ? 'ano' : 'NE – zkontrolujte SMTP' );

	wp_safe_redirect( add_query_arg( 'poptavka', 'odeslano', $back ) . '#poptavka' );
	exit;
}

/* ---------- Zobrazení v administraci ---------- */
add_action(
	'add_meta_boxes_fc_lead',
	function () {
		add_meta_box(
			'fc_lead_detail',
			'Detail poptávky',
			function ( $post ) {
				echo '<table class="widefat striped"><tbody>';
				$rows = fc_lead_fields() + [
					'fc_source'    => 'Odesláno ze stránky',
					'fc_mail_sent' => 'E-mail odeslán',
				];
				foreach ( $rows as $key => $label ) {
					$value = get_post_meta( $post->ID, $key, true );
					if ( 'fc_phone' === $key && $value ) {
						$value = '<a href="' . esc_attr( fc_phone_href( $value ) ) . '">' . esc_html( $value ) . '</a>';
					} else {
						$value = esc_html( $value ?: '—' );
					}
					echo '<tr><th style="width:200px">' . esc_html( $label ) . '</th><td>' . $value . '</td></tr>'; // phpcs:ignore
				}
				echo '<tr><th>Přijato</th><td>' . esc_html( get_the_date( 'j. n. Y H:i', $post ) ) . '</td></tr>';
				echo '</tbody></table>';
			},
			'fc_lead',
			'normal',
			'high'
		);
	}
);

add_filter(
	'manage_fc_lead_posts_columns',
	function () {
		return [
			'cb'       => '<input type="checkbox" />',
			'title'    => 'Firma – jméno',
			'fc_phone' => 'Telefon',
			'fc_type'  => 'Typ objektu',
			'fc_area'  => 'Plocha',
			'date'     => 'Přijato',
		];
	}
);

add_action(
	'manage_fc_lead_posts_custom_column',
	function ( $column, $post_id ) {
		echo esc_html( get_post_meta( $post_id, $column, true ) );
	},
	10,
	2
);
