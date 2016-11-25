<?php

namespace CaribouFute\LocaleRoute\Prefix;

use Config;
use Illuminate\Translation\Translator;

class Url
{
    protected $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function getRouteUrl($locale, $route, array $urls = [])
    {
        $unlocaleUrl = $this->getUnlocaleRouteUrl($locale, $route, $urls);
        $url = $this->addLocale($locale, $unlocaleUrl);
        return $url;
    }

    public function getUnlocaleRouteUrl($locale, $route, array $urls = [])
    {
        $unlocaleUrl = isset($urls[$locale]) ? $urls[$locale] : $this->translator->get('routes.' . $route, [], $locale);
        return $unlocaleUrl;
    }

    public function switchLocale($locale, $url)
    {
        $unlocaleUrl = $this->removeLocale($url);
        $localeUrl = $this->addLocale($locale, $unlocaleUrl);

        return $localeUrl;
    }

    public function addLocale($locale, $url)
    {
        return $this->addLocaleConfig() ? $locale . '/' . $url : $url;
    }

    public function removeLocale($url)
    {
        if (!$this->addLocaleConfig()) {
            return $url;
        }

        $localePrefix = $this->getLocalePrefix($url);
        $unlocaleRoute = str_replace($localePrefix, '', $url);

        return $unlocaleRoute;
    }

    public function getLocalePrefix($url)
    {
        foreach ($this->locales() as $locale) {
            $localePrefix = $locale . '/';

            if (strpos($url, $localePrefix) === 0) {
                return $localePrefix;
            }
        }

        return '';
    }

    public function addLocaleConfig()
    {
        return Config::get('localeroute.add_locale_to_url');
    }

    public function locales()
    {
        return Config::get('localeroute.locales');
    }
}
