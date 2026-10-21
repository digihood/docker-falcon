<?php

declare(strict_types=1);

namespace Digihood\Branding\Modules\Branding;

class AdminFooter
{
    public function __construct()
    {
        add_filter('admin_footer_text', [$this, 'footerText'], 9999);
    }

    public function footerText(mixed $text): string
    {
        if (self::isCzech()) {
            return '<span id="footer-thankyou">Spravováno s &hearts; agenturou <a href="https://digihood.cz" target="_blank" rel="noopener">digihood</a>.</span>';
        }

        return '<span id="footer-thankyou">Managed with &hearts; by <a href="https://digihood.cz/en/" target="_blank" rel="noopener">digihood</a>. Questions? <a href="mailto:hello@digihood.cz">hello@digihood.cz</a></span>';
    }

    private static function isCzech(): bool
    {
        return in_array(get_locale(), ['cs_CZ', 'sk_SK'], true);
    }
}
