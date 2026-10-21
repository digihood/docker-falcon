<?php
/**
 * Plugins welcome page template — digihood
 *
 * Variables: $pluginUrl, $plugins, $isCzech
 */
if (!defined('ABSPATH')) exit;
?>
<div class="dh-page-wrap">
    <div class="dh-page-container">

        <!-- Header -->
        <div class="dh-page-header">
            <img src="<?php echo esc_url($pluginUrl . 'assets/img/d1g1-logo-cz.png'); ?>" alt="digihood">
            <p class="dh-page-subtitle">
                <?php echo $isCzech
                    ? 'Administrace spravovaná agenturou digihood'
                    : 'Administration managed by digihood agency'; ?>
            </p>
        </div>

        <!-- Welcome card -->
        <div class="dh-glass dh-glass-hero">
            <?php if ($isCzech): ?>
                <h2>Vítejte v administraci vašeho webu</h2>
                <p>
                    Váš web je pod profesionální správou agentury <strong>digihood</strong>.
                    Staráme se o pravidelné aktualizace, bezpečnost a výkon — abyste se mohli soustředit na to, co je pro vás důležité.
                </p>
                <p>
                    Potřebujete novou funkcionalitu, úpravu designu nebo pomoc s marketingem?
                    Neváhejte se na nás obrátit — rádi vám pomůžeme váš web posunout dál.
                </p>
            <?php else: ?>
                <h2>Welcome to your website administration</h2>
                <p>
                    Your website is under professional management by <strong>digihood</strong> agency.
                    We handle regular updates, security and performance — so you can focus on what matters most.
                </p>
                <p>
                    Need new functionality, design changes or marketing support?
                    Don't hesitate to reach out — we'll be happy to help take your website further.
                </p>
            <?php endif; ?>
        </div>

        <!-- Contact + Services grid -->
        <div class="dh-glass-grid">
            <!-- Contact -->
            <div class="dh-glass">
                <h3><span class="dashicons dashicons-email-alt"></span> <?php echo $isCzech ? 'Kontakt' : 'Contact'; ?></h3>
                <ul>
                    <li><span class="dashicons dashicons-email"></span> <a href="mailto:hello@digihood.cz">hello@digihood.cz</a></li>
                    <li><span class="dashicons dashicons-phone"></span> +420 731 901 533</li>
                    <li><span class="dashicons dashicons-admin-site-alt3"></span> <a href="https://digihood.cz" target="_blank" rel="noopener">digihood.cz</a></li>
                </ul>
            </div>

            <!-- Services -->
            <div class="dh-glass">
                <h3><span class="dashicons dashicons-admin-tools"></span> <?php echo $isCzech ? 'Naše služby' : 'Our services'; ?></h3>
                <ul>
                    <li><span class="dashicons dashicons-laptop"></span> <a href="https://digihood.cz/tvorba-webovych-stranek" target="_blank" rel="noopener"><?php echo $isCzech ? 'Webový vývoj na míru' : 'Custom web development'; ?></a></li>
                    <li><span class="dashicons dashicons-chart-bar"></span> <a href="https://digihood.cz/online-marketing" target="_blank" rel="noopener"><?php echo $isCzech ? 'SEO & online marketing' : 'SEO & online marketing'; ?></a></li>
                    <li><span class="dashicons dashicons-shield"></span> <a href="https://digihood.cz/tvorba-webovych-stranek/sprava-webovych-stranek" target="_blank" rel="noopener"><?php echo $isCzech ? 'Správa a údržba webů' : 'Website management & maintenance'; ?></a></li>
                    <li><span class="dashicons dashicons-art"></span> <a href="https://digihood.cz/tvorba-webovych-stranek/design-webovych-stranek" target="_blank" rel="noopener"><?php echo $isCzech ? 'Grafický design & branding' : 'Graphic design & branding'; ?></a></li>
                    <li><span class="dashicons dashicons-admin-generic"></span> <a href="https://digihood.cz/vyvoj-aplikaci" target="_blank" rel="noopener"><?php echo $isCzech ? 'Vlastní řešení & automatizace' : 'Custom solutions & automation'; ?></a></li>
                </ul>
            </div>
        </div>

        <!-- Installed digihood plugins -->
        <?php if (!empty($plugins)): ?>
        <div class="dh-glass">
            <h3><span class="dashicons dashicons-admin-plugins"></span> <?php echo $isCzech ? 'Nainstalované digihood pluginy' : 'Installed digihood plugins'; ?></h3>
            <table class="dh-plugin-list">
                <thead>
                    <tr>
                        <th><?php echo $isCzech ? 'Plugin' : 'Plugin'; ?></th>
                        <th><?php echo $isCzech ? 'Verze' : 'Version'; ?></th>
                        <th><?php echo $isCzech ? 'Stav' : 'Status'; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($plugins as $plugin): ?>
                    <tr>
                        <td><strong><?php echo esc_html($plugin['name']); ?></strong></td>
                        <td><?php echo esc_html($plugin['version']); ?></td>
                        <td>
                            <?php if ($plugin['active']): ?>
                                <span class="dh-plugin-active"><?php echo $isCzech ? '✓ Aktivní' : '✓ Active'; ?></span>
                            <?php else: ?>
                                <span class="dh-plugin-inactive"><?php echo $isCzech ? 'Neaktivní' : 'Inactive'; ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

    </div>
</div>
