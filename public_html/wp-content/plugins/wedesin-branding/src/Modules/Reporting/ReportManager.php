<?php

declare(strict_types=1);

namespace Digihood\Branding\Modules\Reporting;

use Digihood\Branding\Database\UpdateLogTable;
use Digihood\Branding\Database\EmailHistoryTable;
use Digihood\Branding\Modules\Email\Mailer;
use Digihood\Branding\Settings\Settings;

class ReportManager
{
    public function __construct()
    {
        add_action('wp_ajax_digihood_send_report', [$this, 'handleSendReport']);
        add_action('wp_ajax_digihood_clear_logs', [$this, 'handleClearLogs']);
    }

    public function handleSendReport(): void
    {
        check_ajax_referer('digihood_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Nedostatečná oprávnění.');
        }

        $logs = UpdateLogTable::getUnreported('digihood');

        if (empty($logs)) {
            wp_send_json_error('Žádné aktualizace k odeslání.');
        }

        $recipient = Settings::getMailTo();
        $note = sanitize_textarea_field($_POST['note'] ?? '');

        $mailer = new Mailer();
        $result = $mailer->sendReport($recipient, $logs, $note);

        if ($result) {
            $logIds = array_map(fn($log) => (int) $log->id, $logs);
            UpdateLogTable::markAsReported($logIds);

            // Stop recording if active
            if (RecordingMode::isActive()) {
                RecordingMode::stop();
            }

            wp_send_json_success('Report úspěšně odeslán.');
        } else {
            wp_send_json_error('Chyba při odesílání emailu. Zkontrolujte SMTP nastavení.');
        }
    }

    public function handleClearLogs(): void
    {
        check_ajax_referer('digihood_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Nedostatečná oprávnění.');
        }

        $source = sanitize_text_field($_POST['source'] ?? 'digihood');
        UpdateLogTable::deleteUnreported($source);

        wp_send_json_success('Logy smazány.');
    }
}
