<?php

namespace CaribouFute\LocaleRoute\Routing;

use Config;
use Illuminate\Translation\Translator;

class UrlLocalizer
{
    protected $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function addLocaleConfig()
    {
        return Config::get('localeroute.add_locale_to_url');
    }

    public function getRouteUrl($locale, $route, array $urls = [])
    {
        $unlocaleUrl = isset($urls[$locale]) ? $urls[$locale] : $this->translator->get('routes.' . $route, [], $locale);
        $url = $this->addLocale($locale, $unlocaleUrl);
        return $url;
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
