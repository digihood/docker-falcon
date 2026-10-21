<?php

declare(strict_types=1);

namespace Digihood\Branding\Modules\Branding;

class AdminNotices
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueueStyles']);
    }

    public function enqueueStyles(): void
    {
        wp_enqueue_style(
            'digihood-admin-branding',
            D1G1_URL_digibrand . 'assets/css/admin-branding.css',
            [],
            filemtime(D1G1_PATHS_digibrand . 'assets/css/admin-branding.css')
        );
    }
}
