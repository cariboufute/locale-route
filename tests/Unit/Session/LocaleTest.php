<?php

namespace Tests\CaribouFute\LocaleRoute\Unit\Session;

use CaribouFute\LocaleRoute\Session\Locale as SessionLocale;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Foundation\Application;
use Illuminate\Session\Store;
use Mockery;
use PHPUnit\Framework\TestCase;

class LocaleTest extends TestCase
{
    protected $fallbackLocale;
    protected $store;
    protected $config;
    protected $app;
    protected $sessionLocale;

    public function setUp(): void
    {
        parent::setUp();

        $this->fallbackLocale = 'fr';
        $this->store = Mockery::mock(Store::class);
        $this->config = $this->getConfig();
        $this->app = $this->getApp();

        $this->sessionLocale = new SessionLocale(
            $this->store,
            $this->config,
            $this->app
        );
    }

    protected function getConfig(): Config
    {
        $config = Mockery::mock(Config::class);
        $config->shouldReceive('get')->with('app.fallback_locale')->andReturn($this->fallbackLocale);

        return $config;
    }

    protected function getApp(): Application
    {
        $app = Mockery::mock(Application::class);
        $app->shouldReceive('flush');

        return $app;
    }

    public function testGet()
    {
        $locale = 'fr';
        $this->store->shouldReceive('get')->with('locale')->andReturn($locale);

        $this->assertSame($locale, $this->sessionLocale->get());
    }

    public function testSet()
    {
        $locale = 'es';
        $this->store->shouldReceive('put')->with('locale', $locale);
        $this->store->shouldReceive('get')->with('locale')->andReturn($locale);
        $this->app->shouldReceive('setLocale')->with($locale);

        $this->sessionLocale->set($locale);

        $this->assertSame($locale, $this->sessionLocale->get());
    }

    public function testGetFallbackLocaleIfNone()
    {
        $this->store->shouldReceive('get')->with('locale')->andReturn(null);
        $this->store->shouldReceive('put')->with('locale', $this->fallbackLocale);
        $this->app->shouldReceive('setLocale')->with($this->fallbackLocale);

        $this->assertSame($this->fallbackLocale, $this->sessionLocale->get());
    }
}
