<?php

declare(strict_types=1);

namespace Digihood\Branding\Modules\Branding;

class AdminBar
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueueStyles']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueStyles']);
    }

    public function enqueueStyles(): void
    {
        if (!is_user_logged_in()) {
            return;
        }

        $iconUrl = esc_url(D1G1_URL_digibrand . 'assets/icons/digihood_icon-white.svg');
        $reporterUrl = esc_url(admin_url('admin.php?page=digihood-reporter'));

        $css = "
            #wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon:before {
                content: '' !important;
                background: url({$iconUrl}) no-repeat center !important;
                background-size: 20px !important;
                width: 20px !important;
                height: 20px !important;
                display: block;
                top: 2px;
                position: relative;
            }
            #wpadminbar #wp-admin-bar-wp-logo > a.ab-item {
                pointer-events: auto;
            }
        ";

        wp_add_inline_style('admin-bar', $css);
    }
}
