<?php

namespace Tests\Unit;

use CaribouFute\LocaleRoute\LocaleConfig;
use Illuminate\Config\Repository;
use Mockery;
use Orchestra\Testbench\TestCase;

class LocaleConfigTest extends TestCase
{
    protected $config;
    protected $localeConfig;

    public function setUp()
    {
        parent::setUp();
        $this->config = Mockery::mock(Repository::class);

        $this->localeConfig = new LocaleConfig($this->config);
    }

    public function testLocalesWhenPassedOption()
    {
        $configLocales = ['en', 'js'];
        $options = ['locales' => $configLocales];

        $testLocales = $this->localeConfig->locales($options);

        $this->assertSame($configLocales, $testLocales);
    }

    public function testLocalesWhenNoPassedOption()
    {
        $configLocales = ['fr'];

        $this->config->shouldReceive('get')
            ->with('localeroute.locales')
            ->once()
            ->andReturn($configLocales);

        $testLocales = $this->localeConfig->locales();

        $this->assertSame($configLocales, $testLocales);
    }

    public function testAddLocaleToUrlWhenPassedOption()
    {
        $addLocaleToUrl = false;
        $options = ['add_locale_to_url' => $addLocaleToUrl];

        $testAddLocaleToUrl = $this->localeConfig->addLocaleToUrl($options);

        $this->assertSame($addLocaleToUrl, $testAddLocaleToUrl);
    }

    public function testAddLocaleToUrlWhenNoPassedOption()
    {
        $addLocaleToUrl = false;

        $this->config->shouldReceive('get')
            ->with('localeroute.add_locale_to_url')
            ->once()
            ->andReturn($addLocaleToUrl);

        $testAddLocaleToUrl = $this->localeConfig->addLocaleToUrl();

        $this->assertSame($addLocaleToUrl, $testAddLocaleToUrl);
    }
}
