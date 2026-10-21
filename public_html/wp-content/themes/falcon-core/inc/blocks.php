<?php
/**
 * Registrace ACF bloků ze složky /blocks (každý má block.json + render.php).
 */

defined( 'ABSPATH' ) || exit;

function fc_block_names() {
	return [ 'hero', 'stats', 'services', 'compare', 'process', 'textmedia', 'features', 'faq', 'cta', 'contact' ];
}

add_action(
	'init',
	function () {
		if ( ! function_exists( 'acf_register_block_type' ) ) {
			return;
		}
		foreach ( fc_block_names() as $name ) {
			register_block_type( FC_DIR . '/blocks/' . $name );
		}
	}
);
