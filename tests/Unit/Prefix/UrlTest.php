<?php

namespace Tests\Unit\Locale;

use CaribouFute\LocaleRoute\Prefix\Url as PrefixUrl;
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
        $this->url = new PrefixUrl($this->translator);
    }

    public function testLocale()
    {
        Config::shouldReceive('get')->with('localeroute.locales')->twice()->andReturn(['fr', 'en']);
        $localeUrl = 'fr/test';
        $noLocaleUrl = 'test';

        $this->assertSame('fr', $this->url->locale($localeUrl));
        $this->assertSame('', $this->url->locale($noLocaleUrl));
    }

    public function testSwitchLocale()
    {
        Config::shouldReceive('get')->with('localeroute.add_locale_to_url')->once()->andReturn(true);
        Config::shouldReceive('get')->with('localeroute.locales')->once()->andReturn(['fr', 'en']);

        $url = 'en/url';
        $locale = 'fr';
        $newUrl = 'fr/url';

        $this->assertSame($newUrl, $this->url->switchLocale($locale, $url));
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

    public function testRemoveLocale()
    {
        Config::shouldReceive('get')->with('localeroute.locales')->once()->andReturn(['fr', 'en']);

        $locale = 'fr';
        $url = 'url';
        $localeUrl = $locale . '/' . $url;

        $testUrl = $this->url->removeLocale($localeUrl);

        $this->assertSame($url, $testUrl);
    }
}
