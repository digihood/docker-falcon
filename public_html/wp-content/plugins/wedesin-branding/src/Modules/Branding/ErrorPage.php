<?php

declare(strict_types=1);

namespace Digihood\Branding\Modules\Branding;

class ErrorPage
{
    public function __construct()
    {
        add_filter('wp_die_handler', [$this, 'overrideHandler']);
    }

    public function overrideHandler(callable $handler): callable
    {
        if (is_admin()) {
            return $handler;
        }

        return [$this, 'handleDie'];
    }

    public function handleDie(string|\WP_Error $message, string $title = '', array|string $args = []): void
    {
        $defaults = ['response' => 500, 'back_link' => false];
        $args = wp_parse_args($args, $defaults);

        if (is_wp_error($message)) {
            $errors = $message->get_error_messages();
            $message = match (count($errors)) {
                0 => '',
                1 => $errors[0],
                default => implode('<br>', $errors),
            };
        }

        $message = wp_kses_post($message);
        $title = $title ?: 'Chyba';
        $homeUrl = home_url('/');
        $logoUrl = D1G1_URL_digibrand . 'assets/icons/digihood_icon-white.svg';
        $response = $args['response'];
        $backLink = $args['back_link'] ?? false;

        if (!headers_sent()) {
            status_header((int) $response);
            nocache_headers();
            header('Content-Type: text/html; charset=utf-8');
        }

        include D1G1_PATHS_digibrand . 'templates/error-page.php';
        die();
    }
}
