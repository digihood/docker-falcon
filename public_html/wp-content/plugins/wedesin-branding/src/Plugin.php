<?php

declare(strict_types=1);

namespace Digihood\Branding;

use sitemap\framework\pluginSystem\d1g1PluginRequirements;

class Plugin extends d1g1PluginRequirements
{
    public const NAME = 'digihood branding';
    public const VERSION = '5.1.4';
    public const SLUG = 'digibrand';

    private static ?self $instance = null;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->add_activation_hooks(D1G1_PATHS_digibrand . 'plugin.php');

        // DB tables creation on activation
        register_activation_hook(D1G1_PATHS_digibrand . 'plugin.php', [Database\Migrator::class, 'createTables']);

        add_action('plugins_loaded', [$this, 'init']);
    }

    public function init(): void
    {
        // Database (Globals already initialized by framework includes)
        new Database\Migrator();

        // Settings
        new Settings\Settings();

        // Branding modules
        new Modules\Branding\LoginPage();
        new Modules\Branding\AdminBar();
        new Modules\Branding\AdminFooter();
        new Modules\Branding\AdminColorScheme();
        new Modules\Branding\AdminNotices();
        new Modules\Branding\DashboardWidget();
        new Modules\Branding\ErrorPage();
        new Modules\Branding\PluginsPage();

        // Reporting modules
        new Modules\Reporting\UpdateTracker();
        new Modules\Reporting\RecordingMode();
        new Modules\Reporting\ReportManager();
        new Modules\Reporting\AdminPage();

        // GitHub auto-update (bypass framework's UpdatePlugin to avoid license class issues)
        $this->setupAutoUpdate();
    }

    private function setupAutoUpdate(): void
    {
        if (!class_exists('\Puc_v4_Factory')) {
            return;
        }

        $checker = \Puc_v4_Factory::buildUpdateChecker(
            D1G1_GITHUBURL_BRAND,
            D1G1_PATHS_digibrand . 'plugin.php',
            D1G1_GITHUBREP_BRAND
        );
        $checker->setBranch('master');

        if (defined('GithubToken') && GithubToken) {
            $checker->setAuthentication(GithubToken);
        }
    }

    protected function check_plugin_requirements(): array
    {
        global $wp_version;
        $failed = [];

        if (version_compare($wp_version, '6.0', '<')) {
            $failed[] = 'WordPress version must be at least 6.0!';
        }
        if (version_compare(PHP_VERSION, '8.0.0', '<')) {
            $failed[] = 'PHP version must be at least 8.0!';
        }

        return $failed;
    }
}
