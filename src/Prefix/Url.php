<?php

namespace CaribouFute\LocaleRoute\Prefix;

use CaribouFute\LocaleRoute\Prefix\Base;
use Illuminate\Translation\Translator;

class Url extends Base
{
    protected $separator = '/';
    protected $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
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
}
