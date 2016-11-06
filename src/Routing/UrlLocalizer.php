<?php

namespace CaribouFute\LocaleRoute\Routing;

use Config;

class UrlLocalizer
{
    public function addLocaleConfig()
    {
        return Config::get('localeroute.add_locale_to_url');
    }

    public function addLocale($locale, $url)
    {
        return $this->addLocaleConfig() ? $locale . '/' . $url : $url;
    }

    public function removeLocale($locale, $url)
    {
        return $this->addLocaleConfig() ? str_replace($locale . '/', '', $url) : $url;
    }
}
