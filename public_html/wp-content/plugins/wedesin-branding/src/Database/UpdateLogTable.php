<?php

declare(strict_types=1);

namespace Digihood\Branding\Database;

class UpdateLogTable
{
    private static function table(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'digihood_update_logs';
    }

    public static function insert(
        string $itemName,
        string $itemType,
        string $oldVersion,
        string $newVersion,
        string $source = 'other'
    ): int|false {
        global $wpdb;
        $result = $wpdb->insert(self::table(), [
            'item_name' => $itemName,
            'item_type' => $itemType,
            'old_version' => $oldVersion,
            'new_version' => $newVersion,
            'source' => $source,
        ]);
        return $result ? (int) $wpdb->insert_id : false;
    }

    public static function getUnreported(string $source = 'digihood'): array
    {
        global $wpdb;
        $table = self::table();
        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table} WHERE reported = 0 AND source = %s ORDER BY time DESC",
                $source
            )
        );
    }

    public static function getAll(int $limit = 100): array
    {
        global $wpdb;
        $table = self::table();
        return $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$table} ORDER BY time DESC LIMIT %d", $limit)
        );
    }

    public static function getBySource(string $source, int $limit = 50): array
    {
        global $wpdb;
        $table = self::table();
        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table} WHERE source = %s ORDER BY time DESC LIMIT %d",
                $source,
                $limit
            )
        );
    }

    public static function markAsReported(array $ids): bool
    {
        if (empty($ids)) return false;
        global $wpdb;
        $table = self::table();
        $placeholders = implode(',', array_fill(0, count($ids), '%d'));
        return (bool) $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$table} SET reported = 1 WHERE id IN ({$placeholders})",
                ...$ids
            )
        );
    }

    public static function deleteUnreported(string $source = 'digihood'): int
    {
        global $wpdb;
        $table = self::table();
        return (int) $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$table} WHERE reported = 0 AND source = %s",
                $source
            )
        );
    }
}
