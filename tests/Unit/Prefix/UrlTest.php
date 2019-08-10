<?php

namespace Tests\Unit\Prefix;

use CaribouFute\LocaleRoute\LocaleConfig;
use CaribouFute\LocaleRoute\Prefix\Url as PrefixUrl;
use Illuminate\Config\Repository as Config;
use Illuminate\Translation\Translator;
use Mockery;
use Orchestra\Testbench\TestCase;

class UrlTest extends TestCase
{
    protected $localeConfig;

    public function setUp(): void
    {
        parent::setUp();

        $this->localeConfig = Mockery::mock(LocaleConfig::class);
        $this->localeConfig
            ->shouldReceive('locales')
            ->andReturn(['fr', 'en']);

        $this->translator = Mockery::mock(Translator::class);
        $this->config = Mockery::mock(Config::class);

        $this->url = new PrefixUrl($this->localeConfig, $this->translator, $this->config);
    }

    public function testLocale()
    {
        $localeUrl = 'fr/test';
        $noLocaleUrl = 'test';

        $this->assertSame('fr', $this->url->locale($localeUrl));
        $this->assertSame('', $this->url->locale($noLocaleUrl));
    }

    public function testSwitchLocale()
    {
        $this->localeConfig->shouldReceive('addLocaleToUrl')->once()->andReturn(true);

        $url = 'en/url';
        $locale = 'fr';
        $newUrl = 'fr/url';

        $this->assertSame($newUrl, $this->url->switchLocale($locale, $url));
    }

    public function testAddLocaleWithConfigAddLocaleToUrlToTrue()
    {
        $this->localeConfig->shouldReceive('addLocaleToUrl')->once()->andReturn(true);

        $locale = 'fr';
        $url = 'url';
        $localeUrl = $locale . '/' . $url;

        $testUrl = $this->url->addLocale($locale, $url);

        $this->assertSame($localeUrl, $testUrl);
    }

    public function testAddLocaleWithConfigAddLocaleToUrlToFalse()
    {
        $this->localeConfig->shouldReceive('addLocaleToUrl')->once()->andReturn(false);

        $locale = 'fr';
        $url = 'url';

        $testUrl = $this->url->addLocale($locale, $url);

        $this->assertSame($url, $testUrl);
    }

    public function testRemoveLocale()
    {
        $locale = 'fr';
        $url = 'url';
        $localeUrl = $locale . '/' . $url;

        $testUrl = $this->url->removeLocale($localeUrl);

        $this->assertSame($url, $testUrl);
    }
}
