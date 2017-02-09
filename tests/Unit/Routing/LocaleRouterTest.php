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

    public function testGetWithStringMiddleware()
    {
        $this->makeRouteTest('get', 'auth');
    }

    public function testGetWithArrayMiddleware()
    {
        $this->makeRouteTest('get', ['auth', 'guest']);
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

    protected function makeRouteTest($method, $middleware = [])
    {
        $route = 'route';
        $action = 'ActionController@action';
        $options = $middleware ? ['middleware' => $middleware] : [];

        foreach ($this->locales as $locale) {
            $url = 'url' . $locale;
            $routeObject = Mockery::mock(Route::class);

            $this->prefixUrl
                ->shouldReceive('rawRouteUrl')
                ->with($locale, $route, $options)
                ->once()
                ->andReturn($url);

            $this->router
                ->shouldReceive($method)
                ->with($url, ['locale' => $locale, 'as' => $route, 'uses' => $action])
                ->once()
                ->andReturn($routeObject);

            $routeObject->shouldReceive('middleware')->with($middleware)->once();
        }

        $this->localeRouter->$method($route, $action, $options);

        $this->assertTrue(true);
    }
}
