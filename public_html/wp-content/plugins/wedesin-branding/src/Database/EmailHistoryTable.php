<?php

declare(strict_types=1);

namespace Digihood\Branding\Database;

class EmailHistoryTable
{
    private static function table(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'digihood_email_history';
    }

    public static function insert(
        string $recipient,
        string $subject,
        string $content,
        string $note = '',
        array $logIds = []
    ): int|false {
        global $wpdb;
        $result = $wpdb->insert(self::table(), [
            'recipient' => $recipient,
            'subject' => $subject,
            'content' => $content,
            'note' => $note,
            'log_ids' => wp_json_encode($logIds),
        ]);
        return $result ? (int) $wpdb->insert_id : false;
    }

    public static function getRecent(int $limit = 10): array
    {
        global $wpdb;
        $table = self::table();
        return $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$table} ORDER BY sent_at DESC LIMIT %d", $limit)
        );
    }

    public static function getLastReport(): ?object
    {
        global $wpdb;
        $table = self::table();
        return $wpdb->get_row("SELECT * FROM {$table} ORDER BY sent_at DESC LIMIT 1");
    }

    public static function getAll(): array
    {
        global $wpdb;
        $table = self::table();
        return $wpdb->get_results("SELECT * FROM {$table} ORDER BY sent_at DESC");
    }
}
