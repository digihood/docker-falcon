<?php
/**
 * Stránka „Připravujeme“ pro neveřejný režim.
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex, nofollow">
	<title>Falcon Core - připravujeme</title>
	<link rel="stylesheet" href="<?php echo esc_url( FC_URI . '/assets/css/main.css?ver=' . FC_VERSION ); ?>">
	<?php wp_site_icon(); ?>
</head>
<body class="fc-soon">
	<main class="fc-soon__inner">
		<span class="fc-logo fc-logo--light"><?php fc_logo( 'dark' ); ?></span>
		<h1>Mytí fasád a oken dronem</h1>
		<p>Web připravujeme.</p>
		<?php if ( fc_has_real( 'email' ) ) : ?>
			<p><a href="mailto:<?php echo esc_attr( fc_opt( 'email' ) ); ?>"><?php echo esc_html( fc_opt( 'email' ) ); ?></a></p>
		<?php endif; ?>
	</main>
</body>
</html>
