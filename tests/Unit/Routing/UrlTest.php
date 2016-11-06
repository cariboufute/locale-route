<?php

namespace Tests\Unit\Routing;

use App;
use CaribouFute\LocaleRoute\Routing\Url;
use Config;
use Illuminate\Routing\Router;
use Illuminate\Routing\UrlGenerator;
use Mockery;
use TestCase;

class UrlTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->illuminateUrl = Mockery::mock(UrlGenerator::class);
        $this->router = Mockery::mock(Router::class);
        $this->url = new Url($this->illuminateUrl, $this->router);
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

    public function testLocaleRouteWithNoParamsReturnsCurrentUrl()
    {
        $locale = 'fr';
        $route = 'route';
        $localeRoute = $locale . '.' . $route;
        $localeUrl = 'fr/route_fr';

        App::shouldReceive('getLocale')->once()->andReturn($locale);
        $this->router->shouldReceive('currentRouteName')->once()->andReturn($localeRoute);
        $this->illuminateUrl->shouldReceive('route')->with('fr.route', [], true)->once()->andReturn($localeUrl);

        $this->assertSame($localeUrl, $this->url->localeRoute());
    }

    public function testLocaleRouteWithLocaleReturnsCurrentUrlWithLocale()
    {
        $currentLocale = 'en';
        $locale = 'fr';
        $route = 'route';
        $currentLocaleRoute = $currentLocale . '.' . $route;
        $currentUrl = 'en/route_en';
        $localeUrl = 'fr/route_fr';

        $this->router->shouldReceive('currentRouteName')->once()->andReturn($currentLocaleRoute);
        $this->illuminateUrl->shouldReceive('route')->with('fr.route', [], true)->once()->andReturn($localeUrl);

        $this->assertSame($localeUrl, $this->url->localeRoute($locale));
    }
}
