<?php

namespace Tests\Unit\Router;

use CaribouFute\LocaleRoute\Middleware\SetSessionLocale;
use CaribouFute\LocaleRoute\Routing\Router as LocaleRouter;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Translation\Translator;
use Mockery;
use TestCase;

class RouterTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->router = Mockery::mock(Router::class)->makePartial();

        $this->translator = Mockery::mock(Translator::class)->makePartial();
        $this->localeRouter = Mockery::mock(LocaleRouter::class, [$this->router, $this->translator])->makePartial();
    }

    public function testGetRoute()
    {
        $this->makeRouteTest('get');
    }

    public function testPostRoute()
    {
        $this->makeRouteTest('post');
    }

    public function testPutRoute()
    {
        $this->makeRouteTest('put');
    }

    public function testPatchRoute()
    {
        $this->makeRouteTest('patch');
    }

    public function testDeleteRoute()
    {
        $this->makeRouteTest('delete');
    }

    public function testOptionsRoute()
    {
        $this->makeRouteTest('options');
    }

    protected function makeRouteTest($method)
    {
        $route = 'route';
        $frUrl = 'urlfr';
        $enUrl = 'urlen';
        $action = 'ActionController@action';

        $frRouteInstance = Mockery::mock(Route::class);
        $enRouteInstance = Mockery::mock(Route::class);

        $this->translator->shouldReceive('get')->with('routes.route', [], 'fr')->once()->andReturn($frUrl);
        $this->translator->shouldReceive('get')->with('routes.route', [], 'en')->once()->andReturn($enUrl);

        $this->router->shouldReceive($method)->with('fr/' . $frUrl, ['as' => 'fr.' . $route, 'uses' => $action])->once()->andReturn($frRouteInstance);
        $frRouteInstance->shouldReceive('middleware')->with(SetSessionLocale::class . ':fr')->once();

        $this->router->shouldReceive($method)->with('en/' . $enUrl, ['as' => 'en.' . $route, 'uses' => $action])->once()->andReturn($enRouteInstance);
        $enRouteInstance->shouldReceive('middleware')->with(SetSessionLocale::class . ':en')->once();

        $localeRouteMethod = $method . 'Route';
        $this->localeRouter->$localeRouteMethod($route, $action);
    }

    public function testGet()
    {
        $this->makeUrlTest('get');
    }

    public function testPost()
    {
        $this->makeUrlTest('post');
    }

    public function testPut()
    {
        $this->makeUrlTest('put');
    }

    public function testPatch()
    {
        $this->makeUrlTest('patch');
    }

    public function testDelete()
    {
        $this->makeUrlTest('delete');
    }

    public function testOptions()
    {
        $this->makeUrlTest('options');
    }

    protected function makeUrlTest($method)
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
        $frRouteInstance->shouldReceive('middleware')->with(SetSessionLocale::class . ':fr')->once();

        $this->router->shouldReceive($method)->with('en/' . $enRoute, $action)->once()->andReturn($enRouteInstance);
        $enRouteInstance->shouldReceive('middleware')->with(SetSessionLocale::class . ':en')->once();

        $this->localeRouter->$method($routeArray, $action);
    }
}
