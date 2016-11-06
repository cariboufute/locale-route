<?php

namespace Tests\Unit\Routing;

use App;
use CaribouFute\LocaleRoute\Routing\RouteLocalizer;
use Config;
use Illuminate\Routing\Router;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Translation\Translator;
use Mockery;
use Orchestra\Testbench\TestCase;

class RouteLocalizerTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $this->locales = ['fr', 'en'];
        $this->addLocaleOption = true;

        $app['config']->set('localeroute.locales', $this->locales);
        $app['config']->set('localeroute.add_locale_to_url', $this->addLocaleOption);
    }

    public function setUp()
    {
        parent::setUp();

        $this->illuminateUrl = Mockery::mock(UrlGenerator::class);
        $this->router = Mockery::mock(Router::class);
        $this->translator = Mockery::mock(Translator::class);

        $this->localizer = new RouteLocalizer($this->illuminateUrl, $this->router, $this->translator);
    }

    public function testLocales()
    {
        $locales = 'locales';
        Config::shouldReceive('get')->with('localeroute.locales')->once()->andReturn($locales);

        $this->assertSame($locales, $this->localizer->locales());
    }

    public function testRemoveLocaleKeepsSameRouteWhenNotInConfigLocales()
    {
        $route = 'es.route';

        $this->assertSame($route, $this->localizer->removeLocale($route));
    }

    public function testRemoveLocaleRemovesConfigLocale()
    {
        $route = 'fr.route';

        $this->assertSame('route', $this->localizer->removeLocale($route));
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

        $this->assertSame($localeUrl, $this->localizer->localeRoute());
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

        $this->assertSame($localeUrl, $this->localizer->localeRoute($locale));
    }
}
