<?php

namespace Tests\Functional\Session;

use CaribouFute\LocaleRoute\Session\Locale as SessionLocale;
use Config;
use Session;
use TestCase;

class LocaleTest extends TestCase
{
    public function setUp()
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
