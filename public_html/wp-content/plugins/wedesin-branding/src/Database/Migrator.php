<?php

declare(strict_types=1);

namespace Digihood\Branding\Database;

class Migrator
{
    private const DB_VERSION = '1.0.0';
    private const DB_VERSION_OPTION = 'digihood_branding_db_version';

    public function __construct()
    {
        $this->maybeUpgrade();
    }

    public static function createTables(): void
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $table_logs = $wpdb->prefix . 'digihood_update_logs';
        $table_history = $wpdb->prefix . 'digihood_email_history';

        $sql_logs = "CREATE TABLE {$table_logs} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            item_name varchar(255) NOT NULL,
            item_type varchar(50) NOT NULL,
            old_version varchar(50) DEFAULT '' NOT NULL,
            new_version varchar(50) DEFAULT '' NOT NULL,
            source varchar(20) DEFAULT 'other' NOT NULL,
            reported tinyint(1) DEFAULT 0 NOT NULL,
            PRIMARY KEY  (id),
            KEY source (source),
            KEY reported (reported)
        ) {$charset_collate};";

        $sql_history = "CREATE TABLE {$table_history} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            sent_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            recipient varchar(255) NOT NULL,
            subject varchar(255) NOT NULL,
            content longtext NOT NULL,
            note text DEFAULT '' NOT NULL,
            log_ids text DEFAULT '' NOT NULL,
            PRIMARY KEY  (id)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql_logs);
        dbDelta($sql_history);

        update_option(self::DB_VERSION_OPTION, self::DB_VERSION);
    }

    private function maybeUpgrade(): void
    {
        $current = get_option(self::DB_VERSION_OPTION, '0');
        if (version_compare($current, self::DB_VERSION, '<')) {
            self::createTables();
        }
    }
}
