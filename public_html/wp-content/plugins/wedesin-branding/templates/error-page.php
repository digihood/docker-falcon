<?php
/**
 * Custom error page template
 * Variables: $title, $message, $homeUrl, $logoUrl, $backLink, $response
 */
if (!defined('ABSPATH')) exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo esc_html($title); ?></title>
    <style>
        :root {
            --dh-orange: #ff817e;
            --dh-dark: #1a1a1a;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d1500 50%, #1a1a1a 100%);
            background-size: 400% 400%;
            animation: gradient-shift 15s ease infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
        }

        @keyframes gradient-shift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .error-container {
            text-align: center;
            max-width: 480px;
            padding: 40px 30px;
        }

        .error-logo {
            width: 50px;
            height: 50px;
            margin: 0 auto 30px;
            opacity: 0.6;
        }

        .error-logo img {
            width: 100%;
            height: 100%;
        }

        .error-code {
            font-size: 72px;
            font-weight: 800;
            color: var(--dh-orange);
            line-height: 1;
            margin-bottom: 16px;
        }

        .error-title {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 12px;
            color: #ffffff;
        }

        .error-message {
            font-size: 15px;
            color: rgba(255, 255, 255, 0.6);
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .error-btn {
            display: inline-block;
            background: var(--dh-orange);
            color: #ffffff;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(255, 107, 0, 0.3);
        }

        .error-btn:hover {
            background: #e06e6b;
            box-shadow: 0 6px 16px rgba(255, 107, 0, 0.4);
            transform: translateY(-1px);
        }

        .error-back {
            display: block;
            margin-top: 16px;
            color: rgba(255, 255, 255, 0.4);
            text-decoration: none;
            font-size: 13px;
            transition: color 0.2s;
        }

        .error-back:hover {
            color: var(--dh-orange);
        }

        .error-footer {
            margin-top: 50px;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.2);
            letter-spacing: 1px;
        }

        .error-footer a {
            color: rgba(255, 255, 255, 0.3);
            text-decoration: none;
        }

        .error-footer a:hover {
            color: var(--dh-orange);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-logo">
            <img src="<?php echo esc_url($logoUrl); ?>" alt="digihood">
        </div>

        <div class="error-code"><?php echo esc_html($response); ?></div>
        <h1 class="error-title"><?php echo esc_html($title); ?></h1>

        <?php if ($message): ?>
            <p class="error-message"><?php echo wp_kses_post($message); ?></p>
        <?php endif; ?>

        <a href="<?php echo esc_url($homeUrl); ?>" class="error-btn">Zpět na hlavní stránku</a>

        <?php if ($backLink): ?>
            <a href="javascript:history.back()" class="error-back">&larr; Zpět na předchozí stránku</a>
        <?php endif; ?>

        <div class="error-footer">
            <a href="https://digihood.cz" target="_blank" rel="noopener">digihood.cz</a>
        </div>
    </div>
</body>
</html>
