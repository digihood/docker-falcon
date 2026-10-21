<?php

declare(strict_types=1);

namespace Digihood\Branding\Modules\Email;

use Digihood\Branding\Database\EmailHistoryTable;
use Digihood\Branding\Settings\Settings;

class Mailer
{
    public function sendReport(string $to, array $logs, string $note = ''): bool
    {
        $subject = Localization::getSubject();
        $html = Template::render($logs, $note);
        $replyTo = Settings::getReplyTo();

        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            "Reply-To: {$replyTo}",
        ];

        $sent = wp_mail($to, $subject, $html, $headers);

        if ($sent) {
            $logIds = array_map(fn($log) => (int) $log->id, $logs);
            EmailHistoryTable::insert($to, $subject, $html, $note, $logIds);
        }

        return $sent;
    }
}
