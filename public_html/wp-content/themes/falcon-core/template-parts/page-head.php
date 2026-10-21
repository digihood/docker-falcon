<?php
/**
 * Záhlaví stránky, která nezačíná blokem Hero.
 *
 * @var array $args {
 *     @type string $title Nadpis. Prázdné = název příspěvku.
 * }
 */

defined( 'ABSPATH' ) || exit;
?>
<header class="fc-pagehead">
	<div class="fc-container"><h1 class="fc-h1"><?php echo esc_html( ! empty( $args['title'] ) ? $args['title'] : get_the_title() ); ?></h1></div>
</header>
