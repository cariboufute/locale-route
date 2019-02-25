<?php

namespace CaribouFute\LocaleRoute;

use Illuminate\Config\Repository as Config;

class LocaleConfig
{
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function locales(array $options = [])
    {
        return $this->getOptionOrConfig('locales', $options);
    }

    protected function getOptionOrConfig(string $key, array $options)
    {
        return isset($options[$key]) ?
            $options[$key] :
            $this->getConfig($key);
    }

    public function getConfig(string $key)
    {
        return $this->config->get('localeroute.' . $key);
    }

}
