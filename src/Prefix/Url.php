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

    public function rawRouteUrl($locale, $route, array $options = [])
    {
        $unlocaleUrl = isset($options[$locale]) ? $options[$locale] : $this->translator->get('routes.' . $route, [], $locale);
        return $unlocaleUrl;
    }

    public function addLocale($locale, $url)
    {
        return $this->getAddLocaleToUrl() ? parent::addLocale($locale, $url) : $url;
    }
}
