<?php

declare(strict_types=1);

namespace Digihood\Branding\Modules\Reporting;

use Digihood\Branding\Database\UpdateLogTable;
use Digihood\Branding\Database\EmailHistoryTable;
use Digihood\Branding\Settings\Settings;

class AdminPage
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'renameParentMenu'], 999);
        add_action('admin_menu', [$this, 'addSubmenu'], 20);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
        add_action('admin_head', [$this, 'suppressNotices']);
    }

    /**
     * Rename the framework's parent menu "Digi sitemaps" → "Digihood"
     * and override the framework's page callback with our PluginsPage renderer
     */
    public function renameParentMenu(): void
    {
        global $menu;
        foreach ($menu as &$item) {
            if (isset($item[2]) && $item[2] === 'd1g1-plugins') {
                $item[0] = 'digihood';
                $item[3] = 'digihood';
                $item[6] = 'dashicons-digihood';
                break;
            }
        }

        // Remove framework's duplicate submenu (slug 'digibrand')
        remove_submenu_page('d1g1-plugins', 'digibrand');

        // Override the framework's parent page callback to our PluginsPage renderer
        $hookname = get_plugin_page_hookname('d1g1-plugins', '');
        if ($hookname) {
            remove_all_actions($hookname);
            add_action($hookname, [\Digihood\Branding\Modules\Branding\PluginsPage::class, 'render']);
        }

        // Rename submenu items
        global $submenu;
        if (isset($submenu['d1g1-plugins'])) {
            foreach ($submenu['d1g1-plugins'] as &$sub) {
                if ($sub[2] === 'd1g1-plugins') {
                    $sub[0] = 'digihood pluginy';
                    $sub[3] = 'digihood pluginy';
                }
            }
        }
    }

    /**
     * Add Report as a submenu under the Digihood parent menu
     */
    public function addSubmenu(): void
    {
        add_submenu_page(
            'd1g1-plugins',
            'digihood report',
            'digihood report',
            'manage_options',
            'digihood-reporter',
            [$this, 'render']
        );
    }

    /**
     * Suppress foreign WP notices on our admin pages
     */
    public function suppressNotices(): void
    {
        $screen = get_current_screen();
        if (!$screen || !str_contains($screen->id, 'digihood-reporter')) {
            return;
        }

        remove_all_actions('admin_notices');
        remove_all_actions('all_admin_notices');
    }

    public function enqueueAssets(string $hook): void
    {
        if (!str_contains($hook, 'digihood-reporter')) {
            return;
        }

        $basePath = D1G1_PATHS_digibrand;
        $baseUrl = D1G1_URL_digibrand;

        // Shared design system
        wp_enqueue_style(
            'digihood-admin-pages',
            $baseUrl . 'assets/css/admin-pages.css',
            [],
            filemtime($basePath . 'assets/css/admin-pages.css')
        );

        // Reporter-specific styles
        wp_enqueue_style(
            'digihood-admin',
            $baseUrl . 'assets/css/admin.css',
            ['digihood-admin-pages'],
            filemtime($basePath . 'assets/css/admin.css')
        );

        wp_enqueue_script(
            'digihood-admin',
            $baseUrl . 'assets/js/admin.js',
            ['jquery'],
            filemtime($basePath . 'assets/js/admin.js'),
            true
        );

        wp_localize_script('digihood-admin', 'digihoodAdmin', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('digihood_admin_nonce'),
            'recording' => RecordingMode::isActive(),
            'recordingStarted' => RecordingMode::getStartedAt(),
            'canRecord' => RecordingMode::canCurrentUserRecord(),
        ]);
    }

    public function render(): void
    {
        $settings = Settings::get();
        $digihoodLogs = UpdateLogTable::getUnreported('digihood');
        $otherLogs = UpdateLogTable::getBySource('other', 50);
        $history = EmailHistoryTable::getRecent(10);
        $isRecording = RecordingMode::isActive();
        $recordingStarted = RecordingMode::getStartedAt();
        $canRecord = RecordingMode::canCurrentUserRecord();
        $pluginUrl = D1G1_URL_digibrand;

        // Tab navigation
        $currentTab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'reporting';
        $allowedTabs = ['reporting', 'history', 'settings'];
        if (!in_array($currentTab, $allowedTabs, true)) {
            $currentTab = 'reporting';
        }

        // Digihood user check
        $user = wp_get_current_user();
        $email = strtolower($user->user_email);
        $isDigihood = str_ends_with($email, '@digihood.cz') || str_ends_with($email, '@wedesin.cz');

        // Non-digihood users default to history tab
        if (!$isDigihood && $currentTab === 'reporting') {
            $currentTab = 'history';
        }

        $isCzech = in_array(get_locale(), ['cs_CZ', 'sk_SK'], true);

        include D1G1_PATHS_digibrand . 'templates/admin-page.php';
    }
}
