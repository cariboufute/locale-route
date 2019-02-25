<?php

namespace CaribouFute\LocaleRoute\Prefix;

use CaribouFute\LocaleRoute\LocaleConfig;
use Illuminate\Support\Str;

abstract class Base
{
    protected $localeConfig;
    protected $separator;

    public function __construct(LocaleConfig $localeConfig)
    {
        $this->localeConfig = $localeConfig;
    }

    public function switchLocale($locale, $string)
    {
        $unlocalized = $this->removeLocale($string);
        $localized = $this->addLocale($locale, $unlocalized);

        return $localized;
    }

    public function removeLocale($string)
    {
        $prefix = $this->prefix($string);
        $unlocalized = str_replace($prefix, '', $string);

        return $unlocalized;
    }

    public function prefix($string)
    {
        foreach ($this->localeConfig->locales() as $locale) {
            $prefix = $locale . $this->separator;

            if (Str::startsWith($string, $prefix)) {
                return $prefix;
            }
        }

        return '';
    }

    public function addLocale($locale, $string)
    {
        return $locale . $this->separator . $string;
    }

    public function locale($string)
    {
        return rtrim($this->prefix($string), $this->separator);
    }
}
