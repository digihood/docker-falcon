<?php

declare(strict_types=1);

namespace Digihood\Branding\Modules\Email;

class Localization
{
    private const CS_LOCALES = ['cs_CZ', 'sk_SK'];

    public static function getLocale(): string
    {
        return in_array(get_locale(), self::CS_LOCALES, true) ? 'cs' : 'en';
    }

    public static function isCzech(): bool
    {
        return self::getLocale() === 'cs';
    }

    public static function getSubject(): string
    {
        $siteName = get_bloginfo('name');

        return self::isCzech()
            ? "Report údržby webu — {$siteName}"
            : "Website maintenance report — {$siteName}";
    }

    public static function getTemplatePath(): string
    {
        $locale = self::getLocale();
        return D1G1_PATHS_digibrand . "templates/email/report-{$locale}.php";
    }
}
