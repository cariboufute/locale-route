<?php

namespace CaribouFute\LocaleRoute\Session;

use CaribouFute\LocaleRoute\Session\Base;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Foundation\Application;
use Illuminate\Session\Store;

class Locale extends Base
{
    protected $key = 'locale';
    protected $config;
    protected $app;

    public function __construct(Store $store, Config $config, Application $app)
    {
        parent::__construct($store);
        $this->config = $config;
        $this->app = $app;
    }

    public function set($value): void
    {
        parent::set($value);
        $this->app->setLocale($value);
    }

    protected function getDefault()
    {
        return $this->config->get('app.fallback_locale');
    }
}
