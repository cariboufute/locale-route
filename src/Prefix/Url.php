<?php

namespace CaribouFute\LocaleRoute\Prefix;

use CaribouFute\LocaleRoute\LocaleConfig;
use Illuminate\Translation\Translator;

class Url extends Base
{
    protected $separator = '/';
    protected $translator;

    public function __construct(LocaleConfig $localeConfig, Translator $translator)
    {
        parent::__construct($localeConfig);
        $this->translator = $translator;
    }

    public function trimUrl($url): string
    {
        return trim($url, '/ ') ?: '/';
    }

    public function rawRouteUrl($locale, $route, array $options = []): string
    {
        $unlocaleUrl = $options[$locale] ??
            $this->translator->get('routes.' . $route, [], $locale);

        return $this->trimUrl($unlocaleUrl);
    }

    public function switchLocale($locale, $url, array $options = []): string
    {
        $unlocalized = $this->removeLocale($url);
        $localized = $this->addLocale($locale, $unlocalized, $options);

        return $this->trimUrl($localized);
    }

    public function addLocale($locale, $unlocalized, $options = []): string
    {
        $localized = $this->localeConfig->addLocaleToUrl($options) ?
            parent::addLocale($locale, $unlocalized) :
            $unlocalized;

        return $this->trimUrl($localized);
    }
}
