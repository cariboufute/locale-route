<?php

namespace Tests\Unit\Prefix;

use App;
use CaribouFute\LocaleRoute\Prefix\Route as PrefixRoute;
use CaribouFute\LocaleRoute\TestHelpers\EnvironmentSetUp;
use Config;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Translation\Translator;
use Mockery;
use Orchestra\Testbench\TestCase;

class RouteTest extends TestCase
{
    use EnvironmentSetUp;

    public function setUp()
    {
        parent::setUp();

        $this->route = Mockery::mock(Route::class);

        $this->url = Mockery::mock(UrlGenerator::class);
        $this->router = Mockery::mock(Router::class);
        $this->translator = Mockery::mock(Translator::class);

        $this->prefixRoute = Mockery::mock(PrefixRoute::class, [$this->url, $this->router, $this->translator])->makePartial();
    }

    public function testLocales()
    {
        $locales = 'locales';
        Config::shouldReceive('get')->with('localeroute.locales')->once()->andReturn($locales);

        $this->assertSame($locales, $this->prefixRoute->locales());
    }

    public function testLocaleRouteWithNoParamsReturnsCurrentUrl()
    {
        $locale = 'fr';
        $route = 'route';
        $localeRoute = $locale . '.' . $route;
        $localeUrl = 'fr/route_fr';

        $this->route->shouldReceive('getName')->once()->andReturn($localeRoute);

        App::shouldReceive('getLocale')->once()->andReturn($locale);
        $this->router->shouldReceive('current')->once()->andReturn($this->route);
        $this->prefixRoute->shouldReceive('switchLocale')->with($locale, $localeRoute)->once()->andReturn($localeRoute);
        $this->url->shouldReceive('route')->with($localeRoute, [], true)->once()->andReturn($localeUrl);

        $this->assertSame($localeUrl, $this->prefixRoute->localeRoute());
    }

    public function testLocaleRouteWithLocaleReturnsCurrentUrlWithLocale()
    {
        $currentLocale = 'en';
        $locale = 'fr';
        $route = 'route';
        $currentLocaleRoute = $currentLocale . '.' . $route;
        $localeRoute = $locale . '.' . $route;
        $currentUrl = 'en/route_en';
        $localeUrl = 'fr/route_fr';

        $this->route->shouldReceive('getName')->once()->andReturn($currentLocaleRoute);

        $this->router->shouldReceive('current')->once()->andReturn($this->route);
        $this->prefixRoute->shouldReceive('switchLocale')->with($locale, $currentLocaleRoute)->once()->andReturn($localeRoute);
        $this->url->shouldReceive('route')->with($localeRoute, [], true)->once()->andReturn($localeUrl);

        $this->assertSame($localeUrl, $this->prefixRoute->localeRoute($locale));
    }

    public function testLocaleRouteWithLocaleAndRoute()
    {
        $locale = 'fr';
        $route = 'route';
        $localeRoute = $locale . '.' . $route;
        $localeUrl = 'fr/route_fr';

        $this->prefixRoute->shouldReceive('switchLocale')->with($locale, $route)->once()->andReturn($localeRoute);
        $this->url->shouldReceive('route')->with($localeRoute, [], true)->once()->andReturn($localeUrl);

        $this->assertSame($localeUrl, $this->prefixRoute->localeRoute($locale, $route));
    }

    public function testSwitchLocale()
    {
        $sourceRoute = 'en.route';
        $locale = 'fr';
        $destRoute = $locale . '.route';

        $this->assertSame($destRoute, $this->prefixRoute->switchLocale($locale, $sourceRoute));
    }

    public function testRemoveLocaleKeepsSameRouteWhenNotInConfigLocales()
    {
        $route = 'es.route';

        $this->assertSame($route, $this->prefixRoute->removeLocale($route));
    }

    public function testRemoveLocaleRemovesConfigLocale()
    {
        $route = 'fr.route';

        $this->assertSame('route', $this->prefixRoute->removeLocale($route));
    }

    public function testGetLocalePrefixReturnsEmptyStringWhenNotConfigLocale()
    {
        $route = 'es.route';

        $this->assertSame('', $this->prefixRoute->prefix($route));
    }

    public function testGetLocalePrefixReturnsConfigLocale()
    {
        $route = 'en.route';

        $this->assertSame('en.', $this->prefixRoute->prefix($route));
    }

    public function testAddLocale()
    {
        $route = 'route';
        $locale = 'lc';

        $this->assertSame($locale . '.' . $route, $this->prefixRoute->addLocale($locale, $route));
    }
}
