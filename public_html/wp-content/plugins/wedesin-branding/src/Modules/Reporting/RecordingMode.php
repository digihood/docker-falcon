<?php

declare(strict_types=1);

namespace Digihood\Branding\Modules\Reporting;

class RecordingMode
{
    private const OPTION_ACTIVE = 'digihood_recording_active';
    private const OPTION_STARTED = 'digihood_recording_started_at';
    private const OPTION_USER = 'digihood_recording_user_id';
    private const ALLOWED_DOMAINS = ['@digihood.cz', '@wedesin.cz'];

    public function __construct()
    {
        add_action('wp_ajax_digihood_start_recording', [$this, 'handleStart']);
        add_action('wp_ajax_digihood_stop_recording', [$this, 'handleStop']);
    }

    public static function isActive(): bool
    {
        return (bool) get_option(self::OPTION_ACTIVE, false);
    }

    public static function getStartedAt(): ?string
    {
        $time = get_option(self::OPTION_STARTED, '');
        return $time ? (string) $time : null;
    }

    public static function getRecordingUserId(): int
    {
        return (int) get_option(self::OPTION_USER, 0);
    }

    public static function canCurrentUserRecord(): bool
    {
        $user = wp_get_current_user();
        if (!$user->exists()) {
            return false;
        }
        $email = strtolower($user->user_email);
        foreach (self::ALLOWED_DOMAINS as $domain) {
            if (str_ends_with($email, $domain)) {
                return true;
            }
        }
        return false;
    }

    public static function start(): void
    {
        update_option(self::OPTION_ACTIVE, true);
        update_option(self::OPTION_STARTED, current_time('mysql'));
        update_option(self::OPTION_USER, get_current_user_id());
    }

    public static function stop(): void
    {
        update_option(self::OPTION_ACTIVE, false);
        delete_option(self::OPTION_STARTED);
        delete_option(self::OPTION_USER);
    }

    public function handleStart(): void
    {
        check_ajax_referer('digihood_admin_nonce', 'nonce');

        if (!self::canCurrentUserRecord()) {
            wp_send_json_error('Nemáte oprávnění k záznamu.');
        }

        self::start();
        wp_send_json_success([
            'message' => 'Záznam zahájen.',
            'started_at' => self::getStartedAt(),
        ]);
    }

    public function handleStop(): void
    {
        check_ajax_referer('digihood_admin_nonce', 'nonce');

        if (!self::canCurrentUserRecord()) {
            wp_send_json_error('Nemáte oprávnění.');
        }

        self::stop();
        wp_send_json_success('Záznam ukončen.');
    }
}
