<?php

declare(strict_types=1);

namespace Digihood\Branding\Modules\Branding;

class PluginsPage
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
        add_action('admin_head', [$this, 'suppressNotices']);
    }

    public function suppressNotices(): void
    {
        $screen = get_current_screen();
        if (!$screen || $screen->id !== 'toplevel_page_d1g1-plugins') {
            return;
        }

        remove_all_actions('admin_notices');
        remove_all_actions('all_admin_notices');
    }

    public function enqueueAssets(string $hook): void
    {
        if ($hook !== 'toplevel_page_d1g1-plugins') {
            return;
        }

        $basePath = D1G1_PATHS_digibrand;
        $baseUrl = D1G1_URL_digibrand;

        wp_enqueue_style(
            'digihood-admin-pages',
            $baseUrl . 'assets/css/admin-pages.css',
            [],
            filemtime($basePath . 'assets/css/admin-pages.css')
        );
    }

    public static function render(): void
    {
        $pluginUrl = D1G1_URL_digibrand;
        $plugins = self::getDigihoodPlugins();
        $isCzech = in_array(get_locale(), ['cs_CZ', 'sk_SK'], true);

        include D1G1_PATHS_digibrand . 'templates/plugins-page.php';
    }

    /**
     * Detect installed plugins with d1g1/digihood in slug or name
     */
    private static function getDigihoodPlugins(): array
    {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $allPlugins = get_plugins();
        $activePlugins = get_option('active_plugins', []);
        $result = [];

        foreach ($allPlugins as $file => $data) {
            $slug = dirname($file);
            $name = strtolower($data['Name'] ?? '');

            if (
                str_contains($slug, 'd1g1') ||
                str_contains($slug, 'digihood') ||
                str_contains($slug, 'digibrand') ||
                str_contains($name, 'digihood') ||
                str_contains($name, 'd1g1')
            ) {
                $result[] = [
                    'name' => $data['Name'],
                    'version' => $data['Version'] ?? '—',
                    'active' => in_array($file, $activePlugins, true),
                ];
            }
        }

        return $result;
    }
}
