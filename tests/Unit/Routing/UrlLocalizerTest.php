<?php

namespace Tests\Unit\Routing;

use CaribouFute\LocaleRoute\Routing\UrlLocalizer;
use Config;
use Orchestra\Testbench\TestCase;

class UrlLocalizerTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('localeroute.add_locale_to_url', true);
    }

    public function setUp()
    {
        parent::setUp();
        $this->url = new UrlLocalizer;
    }

    public function testAddLocaleConfig()
    {
        $config = true;
        Config::shouldReceive('get')->with('localeroute.add_locale_to_url')->once()->andReturn($config);

        $this->assertSame($config, $this->url->addLocaleConfig());
    }

    public function testAddLocaleWithConfigAddLocaleToUrlToTrue()
    {
        Config::shouldReceive('get')->with('localeroute.add_locale_to_url')->once()->andReturn(true);

        $locale = 'fr';
        $url = 'url';
        $localeUrl = $locale . '/' . $url;

        $testUrl = $this->url->addLocale($locale, $url);

        $this->assertSame($localeUrl, $testUrl);
    }

    public function testAddLocaleWithConfigAddLocaleToUrlToFalse()
    {
        Config::shouldReceive('get')->with('localeroute.add_locale_to_url')->once()->andReturn(false);

        $locale = 'fr';
        $url = 'url';

        $testUrl = $this->url->addLocale($locale, $url);

        $this->assertSame($url, $testUrl);
    }

    public function testRemoveLocaleWithConfigAddLocaleToUrlToTrue()
    {
        Config::shouldReceive('get')->with('localeroute.add_locale_to_url')->once()->andReturn(true);

        $locale = 'fr';
        $url = 'url';
        $localeUrl = $locale . '/' . $url;

        $testUrl = $this->url->removeLocale($locale, $localeUrl);

        $this->assertSame($url, $testUrl);
    }

    public function testRemoveLocaleWithConfigAddLocaleToUrlToFalse()
    {
        Config::shouldReceive('get')->with('localeroute.add_locale_to_url')->once()->andReturn(false);

        $locale = 'fr';
        $url = 'fr/url';

        $testUrl = $this->url->removeLocale($locale, $url);

        $this->assertSame($url, $testUrl);
    }
}
