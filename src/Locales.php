<?php

namespace CaribouFute\LocaleRoute;

use Illuminate\Config\Repository as Config;

class Locales
{
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function get(array $options = [])
    {
        return isset($options['locales']) ?
            $options['locales'] :
            $this->getConfig();
    }

    public function getConfig()
    {
        return $this->config->get('localeroute.locales');
    }

}
