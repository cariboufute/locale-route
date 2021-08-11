<?php

namespace Tests\CaribouFute\LocaleRoute\Unit\Routing;

use CaribouFute\LocaleRoute\Prefix\Route as PrefixRoute;
use CaribouFute\LocaleRoute\Prefix\Url as PrefixUrl;
use CaribouFute\LocaleRoute\Routing\Router;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router as IlluminateRouter;
use Mockery;
use Orchestra\Testbench\TestCase;

class RouterTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->illuminateRouter = Mockery::mock(IlluminateRouter::class);
        $this->url = Mockery::mock(PrefixUrl::class);
        $this->route = Mockery::mock(PrefixRoute::class);
        $this->router = Mockery::mock(Router::class, [$this->illuminateRouter, $this->url, $this->route])
            ->makePartial();
    }

    public function testAny()
    {
        $this->makeRouteTest('any');
    }

    public function testGet()
    {
        $this->makeRouteTest('get');
    }

    public function testPost()
    {
        $this->makeRouteTest('post');
    }

    public function testPut()
    {
        $this->makeRouteTest('put');
    }

    public function testPatch()
    {
        $this->makeRouteTest('patch');
    }

    public function testDelete()
    {
        $this->makeRouteTest('delete');
    }

    public function testOptions()
    {
        $this->makeRouteTest('options');
    }

    public function makeRouteTest($method)
    {
        $route = Mockery::mock(Route::class);
        $url = 'url';
        $action = 'Controller@action';

        $this->illuminateRouter->shouldReceive($method)->with($url, $action)->once()->andReturn($route);
        $this->router->shouldReceive('addLocale')->with($route, $action)->once()->andReturn($route);
        $this->router->shouldReceive('refreshRoutes')->once();

        $this->assertSame($route, $this->router->$method($url, $action));
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
        $middleware = 'locale.session:' . $locale;
        $name = 'route';
        $newName = $locale . '.' . $name;

        $action = ['locale' => $locale, 'as' => $name, 'uses' => 'Controller@action'];

        $route = Mockery::mock(Route::class);

        $this->router->shouldReceive('getActionLocale')->with($route)->once()->andReturn($locale);
        $this->router->shouldReceive('switchRouteLocale')->with($locale, $route)->once()->andReturn($route);
        $this->router->shouldReceive('switchUrlLocale')->with($locale, $route, $action)->once()->andReturn($route);
        $route->shouldReceive('middleware')->with($middleware);

        $testRoute = $this->router->addLocale($route, $action);

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
        $this->url->shouldReceive('switchLocale')->with($locale, $uri, [])->once()->andReturn($newUri);
        $route->shouldReceive('setUri')->with($locale . '/' . $uri)->once();

        $testRoute = $this->router->switchUrlLocale($locale, $route);

        $this->assertSame($route, $testRoute);
    }

    public function testGetActionLocalesReturnNullWhenNoLocaleKey()
    {
        $action = ['as' => 'route', 'uses' => 'Controller@action'];

        $route = Mockery::mock(Route::class);
        $route->shouldReceive('getAction')->once()->andReturn($action);

        $this->assertEmpty($this->router->getActionLocales($route));
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

        $this->assertFalse($this->router->getActionLocale($route));
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
