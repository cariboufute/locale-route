<?php

namespace Tests\Unit\Router;

use CaribouFute\LocaleRoute\Prefix\Route as PrefixRoute;
use CaribouFute\LocaleRoute\Prefix\Url as PrefixUrl;
use CaribouFute\LocaleRoute\Routing\LocaleRouter;
use CaribouFute\LocaleRoute\Routing\Router;
use CaribouFute\LocaleRoute\TestHelpers\EnvironmentSetUp;
use Illuminate\Routing\Route;
use Mockery;
use Orchestra\Testbench\TestCase;

class LocaleRouterTest extends TestCase
{
    use EnvironmentSetUp;

    public function setUp()
    {
        parent::setUp();

        $this->router = Mockery::mock(Router::class);
        $this->prefixRoute = Mockery::mock(PrefixRoute::class);
        $this->prefixUrl = Mockery::mock(PrefixUrl::class)->makePartial();

        $this->localeRouter = Mockery::mock(LocaleRouter::class, [$this->router, $this->prefixRoute, $this->prefixUrl])->makePartial();
    }

    public function testAddMiddlewareWithoutLocaleRoutesInArray()
    {
        $route = 'route';
        $action = 'ActionController@action';
        $middleware = ['guest', 'auth'];
        $options = ['middleware' => $middleware];

        foreach ($this->locales as $locale) {
            $url = 'url' . $locale;
            $routeObject = Mockery::mock(Route::class);
            $routeMiddleware = $middleware + [2 => 'locale.session:' . $locale];

            $this->prefixUrl
                ->shouldReceive('getUnlocaleRouteUrl')
                ->with($locale, $route, [])
                ->once()
                ->andReturn($url);

            $this->router
                ->shouldReceive('get')
                ->with($url, ['locale' => $locale, 'as' => $route, 'uses' => $action])
                ->once()
                ->andReturn($routeObject);

            $routeObject
                ->shouldReceive('middleware')
                ->with($routeMiddleware)
                ->once();
        }

        $this->localeRouter->get($route, $action, $options);
    }

    public function testAddMiddleware()
    {
        $route = 'route';
        $action = 'ActionController@action';
        $middleware = 'guest';
        $urls = ['fr' => 'routefr', 'en' => 'routeen', 'es' => 'routees', 'middleware' => $middleware];

        foreach ($this->locales as $locale) {
            $localeRoute = $locale . '.' . $route;
            $routeObject = Mockery::mock(Route::class);
            $routeMiddleware = [$middleware, 'locale.session:' . $locale];

            $url = 'url' . $locale;
            $routeObject = Mockery::mock(Route::class);

            $this->prefixUrl
                ->shouldReceive('getUnlocaleRouteUrl')
                ->with($locale, $route, [])
                ->once()
                ->andReturn($url);

            $this->router
                ->shouldReceive('get')
                ->with($url, ['locale' => $locale, 'as' => $route, 'uses' => $action])
                ->once()
                ->andReturn($routeObject);

            $routeObject
                ->shouldReceive('middleware')
                ->with($routeMiddleware)
                ->once();
        }

        $this->localeRouter->get($route, $action, $urls);
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

    protected function makeRouteTest($method)
    {
        $route = 'route';
        $action = 'ActionController@action';
        $urls = [];

        foreach ($this->locales as $locale) {
            $url = 'url' . $locale;
            $routeObject = Mockery::mock(Route::class);

            $this->prefixUrl
                ->shouldReceive('getUnlocaleRouteUrl')
                ->with($locale, $route, [])
                ->once()
                ->andReturn($url);

            $this->router
                ->shouldReceive($method)
                ->with($url, ['locale' => $locale, 'as' => $route, 'uses' => $action])
                ->once()
                ->andReturn($routeObject);

            $routeObject->shouldReceive('middleware')->with(['locale.session:' . $locale])->once();
        }

        $this->localeRouter->$method($route, $action);
    }

    public function testGroupWithNoHasAttribute()
    {
        $attributes = ['prefix' => 'url', 'middleware' => 'auth'];
        $callback = function () {
        };

        foreach ($this->locales as $locale) {
            $newAttributes = ['as' => $locale . '.', 'prefix' => $locale . '/url', 'middleware' => ['auth', 'locale.session:' . $locale]];
            $this->prefixRoute->shouldReceive('addLocale')->with($locale, '')->once()->andReturn($locale . '.');
            $this->router->shouldReceive('group')->with($newAttributes, $callback)->once();
        }

        $this->localeRouter->group($attributes, $callback);
    }

    public function testGroupWithHasAttribute()
    {
        $route = 'route';
        $attributes = ['as' => $route, 'prefix' => 'url', 'middleware' => 'auth'];
        $callback = function () {
        };

        foreach ($this->locales as $locale) {
            $newAttributes = ['as' => $locale . '.' . $route, 'prefix' => $locale . '/url', 'middleware' => ['auth', 'locale.session:' . $locale]];
            $this->prefixRoute->shouldReceive('addLocale')->with($locale, $route)->once()->andReturn($locale . '.' . $route);
            $this->router->shouldReceive('group')->with($newAttributes, $callback)->once();
        }

        $this->localeRouter->group($attributes, $callback);
    }

    public function testResource()
    {
        $name = 'article';
        $controller = 'Controller';
        $options = ['options' => 'yé'];

        foreach ($this->locales as $locale) {
            $localeName = $locale . '.' . $name;
            $this->prefixRoute->shouldReceive('addLocale')->with($locale, $name)->andReturn($localeName);
            $this->router->shouldReceive('resource')->with($localeName, $controller, $options);
        }

        $this->localeRouter->resource($name, $controller, $options);
    }
}
