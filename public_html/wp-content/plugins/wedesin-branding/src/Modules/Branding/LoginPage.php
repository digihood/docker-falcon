<?php

declare(strict_types=1);

namespace Digihood\Branding\Modules\Branding;

use Digihood\Branding\Database\EmailHistoryTable;

class LoginPage
{
    public function __construct()
    {
        add_action('login_enqueue_scripts', [$this, 'enqueueStyles']);
        add_action('login_head', [$this, 'addFavicon']);
        add_action('login_head', [$this, 'addCustomCSS']);
        add_filter('login_headerurl', [$this, 'headerUrl']);
        add_filter('login_headertext', [$this, 'headerText']);
        add_filter('login_body_class', [$this, 'bodyClass']);
        add_action('login_footer', [$this, 'addGradientBackground']);
        add_action('login_footer', [$this, 'addFooterLink']);
    }

    public function enqueueStyles(): void
    {
        wp_enqueue_style(
            'digihood-login',
            D1G1_URL_digibrand . 'assets/css/login.css',
            [],
            filemtime(D1G1_PATHS_digibrand . 'assets/css/login.css')
        );
    }

    public function addFavicon(): void
    {
        echo '<link rel="icon" href="' . esc_url(D1G1_URL_digibrand . 'assets/icons/digihood_icon-new.svg') . '" type="image/svg+xml">' . "\n";
    }

    public function addCustomCSS(): void
    {
        $lastReport = EmailHistoryTable::getLastReport();
        if ($lastReport) {
            $date = wp_date('j.n.Y', strtotime($lastReport->sent_at));
            echo '<style>.digihood-login .dh-maintenance-badge::after { content: "Poslední údržba: ' . esc_attr($date) . '"; }</style>' . "\n";
        }
    }

    public function headerUrl(): string
    {
        return 'https://digihood.cz';
    }

    public function headerText(): string
    {
        return 'Spravováno agenturou digihood';
    }

    public function bodyClass(array $classes): array
    {
        $classes[] = 'digihood-login';
        return $classes;
    }

    public function addGradientBackground(): void
    {
        echo '<div class="dh-gradient-bg"></div>' . "\n";
    }

    public function addFooterLink(): void
    {
        echo '<div class="dh-login-footer"><a href="https://digihood.cz" target="_blank" rel="noopener">digihood.cz</a></div>' . "\n";
    }
}
