<?php

declare(strict_types=1);

namespace Digihood\Branding\Modules\Branding;

class AdminColorScheme
{
    public function __construct()
    {
        add_action('admin_init', [$this, 'registerScheme']);
        add_filter('get_user_option_admin_color', [$this, 'forceDefaultScheme']);
    }

    public function registerScheme(): void
    {
        wp_admin_css_color(
            'digihood',
            'digihood',
            D1G1_URL_digibrand . 'assets/css/admin-color-scheme.css',
            ['#1a1a1a', '#2a2a2a', '#ff817e', '#f4f4f4'],
            [
                'base' => '#cccccc',
                'focus' => '#ffffff',
                'current' => '#ffffff',
            ]
        );
    }

    public function forceDefaultScheme(mixed $color): string
    {
        // Enforce the digihood brand scheme across the whole admin for every
        // user, regardless of any personal color-scheme choice. Intercepts the
        // value at runtime, so stored user meta doesn't need to change.
        return 'digihood';
    }
}
