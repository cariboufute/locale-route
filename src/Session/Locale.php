<?php

namespace CaribouFute\LocaleRoute\Session;

use CaribouFute\LocaleRoute\Session\Base;
use Illuminate\Contracts\Config\Repository as Config;

class Locale extends Base
{
    protected $key = 'locale';
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function set($value)
    {
        parent::set($value);
        app()->setLocale($value);
    }

    protected function getDefault()
    {
        return $this->config->get('app.fallback_locale');
    }
}
