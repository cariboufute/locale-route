<?php

namespace Tests\Unit\Router;

use CaribouFute\LocaleRoute\Middleware\SetSessionLocale;
use CaribouFute\LocaleRoute\Routing\Router as LocaleRouter;
use CaribouFute\LocaleRoute\Routing\Url as Url;
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
        $this->url = Mockery::mock(Url::class)->makePartial();
        $this->localeRouter = Mockery::mock(LocaleRouter::class, [$this->router, $this->url])->makePartial();
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
        $frUrl = 'fr/urlfr';
        $enUrl = 'en/urlen';
        $action = 'ActionController@action';

        $frRouteInstance = Mockery::mock(Route::class);
        $enRouteInstance = Mockery::mock(Route::class);

        $this->url->shouldReceive('getRouteUrl')->with('fr', $route, [])->once()->andReturn($frUrl);
        $this->url->shouldReceive('getRouteUrl')->with('en', $route, [])->once()->andReturn($enUrl);

        $this->router->shouldReceive($method)->with($frUrl, ['as' => 'fr.' . $route, 'uses' => $action])->once()->andReturn($frRouteInstance);
        $frRouteInstance->shouldReceive('middleware')->with(SetSessionLocale::class . ':fr')->once();

        $this->router->shouldReceive($method)->with($enUrl, ['as' => 'en.' . $route, 'uses' => $action])->once()->andReturn($enRouteInstance);
        $enRouteInstance->shouldReceive('middleware')->with(SetSessionLocale::class . ':en')->once();

        $localeRouteMethod = $method;
        $this->localeRouter->$localeRouteMethod($route, $action);
    }
}
