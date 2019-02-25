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

    public function testLocalesWhenLocalesOption()
    {
        $localeConfig = ['en', 'js'];
        $options = ['locales' => $localeConfig];

        $testLocales = $this->localeConfig->locales($options);

        $this->assertSame($localeConfig, $testLocales);
    }

    public function testLocalesWhenNoLocalesOption()
    {
        $configLocales = ['fr'];
        $this->options = [];
        $this->config->shouldReceive('get')
            ->with('localeroute.locales')
            ->once()
            ->andReturn($configLocales);

        $testLocales = $this->localeConfig->locales();

        $this->assertSame($configLocales, $testLocales);
    }
}
