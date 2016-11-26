<?php

namespace CaribouFute\LocaleRoute\Traits;

use Illuminate\Support\Facades\Config;

trait ConfigParams
{
    public function locales()
    {
        return Config::get('localeroute.locales');
    }

    public function getAddLocaleToUrl()
    {
        return Config::get('localeroute.add_locale_to_url');
    }
}
