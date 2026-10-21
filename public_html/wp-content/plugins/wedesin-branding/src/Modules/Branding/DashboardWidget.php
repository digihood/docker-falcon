<?php

declare(strict_types=1);

namespace Digihood\Branding\Modules\Branding;

use Digihood\Branding\Database\EmailHistoryTable;

class DashboardWidget
{
    public function __construct()
    {
        // High priority (999) to run after all other widgets are registered
        add_action('wp_dashboard_setup', [$this, 'register'], 999);
    }

    public function register(): void
    {
        wp_add_dashboard_widget(
            'digihood_welcome',
            'digihood',
            [$this, 'render']
        );

        // Move to top of dashboard — reorder meta_boxes
        global $wp_meta_boxes;
        foreach (['normal', 'side'] as $context) {
            foreach (['high', 'core', 'default', 'low'] as $priority) {
                if (isset($wp_meta_boxes['dashboard'][$context][$priority]['digihood_welcome'])) {
                    $widget = $wp_meta_boxes['dashboard'][$context][$priority]['digihood_welcome'];
                    unset($wp_meta_boxes['dashboard'][$context][$priority]['digihood_welcome']);
                }
            }
        }
        if (isset($widget)) {
            $existing = $wp_meta_boxes['dashboard']['normal']['high'] ?? [];
            $wp_meta_boxes['dashboard']['normal']['high'] = array_merge(
                ['digihood_welcome' => $widget],
                $existing
            );
        }

        // Force widget to top for users who haven't manually reordered
        add_filter('get_user_option_meta-box-order_dashboard', [$this, 'forceOrder']);
    }

    public function forceOrder($order): mixed
    {
        if (!is_array($order)) {
            return $order;
        }

        // Ensure digihood_welcome is first in the normal column
        foreach ($order as $context => $widgets) {
            if (!is_string($widgets)) continue;
            $list = explode(',', $widgets);
            $list = array_filter($list, fn($w) => $w !== 'digihood_welcome');
            if ($context === 'normal') {
                array_unshift($list, 'digihood_welcome');
            }
            $order[$context] = implode(',', $list);
        }

        return $order;
    }

    public function render(): void
    {
        $lastReport = EmailHistoryTable::getLastReport();
        $lastDate = $lastReport
            ? wp_date('j. n. Y', strtotime($lastReport->sent_at))
            : null;
        $logoUrl = D1G1_URL_digibrand . 'assets/img/d1g1-logo-dark-blue.png';
        $blogPosts = self::getBlogPosts();

        include D1G1_PATHS_digibrand . 'templates/dashboard-widget.php';
    }

    private static function getBlogPosts(): array
    {
        $cacheKey = 'digihood_blog_posts';
        $cached = get_transient($cacheKey);

        if ($cached !== false) {
            return $cached;
        }

        include_once ABSPATH . WPINC . '/feed.php';

        $rss = fetch_feed('https://digihood.cz/feed/');

        if (is_wp_error($rss)) {
            return [];
        }

        $items = $rss->get_items(0, 3);
        $result = [];

        foreach ($items as $item) {
            $author = $item->get_author();
            $thumbnail = '';

            // Extract first image from content
            $content = $item->get_content();
            if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/', $content, $matches)) {
                $thumbnail = $matches[1];
            }

            $result[] = [
                'title' => $item->get_title(),
                'link' => $item->get_permalink(),
                'date' => wp_date('j. n. Y', $item->get_date('U')),
                'author' => $author ? $author->get_name() : '',
                'thumbnail' => $thumbnail,
            ];
        }

        set_transient($cacheKey, $result, 12 * HOUR_IN_SECONDS);

        return $result;
    }
}
