<?php

namespace CaribouFute\LocaleRoute\Prefix;

use CaribouFute\LocaleRoute\Prefix\Base;
use Config;
use Illuminate\Translation\Translator;

class Url extends Base
{
    protected $separator = '/';
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
        $unlocaleUrl = $this->getUnlocaleRouteUrl($locale, $route, $urls);
        $url = $this->addLocale($locale, $unlocaleUrl);
        return $url;
    }

    public function getUnlocaleRouteUrl($locale, $route, array $urls = [])
    {
        $unlocaleUrl = isset($urls[$locale]) ? $urls[$locale] : $this->translator->get('routes.' . $route, [], $locale);
        return $unlocaleUrl;
    }

    public function addLocale($locale, $url)
    {
        return $this->addLocaleConfig() ? parent::addLocale($locale, $url) : $url;
    }
}
