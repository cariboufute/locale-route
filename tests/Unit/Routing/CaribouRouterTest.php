<?php

namespace Tests\Unit\Routing;

use CaribouFute\LocaleRoute\Locale\Route as LocaleRoute;
use CaribouFute\LocaleRoute\Locale\Url;
use CaribouFute\LocaleRoute\Routing\CaribouRouter;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router as IlluminateRouter;
use Mockery;
use Orchestra\Testbench\TestCase;

class ClassTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->illuminateRouter = Mockery::mock(IlluminateRouter::class);
        $this->url = Mockery::mock(Url::class);
        $this->route = Mockery::mock(LocaleRoute::class);
        $this->router = Mockery::mock(CaribouRouter::class, [$this->illuminateRouter, $this->url, $this->route])->makePartial();
    }

    public function testAddLocaleDoesNothingWhenNoLocale()
    {
        $route = Mockery::mock(Route::class);
        $this->router->shouldReceive('getActionLocale')->with($route)->once()->andReturn(null);

        $testRoute = $this->router->addLocale($route);

        $this->assertSame($route, $testRoute);
    }

    public function testAddLocaleChangesRouteAndUrl()
    {
        $locale = 'fr';
        $uri = 'uri';
        $newUri = $locale . '/' . $uri;

        $name = 'route';
        $newName = $locale . '.' . $name;

        $action = ['locale' => $locale, 'as' => $name, 'uses' => 'Controller@action'];

        $route = Mockery::mock(Route::class);

        $this->router->shouldReceive('getActionLocale')->with($route)->once()->andReturn($locale);
        $this->router->shouldReceive('switchRouteLocale')->with($locale, $route)->once()->andReturn($route);
        $this->router->shouldReceive('switchUrlLocale')->with($locale, $route)->once()->andReturn($route);

        $testRoute = $this->router->addLocale($route);

        $this->assertSame($route, $testRoute);

    }

    public function testSwitchRouteLocaleDoesNothingWhenNoName()
    {
        $route = Mockery::mock(Route::class);
        $route->shouldReceive('getName')->once()->andReturn(null);

        $testRoute = $this->router->switchRouteLocale('fr', $route);

        $this->assertSame($route, $testRoute);
    }

    public function testSwitchRouteLocaleChangesRouteName()
    {
        $locale = 'fr';
        $name = 'route';
        $newName = $locale . '.' . $name;
        $route = Mockery::mock(Route::class);
        $route->shouldReceive('getName')->once()->andReturn($name);

        $this->route->shouldReceive('switchLocale')->with($locale, $name)->once()->andReturn($newName);
        $route->shouldReceive('getAction')->once()->andReturn([]);
        $route->shouldReceive('setAction')->with(['as' => $newName])->once();

        $testRoute = $this->router->switchRouteLocale($locale, $route);

        $this->assertSame($route, $testRoute);
    }

    public function testSwitchUrlLocale()
    {
        $locale = 'fr';
        $uri = 'uri';
        $newUri = $locale . '/' . $uri;

        $route = Mockery::mock(Route::class);
        $route->shouldReceive('uri')->once()->andReturn($uri);
        $this->url->shouldReceive('switchLocale')->with($locale, $uri)->once()->andReturn($newUri);
        $route->shouldReceive('setUri')->with($locale . '/' . $uri)->once();

        $testRoute = $this->router->switchUrlLocale($locale, $route);

        $this->assertSame($route, $testRoute);
    }

    public function testGetActionLocalesReturnNullWhenNoLocaleKey()
    {
        $action = ['as' => 'route', 'uses' => 'Controller@action'];

        $route = Mockery::mock(Route::class);
        $route->shouldReceive('getAction')->once()->andReturn($action);

        $this->assertNull($this->router->getActionLocales($route));
    }

    public function testGetActionLocalesReturnsLocaleKey()
    {
        $locales = ['fr', 'en'];
        $action = ['locale' => $locales, 'as' => 'route', 'uses' => 'Controller@action'];

        $route = Mockery::mock(Route::class);
        $route->shouldReceive('getAction')->once()->andReturn($action);

        $this->assertSame($locales, $this->router->getActionLocales($route));
    }

    public function testGetActionLocaleReturnNullWhenNoLocaleKey()
    {
        $action = ['as' => 'route', 'uses' => 'Controller@action'];

        $route = Mockery::mock(Route::class);
        $route->shouldReceive('getAction')->once()->andReturn($action);

        $this->assertNull($this->router->getActionLocale($route));
    }

    public function testGetActionLocaleReturnLocaleKeyWhenString()
    {
        $locale = 'fr';
        $action = ['locale' => $locale, 'as' => 'route', 'uses' => 'Controller@action'];

        $route = Mockery::mock(Route::class);
        $route->shouldReceive('getAction')->once()->andReturn($action);

        $this->assertSame($locale, $this->router->getActionLocale($route));
    }

    public function testGetActionLocaleReturnLastLocaleWhenArray()
    {
        $locale = 'fr';
        $locales = ['en', $locale];
        $action = ['locale' => $locales, 'as' => 'route', 'uses' => 'Controller@action'];

        $route = Mockery::mock(Route::class);
        $route->shouldReceive('getAction')->once()->andReturn($action);

        $this->assertSame($locale, $this->router->getActionLocale($route));
    }
}
