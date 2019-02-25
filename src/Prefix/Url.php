<?php

namespace CaribouFute\LocaleRoute\Prefix;

use CaribouFute\LocaleRoute\Locales;
use Illuminate\Translation\Translator;
use Illuminate\Config\Repository as Config;

class Url extends Base
{
    protected $separator = '/';
    protected $translator;
    protected $config;

    public function __construct(Locales $locales, Translator $translator, Config $config)
    {
        parent::__construct($locales);
        $this->translator = $translator;
        $this->config = $config;
    }

    public function trimUrl($url)
    {
        return trim($url, '/ ') ?: '/';
    }

    public function rawRouteUrl($locale, $route, array $options = [])
    {
        $unlocaleUrl = isset($options[$locale]) ?
            $options[$locale] :
            $this->translator->get('routes.' . $route, [], $locale);

        return $this->trimUrl($unlocaleUrl);
    }

    public function switchLocale($locale, $url, array $options = [])
    {
        $unlocalized = $this->removeLocale($url);
        $localized = $this->addLocale($locale, $unlocalized, $options);

        return $this->trimUrl($localized);
    }

    public function addLocale($locale, $unlocalized, $options = [])
    {
        $localized = $this->getAddLocaleToUrl($options) ?
            parent::addLocale($locale, $unlocalized) :
            $unlocalized;

        return $this->trimUrl($localized);
    }

    public function getAddLocaleToUrl(array $options = [])
    {
        return isset($options['add_locale_to_url']) ?
            $options['add_locale_to_url'] :
            $this->config->get('localeroute.add_locale_to_url');
    }
}
