<?php
/**
 * Admin page template — digihood Reporter
 *
 * Variables: $settings, $digihoodLogs, $otherLogs, $history,
 *            $isRecording, $recordingStarted, $canRecord, $pluginUrl,
 *            $currentTab, $isDigihood, $isCzech
 */
if (!defined('ABSPATH')) exit;

$baseUrl = admin_url('admin.php?page=digihood-reporter');
?>
<div class="dh-page-wrap">
    <div class="dh-page-container">

        <!-- Header -->
        <div class="dh-page-header">
            <img src="<?php echo esc_url($pluginUrl . 'assets/img/d1g1-logo-cz.png'); ?>" alt="digihood">
            <p class="dh-page-subtitle">
                <?php echo $isCzech ? 'Report správy a aktualizací' : 'Management & update reporting'; ?>
            </p>
        </div>

        <!-- Tab navigation -->
        <div class="dh-tabs">
            <?php if ($isDigihood): ?>
                <a href="<?php echo esc_url($baseUrl . '&tab=reporting'); ?>"
                   class="dh-tab <?php echo $currentTab === 'reporting' ? 'is-active' : ''; ?>">
                    <span class="dashicons dashicons-chart-area"></span>
                    <?php echo $isCzech ? 'Reporting' : 'Reporting'; ?>
                </a>
            <?php endif; ?>
            <a href="<?php echo esc_url($baseUrl . '&tab=history'); ?>"
               class="dh-tab <?php echo $currentTab === 'history' ? 'is-active' : ''; ?>">
                <span class="dashicons dashicons-backup"></span>
                <?php echo $isCzech ? 'Historie' : 'History'; ?>
            </a>
            <a href="<?php echo esc_url($baseUrl . '&tab=settings'); ?>"
               class="dh-tab <?php echo $currentTab === 'settings' ? 'is-active' : ''; ?>">
                <span class="dashicons dashicons-admin-generic"></span>
                <?php echo $isCzech ? 'Nastavení' : 'Settings'; ?>
            </a>
        </div>

        <!-- TAB: Reporting -->
        <?php if ($currentTab === 'reporting' && $isDigihood): ?>
        <div class="dh-tab-panel is-active">

            <!-- Recording Mode -->
            <?php if ($canRecord): ?>
            <div class="dh-glass dh-recording-card <?php echo $isRecording ? 'is-recording' : ''; ?>" id="dh-recording-card">
                <div class="dh-recording-status">
                    <span class="dh-recording-dot" id="dh-recording-dot"></span>
                    <span id="dh-recording-text">
                        <?php if ($isRecording): ?>
                            <?php echo $isCzech ? 'Zaznamenáváme od' : 'Recording since'; ?> <?php echo esc_html(wp_date('j.n.Y H:i', strtotime($recordingStarted))); ?>
                        <?php else: ?>
                            <?php echo $isCzech ? 'Nezaznamenáváme' : 'Not recording'; ?>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="dh-recording-actions">
                    <?php if ($isRecording): ?>
                        <button type="button" class="dh-btn dh-btn-stop" id="dh-stop-recording">
                            <span class="dashicons dashicons-controls-pause"></span> <?php echo $isCzech ? 'Ukončit záznam' : 'Stop recording'; ?>
                        </button>
                    <?php else: ?>
                        <button type="button" class="dh-btn dh-btn-primary" id="dh-start-recording">
                            <span class="dashicons dashicons-controls-play"></span> <?php echo $isCzech ? 'Začít zaznamenávat' : 'Start recording'; ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Send Report -->
            <div class="dh-glass">
                <h3><span class="dashicons dashicons-email-alt"></span> <?php echo $isCzech ? 'Odeslat report' : 'Send report'; ?></h3>
                <p class="dh-stat">
                    <?php echo $isCzech ? 'Aktualizací k odeslání:' : 'Updates to report:'; ?> <strong id="dh-log-count"><?php echo count($digihoodLogs); ?></strong>
                </p>

                <!-- Pending logs table -->
                <?php if ($digihoodLogs): ?>
                <table class="dh-table" id="dh-digihood-table">
                    <thead>
                        <tr>
                            <th><?php echo $isCzech ? 'Datum' : 'Date'; ?></th>
                            <th><?php echo $isCzech ? 'Typ' : 'Type'; ?></th>
                            <th><?php echo $isCzech ? 'Název' : 'Name'; ?></th>
                            <th><?php echo $isCzech ? 'Verze' : 'Version'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($digihoodLogs as $log): ?>
                        <tr>
                            <td><?php echo esc_html(wp_date('j.n. H:i', strtotime($log->time))); ?></td>
                            <td><span class="dh-tag dh-tag-<?php echo esc_attr($log->item_type); ?>"><?php echo esc_html($log->item_type); ?></span></td>
                            <td><strong><?php echo esc_html($log->item_name); ?></strong></td>
                            <td>
                                <?php if ($log->old_version && $log->old_version !== '?'): ?>
                                    <?php echo esc_html($log->old_version); ?> &rarr; <?php echo esc_html($log->new_version); ?>
                                <?php else: ?>
                                    <?php echo esc_html($log->new_version); ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>

                <div class="dh-field" style="margin-top: 16px;">
                    <label for="dh-note"><?php echo $isCzech ? 'Poznámka (volitelná):' : 'Note (optional):'; ?></label>
                    <textarea id="dh-note" class="dh-input dh-textarea" rows="3" placeholder="<?php echo $isCzech ? 'Např. provedena migrace na PHP 8.2, optimalizace DB...' : 'E.g. PHP 8.2 migration, DB optimization...'; ?>"></textarea>
                </div>

                <?php if (count($digihoodLogs) > 0 && !empty($settings['mail_to_email'])): ?>
                    <button type="button" class="dh-btn dh-btn-primary" id="dh-send-report">
                        <span class="dashicons dashicons-email"></span> <?php echo $isCzech ? 'Odeslat report klientovi' : 'Send report to client'; ?>
                    </button>
                <?php else: ?>
                    <button type="button" class="dh-btn" disabled>
                        <?php echo empty($settings['mail_to_email'])
                            ? ($isCzech ? 'Nejdříve nastavte email klienta' : 'Set client email first')
                            : ($isCzech ? 'Žádné aktualizace k odeslání' : 'No updates to report'); ?>
                    </button>
                <?php endif; ?>
                <?php if (count($digihoodLogs) > 0): ?>
                    <button type="button" class="dh-btn-link dh-btn-danger" id="dh-clear-logs" data-source="digihood">
                        <?php echo $isCzech ? 'Smazat logy bez odeslání' : 'Delete logs without sending'; ?>
                    </button>
                <?php endif; ?>
            </div>

            <!-- Other Updates (collapsible) -->
            <div class="dh-glass dh-card-muted">
                <div class="dh-collapsible-header" id="dh-toggle-other">
                    <h3><span class="dashicons dashicons-visibility"></span> <?php echo $isCzech ? 'Ostatní aktualizace (auto / klient)' : 'Other updates (auto / client)'; ?></h3>
                    <span class="dh-toggle-icon dashicons dashicons-arrow-down-alt2"></span>
                </div>
                <div class="dh-collapsible-body" id="dh-other-body" style="display: none;">
                    <table class="dh-table dh-table-muted">
                        <thead>
                            <tr>
                                <th><?php echo $isCzech ? 'Datum' : 'Date'; ?></th>
                                <th><?php echo $isCzech ? 'Typ' : 'Type'; ?></th>
                                <th><?php echo $isCzech ? 'Název' : 'Name'; ?></th>
                                <th><?php echo $isCzech ? 'Verze' : 'Version'; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($otherLogs): ?>
                                <?php foreach ($otherLogs as $log): ?>
                                <tr>
                                    <td><?php echo esc_html(wp_date('j.n. H:i', strtotime($log->time))); ?></td>
                                    <td><span class="dh-tag dh-tag-muted"><?php echo esc_html($log->item_type); ?></span></td>
                                    <td><?php echo esc_html($log->item_name); ?></td>
                                    <td>
                                        <?php if ($log->old_version && $log->old_version !== '?'): ?>
                                            <?php echo esc_html($log->old_version); ?> &rarr; <?php echo esc_html($log->new_version); ?>
                                        <?php else: ?>
                                            <?php echo esc_html($log->new_version); ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="dh-empty"><?php echo $isCzech ? 'Žádné záznamy.' : 'No records.'; ?></td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <?php endif; ?>

        <!-- TAB: History -->
        <?php if ($currentTab === 'history'): ?>
        <div class="dh-tab-panel is-active">
            <div class="dh-glass">
                <h3><span class="dashicons dashicons-backup"></span> <?php echo $isCzech ? 'Historie odeslaných reportů' : 'Sent report history'; ?></h3>
                <table class="dh-table">
                    <thead>
                        <tr>
                            <th><?php echo $isCzech ? 'Datum' : 'Date'; ?></th>
                            <th><?php echo $isCzech ? 'Příjemce' : 'Recipient'; ?></th>
                            <th><?php echo $isCzech ? 'Předmět' : 'Subject'; ?></th>
                            <th><?php echo $isCzech ? 'Poznámka' : 'Note'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($history): ?>
                            <?php foreach ($history as $h): ?>
                            <tr>
                                <td><?php echo esc_html(wp_date('j.n.Y H:i', strtotime($h->sent_at))); ?></td>
                                <td><?php echo esc_html($h->recipient); ?></td>
                                <td><?php echo esc_html($h->subject); ?></td>
                                <td><?php echo $h->note ? esc_html(mb_strimwidth($h->note, 0, 60, '...')) : '<span class="dh-text-muted">—</span>'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="dh-empty"><?php echo $isCzech ? 'Zatím nebyly odeslány žádné reporty.' : 'No reports sent yet.'; ?></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- TAB: Settings -->
        <?php if ($currentTab === 'settings'): ?>
        <div class="dh-tab-panel is-active">
            <?php if (!empty($_GET['dh_saved'])): ?>
                <div class="dh-notice" style="margin-bottom:20px;">
                    <p style="margin:0;"><?php echo $isCzech ? 'Nastavení uloženo.' : 'Settings saved.'; ?></p>
                </div>
            <?php endif; ?>
            <div class="dh-glass">
                <h3><span class="dashicons dashicons-admin-generic"></span> <?php echo $isCzech ? 'E-mailová konfigurace' : 'Email configuration'; ?></h3>
                <form id="dh-settings-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <?php wp_nonce_field('digihood_save_settings'); ?>
                    <input type="hidden" name="action" value="digihood_save_settings">
                    <div class="dh-field">
                        <label for="dh-reply-to">Reply-to email:</label>
                        <input type="email" id="dh-reply-to" name="reply_to_email" class="dh-input" value="<?php echo esc_attr($settings['reply_to_email']); ?>" placeholder="hello@digihood.cz">
                    </div>
                    <div class="dh-field">
                        <label for="dh-mail-to"><?php echo $isCzech ? 'Email klienta (příjemce):' : 'Client email (recipient):'; ?></label>
                        <input type="email" id="dh-mail-to" name="mail_to_email" class="dh-input" value="<?php echo esc_attr($settings['mail_to_email']); ?>" placeholder="klient@example.com">
                    </div>
                    <button type="submit" class="dh-btn dh-btn-primary">
                        <span class="dashicons dashicons-saved"></span> <?php echo $isCzech ? 'Uložit nastavení' : 'Save settings'; ?>
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>
