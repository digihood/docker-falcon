<?php

declare(strict_types=1);

namespace Digihood\Branding\Settings;

class Settings
{
    private const OPTION_NAME = 'digihood_branding_settings';

    private const DEFAULTS = [
        'reply_to_email' => 'hello@digihood.cz',
        'mail_to_email' => '',
    ];

    public function __construct()
    {
        add_action('wp_ajax_digihood_save_settings', [$this, 'handleAjaxSave']);
        add_action('admin_post_digihood_save_settings', [$this, 'handlePostSave']);
    }

    public static function get(): array
    {
        $saved = get_option(self::OPTION_NAME, []);
        return array_merge(self::DEFAULTS, is_array($saved) ? $saved : []);
    }

    public static function save(array $data): bool
    {
        $settings = self::get();

        if (isset($data['reply_to_email'])) {
            $settings['reply_to_email'] = sanitize_email($data['reply_to_email']);
        }
        if (isset($data['mail_to_email'])) {
            $settings['mail_to_email'] = sanitize_email($data['mail_to_email']);
        }

        return update_option(self::OPTION_NAME, $settings);
    }

    public static function getReplyTo(): string
    {
        $settings = self::get();
        return $settings['reply_to_email'] ?: 'hello@digihood.cz';
    }

    public static function getMailTo(): string
    {
        $settings = self::get();
        return $settings['mail_to_email'] ?: get_option('admin_email');
    }

    public function handleAjaxSave(): void
    {
        check_ajax_referer('digihood_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Nedostatečná oprávnění.');
        }

        self::save([
            'reply_to_email' => sanitize_email($_POST['reply_to_email'] ?? ''),
            'mail_to_email' => sanitize_email($_POST['mail_to_email'] ?? ''),
        ]);

        wp_send_json_success('Nastavení uloženo.');
    }

    /**
     * Non-AJAX fallback (admin-post.php) so saving works even when JS does not
     * run. Saves, then redirects back to the settings tab — prevents the native
     * GET submit that dropped the page context and showed a blank page.
     */
    public function handlePostSave(): void
    {
        check_admin_referer('digihood_save_settings');

        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Nedostatečná oprávnění.', 'digihood-branding'));
        }

        self::save([
            'reply_to_email' => sanitize_email($_POST['reply_to_email'] ?? ''),
            'mail_to_email' => sanitize_email($_POST['mail_to_email'] ?? ''),
        ]);

        wp_safe_redirect(add_query_arg(
            ['page' => 'digihood-reporter', 'tab' => 'settings', 'dh_saved' => '1'],
            admin_url('admin.php')
        ));
        exit;
    }
}
