<?php
/**
 * Falcon Core – functions.
 */

defined( 'ABSPATH' ) || exit;

define( 'FC_VERSION', '2.0.0' );
define( 'FC_DIR', get_template_directory() );
define( 'FC_URI', get_template_directory_uri() );

foreach ( [ 'helpers', 'setup', 'acf', 'fields', 'blocks', 'widgets', 'lead-form', 'seo', 'analytics', 'maintenance' ] as $fc_file ) {
	require FC_DIR . '/inc/' . $fc_file . '.php';
}
