<?php

namespace CaribouFute\LocaleRoute\Prefix;

use CaribouFute\LocaleRoute\Traits\ConfigParams;

abstract class Base
{
    use ConfigParams;

    protected $separator;

    public function switchLocale($locale, $string)
    {
        $unlocalized = $this->removeLocale($string);
        $localized = $this->addLocale($locale, $unlocalized);

        return $localized;
    }

    public function removeLocale($string)
    {
        $prefix = $this->prefix($string);
        $unlocale = str_replace($prefix, '', $string);

        return $unlocale;
    }

    public function prefix($string)
    {
        foreach ($this->locales() as $locale) {
            $prefix = $locale . $this->separator;

            if (starts_with($string, $prefix)) {
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
