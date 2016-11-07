<?php

namespace Tests\Unit\Router;

use CaribouFute\LocaleRoute\Locale\Route as LocaleRoute;
use CaribouFute\LocaleRoute\Locale\Url as LocaleUrl;
use CaribouFute\LocaleRoute\Middleware\SetSessionLocale;
use CaribouFute\LocaleRoute\Routing\Router;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router as LaravelRouter;
use Mockery;
use Orchestra\Testbench\TestCase;

class RouterTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $this->locales = ['fr', 'en', 'es'];
        $app['config']->set('localeroute.locales', $this->locales);
        $app['config']->set('localeroute.add_locale_to_url', true);
    }

    public function setUp()
    {
        parent::setUp();

        $this->laravelRouter = Mockery::mock(LaravelRouter::class)->makePartial();
        $this->routeLocalizer = Mockery::mock(LocaleRoute::class);
        $this->url = Mockery::mock(LocaleUrl::class)->makePartial();

        $this->localeRouter = Mockery::mock(Router::class, [$this->laravelRouter, $this->routeLocalizer, $this->url])->makePartial();
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
        $routeObjects = [];

        foreach ($this->locales as $locale) {
            $urls[$locale] = $locale . '/url' . $locale;
            $routeObjects[$locale] = Mockery::mock(Route::class);

            $this->routeLocalizer->shouldReceive('addLocale')->with($locale, $route)->once()->andReturn($locale . '.' . $route);
            $this->url->shouldReceive('getRouteUrl')->with($locale, $route, [])->once()->andReturn($urls[$locale]);
            $this->laravelRouter->shouldReceive($method)->with($urls[$locale], ['as' => $locale . '.' . $route, 'uses' => $action])->once()->andReturn($routeObjects[$locale]);
            $routeObjects[$locale]->shouldReceive('middleware')->with(SetSessionLocale::class . ':' . $locale)->once();
        }

        $this->localeRouter->$method($route, $action);
    }

    public function testGroupWithNoHasAttribute()
    {
        $attributes = ['prefix' => 'url', 'middleware' => 'auth'];
        $callback = function () {};

        foreach ($this->locales as $locale) {
            $newAttributes = ['as' => $locale . '.', 'prefix' => $locale . '/url', 'middleware' => ['auth', SetSessionLocale::class . ':' . $locale]];
            $this->routeLocalizer->shouldReceive('addLocale')->with($locale, '')->once()->andReturn($locale . '.');
            $this->laravelRouter->shouldReceive('group')->with($newAttributes, $callback)->once();
        }

        $this->localeRouter->group($attributes, $callback);
    }

    public function testGroupWithHasAttribute()
    {
        $route = 'route';
        $attributes = ['as' => $route, 'prefix' => 'url', 'middleware' => 'auth'];
        $callback = function () {};

        foreach ($this->locales as $locale) {
            $newAttributes = ['as' => $locale . '.' . $route, 'prefix' => $locale . '/url', 'middleware' => ['auth', SetSessionLocale::class . ':' . $locale]];
            $this->routeLocalizer->shouldReceive('addLocale')->with($locale, $route)->once()->andReturn($locale . '.' . $route);
            $this->laravelRouter->shouldReceive('group')->with($newAttributes, $callback)->once();
        }

        $this->localeRouter->group($attributes, $callback);
    }
}
