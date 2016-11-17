<?php

namespace Tests\Unit\Session;

use CaribouFute\LocaleRoute\Session\Locale as SessionLocale;
use Config;
use Mockery;
use Orchestra\Testbench\TestCase;
use Session;

class LocaleTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->fallbackLocale = 'fr';
        $this->config = Mockery::mock('Illuminate\Contracts\Config\Repository');
        $this->config->shouldReceive('get')->with('app.fallback_locale')->andReturn($this->fallbackLocale);

        $this->locale = new SessionLocale($this->config);
    }

    public function testGet()
    {
        $locale = 'fr';
        Session::shouldReceive('get')->with('locale')->andReturn($locale);

        $this->assertSame($locale, $this->locale->get());
    }

    public function testSet()
    {
        $locale = 'es';
        Session::shouldReceive('set')->with('locale', $locale);
        Session::shouldReceive('get')->with('locale')->andReturn($locale);
        $this->locale->set($locale);

        $this->assertSame($locale, $this->locale->get());
    }

    public function testGetFallbackLocaleIfNone()
    {
        Session::shouldReceive('get')->with('locale')->andReturn(null);
        Session::shouldReceive('set')->with('locale', $this->fallbackLocale);

        $this->assertSame($this->fallbackLocale, $this->locale->get());
    }
}
