<?php

namespace Tests\Unit;

use CaribouFute\LocaleRoute\Locales;
use Illuminate\Config\Repository;
use Mockery;
use Orchestra\Testbench\TestCase;

class LocalesTest extends TestCase
{
    protected $config;
    protected $locales;

    public function setUp()
    {
        parent::setUp();
        $this->config = Mockery::mock(Repository::class);

        $this->locales = new Locales($this->config);
    }

    public function testGetWhenLocalesOption()
    {
        $locales = ['en', 'js'];
        $options = ['locales' => $locales];

        $testLocales = $this->locales->get($options);

        $this->assertSame($locales, $testLocales);
    }

    public function testGetWhenNoLocalesOption()
    {
        $configLocales = ['fr'];
        $this->options = [];
        $this->config->shouldReceive('get')
            ->with('localeroute.locales')
            ->once()
            ->andReturn($configLocales);

        $testLocales = $this->locales->get();

        $this->assertSame($configLocales, $testLocales);
    }
}
