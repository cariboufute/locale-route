<?php

namespace Tests\Unit\Router;

use CaribouFute\LocaleRoute\Http\Middleware\SetLocale;
use CaribouFute\LocaleRoute\Routing\Router as LocaleRouter;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Mockery;
use TestCase;

class RouterTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->router = Mockery::mock(Router::class)->makePartial();
        $this->localeRouter = Mockery::mock(LocaleRouter::class, [$this->router])->makePartial();
    }

    public function testGet()
    {
        $this->makeRouteTest('get');
    }

    protected function makeRouteTest($method)
    {
        $frRoute = 'route1';
        $enRoute = 'route2';
        $action = 'ActionController@action';
        $routeArray = [
            'fr' => $frRoute,
            'en' => $enRoute,
        ];

        $frRouteInstance = Mockery::mock(Route::class);
        $enRouteInstance = Mockery::mock(Route::class);

        $this->router->shouldReceive($method)->with('fr/' . $frRoute, $action)->once()->andReturn($frRouteInstance);
        $frRouteInstance->shouldReceive('middleware')->with(SetLocale::class . ':fr')->once();

        $this->router->shouldReceive($method)->with('en/' . $enRoute, $action)->once()->andReturn($enRouteInstance);
        $enRouteInstance->shouldReceive('middleware')->with(SetLocale::class . ':en')->once();

        $this->localeRouter->$method($routeArray, $action);
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
}
