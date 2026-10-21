<?php

declare(strict_types=1);

namespace Digihood\Branding\Modules\Reporting;

use Digihood\Branding\Database\UpdateLogTable;

class UpdateTracker
{
    public function __construct()
    {
        // Cache versions before updates happen
        add_action('pre_auto_update', [$this, 'cacheVersions']);
        add_filter('upgrader_pre_install', [$this, 'cacheVersionsOnManual'], 10, 2);

        // Track completed updates
        add_action('upgrader_process_complete', [$this, 'trackUpdates'], 10, 2);
        add_action('_core_updated_successfully', [$this, 'trackCoreUpdate']);
    }

    public function cacheVersions(): void
    {
        $this->cachePluginVersions();
        $this->cacheThemeVersions();
    }

    public function cacheVersionsOnManual(mixed $response, array $extra): mixed
    {
        $this->cacheVersions();
        return $response;
    }

    public function trackUpdates(object $upgrader, array $options): void
    {
        if (($options['action'] ?? '') !== 'update') {
            return;
        }

        $type = $options['type'] ?? '';
        $source = RecordingMode::isActive() ? 'digihood' : 'other';
        $cachedVersions = get_transient('digihood_pre_update_versions') ?: [];

        if ($type === 'plugin' && !empty($options['plugins'])) {
            foreach ($options['plugins'] as $pluginFile) {
                $pluginData = get_plugin_data(WP_PLUGIN_DIR . '/' . $pluginFile, false);
                $name = $pluginData['Name'] ?: $pluginFile;
                $newVersion = $pluginData['Version'] ?: '?';
                $oldVersion = $cachedVersions['plugins'][$pluginFile] ?? '?';

                UpdateLogTable::insert($name, 'plugin', $oldVersion, $newVersion, $source);
            }
        }

        if ($type === 'theme' && !empty($options['themes'])) {
            foreach ($options['themes'] as $themeSlug) {
                $theme = wp_get_theme($themeSlug);
                $name = $theme->get('Name') ?: $themeSlug;
                $newVersion = $theme->get('Version') ?: '?';
                $oldVersion = $cachedVersions['themes'][$themeSlug] ?? '?';

                UpdateLogTable::insert($name, 'theme', $oldVersion, $newVersion, $source);
            }
        }

        delete_transient('digihood_pre_update_versions');
    }

    public function trackCoreUpdate(string $newVersion): void
    {
        global $wp_version;
        $source = RecordingMode::isActive() ? 'digihood' : 'other';

        UpdateLogTable::insert('WordPress Core', 'core', $wp_version, $newVersion, $source);
    }

    private function cachePluginVersions(): void
    {
        $cached = get_transient('digihood_pre_update_versions') ?: [];
        $plugins = get_plugins();

        $cached['plugins'] = [];
        foreach ($plugins as $file => $data) {
            $cached['plugins'][$file] = $data['Version'] ?? '';
        }

        set_transient('digihood_pre_update_versions', $cached, HOUR_IN_SECONDS);
    }

    private function cacheThemeVersions(): void
    {
        $cached = get_transient('digihood_pre_update_versions') ?: [];
        $themes = wp_get_themes();

        $cached['themes'] = [];
        foreach ($themes as $slug => $theme) {
            $cached['themes'][$slug] = $theme->get('Version');
        }

        set_transient('digihood_pre_update_versions', $cached, HOUR_IN_SECONDS);
    }
}
