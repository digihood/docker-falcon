<?php

declare(strict_types=1);

namespace Digihood\Branding\Modules\Email;

class Template
{
    public static function render(array $logs, string $note = ''): string
    {
        $siteName = get_bloginfo('name');
        $siteUrl = get_home_url();
        $year = date('Y');
        $templatePath = Localization::getTemplatePath();

        ob_start();
        include $templatePath;
        return ob_get_clean();
    }
}
