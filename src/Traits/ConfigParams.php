<?php

namespace CaribouFute\LocaleRoute\Traits;

use Illuminate\Support\Facades\Config;

trait ConfigParams
{
    public function locales(array $options = [])
    {
        return isset($options['locales']) ? $options['locales'] : Config::get('localeroute.locales');
    }

    public function getAddLocaleToUrl(array $options = [])
    {
        return isset($options['add_locale_to_url']) ? $options['add_locale_to_url'] : Config::get('localeroute.add_locale_to_url');
    }
}
