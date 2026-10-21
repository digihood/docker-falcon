<?php
/**
 * Email šablona — česká verze
 *
 * Dostupné proměnné: $logs, $note, $siteName, $siteUrl, $year
 */
if (!defined('ABSPATH')) exit;
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width: 600px; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">

                    <!-- Header -->
                    <tr>
                        <td style="background: #ff817e; padding: 20px 40px; text-align: center;">
                            <img src="<?php echo esc_url(D1G1_URL_digibrand . 'assets/img/d1g1-logo-email.png'); ?>" alt="digihood" width="200" height="61" style="width: 200px; height: auto; max-width: 200px;">
                            <p style="color: #ffffff; margin: 12px 0 0; font-size: 13px;">Report pravidelné údržby</p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px;">
                            <h2 style="color: #1a1a1a; margin: 0 0 15px; font-size: 20px;">Dobrý den,</h2>
                            <p style="color: #555555; font-size: 15px; line-height: 1.6; margin: 0 0 25px;">
                                provedli jsme pravidelnou údržbu Vašeho webu
                                <a href="<?php echo esc_url($siteUrl); ?>" style="color: #ff817e; text-decoration: none; font-weight: 600;"><?php echo esc_html($siteName); ?></a>.
                                Zde je přehled provedených aktualizací:
                            </p>

                            <!-- Updates table -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="background: #f8f8f8; padding: 10px 15px; font-weight: 600; font-size: 13px; color: #666; border-bottom: 2px solid #eee;">Položka</td>
                                    <td style="background: #f8f8f8; padding: 10px 15px; font-weight: 600; font-size: 13px; color: #666; border-bottom: 2px solid #eee; text-align: center;">Typ</td>
                                    <td style="background: #f8f8f8; padding: 10px 15px; font-weight: 600; font-size: 13px; color: #666; border-bottom: 2px solid #eee; text-align: right;">Verze</td>
                                </tr>
                                <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td style="padding: 12px 15px; border-bottom: 1px solid #f0f0f0; font-size: 14px; color: #333;">
                                        <strong><?php echo esc_html($log->item_name); ?></strong>
                                    </td>
                                    <td style="padding: 12px 15px; border-bottom: 1px solid #f0f0f0; font-size: 12px; color: #888; text-align: center;">
                                        <?php
                                        $typeLabels = ['core' => 'jádro', 'theme' => 'šablona', 'plugin' => 'plugin'];
                                        echo esc_html($typeLabels[$log->item_type] ?? $log->item_type);
                                        ?>
                                    </td>
                                    <td style="padding: 12px 15px; border-bottom: 1px solid #f0f0f0; font-size: 14px; color: #555; text-align: right;">
                                        <?php if ($log->old_version && $log->old_version !== '?'): ?>
                                            <?php echo esc_html($log->old_version); ?> → <?php echo esc_html($log->new_version); ?>
                                        <?php else: ?>
                                            <?php echo esc_html($log->new_version); ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </table>

                            <?php if (!empty($note)): ?>
                            <!-- Note -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="background: #fff8f2; padding: 18px 20px; border-radius: 6px; border-left: 4px solid #ff817e;">
                                        <p style="margin: 0 0 5px; font-size: 13px; font-weight: 600; color: #1a1a1a;">Poznámka:</p>
                                        <p style="margin: 0; font-size: 14px; color: #555; line-height: 1.5;"><?php echo nl2br(esc_html($note)); ?></p>
                                    </td>
                                </tr>
                            </table>
                            <?php endif; ?>

                            <p style="color: #555555; font-size: 15px; line-height: 1.6; margin: 0;">
                                Vše proběhlo v pořádku a Váš web je nyní aktuální a v bezpečí.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background: #ff817e; padding: 25px 40px; text-align: center;">
                            <p style="margin: 0 0 10px; font-size: 13px; color: #ffffff; line-height: 1.5;">
                                V případě dotazů nebo požadavků se na nás neváhejte obrátit na <a href="mailto:hello@digihood.cz" style="color: #ffffff; text-decoration: underline;">hello@digihood.cz</a>
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #ffffffb3;">
                                &copy; <?php echo esc_html($year); ?> <a href="https://digihood.cz" style="color: #ffffff; text-decoration: none;">digihood.cz</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
