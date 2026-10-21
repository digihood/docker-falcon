<?php
/**
 * Dashboard widget template
 * Variables: $lastDate, $logoUrl, $blogPosts
 */
if (!defined('ABSPATH')) exit;
?>
<style>
    .dh-widget-btn {
        display: inline-block; background: #ff817e; color: #fff !important;
        padding: 8px 20px; border-radius: 30px; text-decoration: none !important;
        font-size: 13px; font-weight: 600; transition: all 0.2s ease;
        box-shadow: 0 4px 15px rgba(255, 129, 126, 0.3);
    }
    .dh-widget-btn:hover {
        background: #e06e6b; box-shadow: 0 6px 20px rgba(255, 129, 126, 0.45);
        transform: translateY(-1px);
    }
</style>
<div style="text-align: center; padding: 10px 0;">
    <img src="<?php echo esc_url($logoUrl); ?>" alt="digihood" style="max-width: 150px; height: auto; margin-bottom: 12px;">

    <p style="font-size: 15px; color: #333; margin: 0 0 8px;">
        <strong>Váš web spravuje digitální agentura digihood</strong>
    </p>

    <?php if ($lastDate): ?>
        <p style="font-size: 13px; color: #666; margin: 0 0 12px;">
            Poslední údržba: <strong style="color: #ff817e;"><?php echo esc_html($lastDate); ?></strong>
        </p>
    <?php endif; ?>

    <p style="font-size: 13px; color: #666; margin: 0 0 4px;">
        <a href="mailto:hello@digihood.cz" style="color: #ff817e; text-decoration: none;">hello@digihood.cz</a>
    </p>
    <p style="font-size: 13px; color: #666; margin: 0 0 12px;">
        <a href="tel:+420731901533" style="color: #ff817e; text-decoration: none;">+420 731 901 533</a>
    </p>

    <a href="https://digihood.cz" target="_blank" rel="noopener" class="dh-widget-btn">
        Navštívit digihood.cz
    </a>
</div>

<?php if (!empty($blogPosts)): ?>
<div style="border-top: 1px solid #eee; margin-top: 16px; padding-top: 14px;">
    <p style="font-size: 15px; font-weight: 700; color: #333; margin: 0 0 10px; text-align: center;">
        Sledujte novinky z našeho blogu
    </p>
    <?php foreach ($blogPosts as $post): ?>
        <a href="<?php echo esc_url($post['link']); ?>" target="_blank" rel="noopener"
           style="display: flex; gap: 12px; align-items: flex-start; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #f0f0f0; text-decoration: none;">
            <?php if (!empty($post['thumbnail'])): ?>
                <img src="<?php echo esc_url($post['thumbnail']); ?>" alt=""
                     style="width: 72px; height: 48px; object-fit: cover; border-radius: 4px; flex-shrink: 0;">
            <?php endif; ?>
            <div style="min-width: 0;">
                <span style="font-size: 13px; font-weight: 600; color: #333; line-height: 1.3; display: block; margin-bottom: 3px;">
                    <?php echo esc_html($post['title']); ?>
                </span>
                <span style="font-size: 11px; color: #999;">
                    <?php if (!empty($post['author'])): ?>
                        <?php echo esc_html($post['author']); ?> &middot;
                    <?php endif; ?>
                    <?php echo esc_html($post['date']); ?>
                </span>
            </div>
        </a>
    <?php endforeach; ?>
    <div style="text-align: center; margin-top: 4px;">
        <a href="https://digihood.cz/digiblog" target="_blank" rel="noopener" class="dh-widget-btn">
            Zobrazit všechny články
        </a>
    </div>
</div>
<?php endif; ?>
