<?php
/**
 * Global function stubs required by plugin-framework.
 * These functions are referenced by the framework but not defined in it.
 * No namespace — must be in global scope.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('is_licensing')) {
    /**
     * Licensing system check — disabled for this plugin.
     */
    function is_licensing(): bool
    {
        return false;
    }
}

if (!function_exists('is_eng_d1g1')) {
    /**
     * Language check helper — returns true if WP is NOT in Czech/Slovak.
     */
    function is_eng_d1g1(): bool
    {
        return !in_array(get_locale(), ['cs_CZ', 'sk_SK'], true);
    }
}
