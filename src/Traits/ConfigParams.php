<?php

namespace CaribouFute\LocaleRoute\Traits;

use Illuminate\Support\Facades\Config;

trait ConfigParams
{
    public function locales(array $options = [])
    {
        return isset($options['locales']) ?
            $options['locales'] :
            Config::get('localeroute.locales');
    }
}
