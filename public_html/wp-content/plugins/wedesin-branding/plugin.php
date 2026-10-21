<?php
/*
 * Plugin Name: digihood branding
 * Plugin URI: https://digihood.cz
 * Description: Branding, automatické sledování aktualizací a reportování klientům.
 * Version: 5.1.4
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Author: digihood
 * Author URI: https://digihood.cz
 * License: GPL2
 */

namespace digibrand;

if (!defined('ABSPATH')) {
    exit;
}

// Framework constants (required by plugin-framework)
define('D1G1_BRANDING', 'digibrand');
define('D1G1_SITEMAP', D1G1_BRANDING); // Required by plugin-framework Globals
define('D1G1_PLUGSLUG_' . D1G1_BRANDING, 'd1g1_plugins');
define('D1G1_PLUGNAME_' . D1G1_BRANDING, 'digihood branding');
define('D1G1_PATHS_' . D1G1_BRANDING, plugin_dir_path(__FILE__));
define('D1G1_PATHTOFWASSET_' . D1G1_BRANDING, plugin_dir_path(__FILE__) . 'plugin-framework/FrameworkAssets/');
define('D1G1_URL_' . D1G1_BRANDING, plugin_dir_url(__FILE__));

if (!defined('D1G1_SITE_BOX_LANG_SHOW')) {
    define('D1G1_SITE_BOX_LANG_SHOW', ['cs', 'sk-SK']);
}

define('D1G1_GITHUBURL_BRAND', 'https://github.com/digihood/wedesin-branding');
define('D1G1_GITHUBREP_BRAND', 'wedesin-branding');

// GitHub token for plugin updates (read-only access to repo)
if (!defined('GithubToken')) {
    define('GithubToken', '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');
}

// Framework stubs (must be loaded before framework, in global namespace)
include_once __DIR__ . '/src/framework-stubs.php';

// Plugin-framework includes (globals.php + pluginSystem loaded via framework's own includes.php)
$d1g1FrameworkIncludes = __DIR__ . '/plugin-framework/includes.php';
if (is_readable($d1g1FrameworkIncludes)) {
    include_once $d1g1FrameworkIncludes;
}

// Fail safely if the framework is missing/incomplete (e.g. submodule deployed
// without files) instead of white-screening on the missing parent class in
// src/Plugin.php. Show a clear admin notice and stop loading the plugin.
if (!class_exists('sitemap\\framework\\pluginSystem\\d1g1PluginRequirements')) {
    add_action('admin_notices', static function (): void {
        echo '<div class="notice notice-error"><p><strong>digihood branding:</strong> '
            . 'chybí soubory složky <code>plugin-framework</code>. Nahrajte plugin znovu '
            . 'z oficiálního balíčku <code>wedesin-branding.zip</code>, který framework obsahuje.</p></div>';
    });
    return;
}

// PSR-4 style autoloader for src/ directory
spl_autoload_register(function (string $class): void {
    $prefix = 'Digihood\\Branding\\';
    $baseDir = __DIR__ . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Force-enable automatic background updates for this plugin, so client sites
// pull future releases on their own (via the PUC update checker + WP cron)
// without manual intervention.
$d1g1PluginFile = plugin_basename(__FILE__);
add_filter('auto_update_plugin', static function ($update, $item) use ($d1g1PluginFile) {
    if (isset($item->plugin) && $item->plugin === $d1g1PluginFile) {
        return true;
    }
    return $update;
}, 10, 2);

// Boot the plugin
\Digihood\Branding\Plugin::getInstance();
