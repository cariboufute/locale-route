<?php

namespace Tests\CaribouFute\LocaleRoute\Functional\Session;

use CaribouFute\LocaleRoute\Session\Locale as SessionLocale;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Orchestra\Testbench\TestCase;

class LocaleTest extends TestCase
{
    protected $locale;

    public function setUp(): void
    {
        parent::setUp();
        Session::start();

        $this->locale = app()->make(SessionLocale::class);
    }

    public function testSetAlsoSetsAppLocale()
    {
        $locale = 'es';
        $this->locale->set($locale);

        $this->assertSame($locale, $this->locale->get());
        $this->assertSame($locale, app()->getLocale());
    }

    public function testGetLocaleAlsoSetsAppLocaleToFallbackIfNone()
    {
        $this->locale->set(null);
        $fallbackLocale = Config::get('app.fallback_locale');

        $this->assertSame($fallbackLocale, $this->locale->get());
        $this->assertSame($fallbackLocale, app()->getLocale());
    }
}
