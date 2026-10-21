<?php
/**
 * ACF: kontrola pluginu, stránka „Nastavení firmy“, pomocníci pro definici polí.
 */

defined( 'ABSPATH' ) || exit;

// Upozornění, když chybí ACF PRO / Secure Custom Fields.
add_action(
	'admin_notices',
	function () {
		if ( function_exists( 'acf_add_local_field_group' ) && function_exists( 'acf_add_options_page' ) ) {
			return;
		}
		echo '<div class="notice notice-error"><p><strong>Falcon Core:</strong> šablona potřebuje plugin <strong>ACF PRO</strong> nebo zdarma <strong>Secure Custom Fields</strong> (repeatery, bloky, stránka nastavení). Bez něj se bloky nevykreslí.</p></div>';
	}
);

add_action(
	'acf/init',
	function () {
		if ( ! function_exists( 'acf_add_options_page' ) ) {
			return;
		}
		acf_add_options_page(
			[
				'page_title' => 'Nastavení firmy',
				'menu_title' => 'Nastavení firmy',
				'menu_slug'  => 'fc-firma',
				'capability' => 'manage_options',
				'icon_url'   => 'dashicons-building',
				'position'   => 3,
				'redirect'   => false,
			]
		);
	}
);

/**
 * Definice pole bez klíče – klíč se dopočítá (field_fc_{blok}_{pole}).
 */
function fc_field( $name, $label, $type = 'text', $args = [] ) {
	return array_merge(
		[
			'name'  => $name,
			'label' => $label,
			'type'  => $type,
		],
		$args
	);
}

function fc_keyify( array $fields, $prefix ) {
	foreach ( $fields as &$field ) {
		$field['key'] = $prefix . '_' . $field['name'];
		if ( ! empty( $field['sub_fields'] ) ) {
			$field['sub_fields'] = fc_keyify( $field['sub_fields'], $field['key'] );
		}
	}
	return $fields;
}

/**
 * Skupina polí pro blok falcon/{name}.
 */
function fc_block_fields( $name, array $fields ) {
	acf_add_local_field_group(
		[
			'key'      => 'group_fc_' . $name,
			'title'    => 'Blok: ' . $name,
			'fields'   => fc_keyify( $fields, 'field_fc_' . $name ),
			'location' => [
				[
					[
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'falcon/' . $name,
					],
				],
			],
		]
	);
}
