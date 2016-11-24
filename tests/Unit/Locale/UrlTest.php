<?php

namespace Tests\Unit\Locale;

use CaribouFute\LocaleRoute\Locale\Url as LocaleUrl;
use Config;
use Illuminate\Translation\Translator;
use Mockery;
use Orchestra\Testbench\TestCase;

class UrlTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->translator = Mockery::mock(Translator::class);
        $this->url = new LocaleUrl($this->translator);
    }

    public function testGetRouteUrl()
    {
        //TODO
    }

    public function testSwitchLocale()
    {
        Config::shouldReceive('get')->with('localeroute.add_locale_to_url')->twice()->andReturn(true);
        Config::shouldReceive('get')->with('localeroute.locales')->once()->andReturn(['fr', 'en']);

        $url = 'en/url';
        $locale = 'fr';
        $newUrl = 'fr/url';

        $this->assertSame($newUrl, $this->url->switchLocale($locale, $url));
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
        Config::shouldReceive('get')->with('localeroute.locales')->once()->andReturn(['fr', 'en']);

        $locale = 'fr';
        $url = 'url';
        $localeUrl = $locale . '/' . $url;

        $testUrl = $this->url->removeLocale($localeUrl);

        $this->assertSame($url, $testUrl);
    }

    public function testRemoveLocaleWithConfigAddLocaleToUrlToFalse()
    {
        Config::shouldReceive('get')->with('localeroute.add_locale_to_url')->once()->andReturn(false);

        $locale = 'fr';
        $url = 'fr/url';

        $testUrl = $this->url->removeLocale($url);

        $this->assertSame($url, $testUrl);
    }
}
