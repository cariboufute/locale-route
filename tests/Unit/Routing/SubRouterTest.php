<?php

namespace Tests\Unit\Router;

use CaribouFute\LocaleRoute\Locale\Route as LocaleRoute;
use CaribouFute\LocaleRoute\Locale\Url as LocaleUrl;
use CaribouFute\LocaleRoute\Routing\SubRouter;
use CaribouFute\LocaleRoute\TestHelpers\EnvironmentSetUp;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router as LaravelRouter;
use Mockery;
use Orchestra\Testbench\TestCase;

class SubRouterTest extends TestCase
{
    use EnvironmentSetUp;

    public function setUp()
    {
        parent::setUp();

        $this->laravelRouter = Mockery::mock(LaravelRouter::class);
        $this->routeLocalizer = Mockery::mock(LocaleRoute::class);
        $this->url = Mockery::mock(LocaleUrl::class)->makePartial();

        $this->router = Mockery::mock(SubRouter::class, [$this->laravelRouter, $this->routeLocalizer, $this->url])->makePartial();
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
        $locale = 'fr';
        $urls = [];
        $routeObjects = [];
        $groupStack = [
            [
                "as" => $locale . ".",
                "prefix" => $locale,
                "middleware" => [
                    "locale.session:" . $locale,
                ],
            ],
        ];

        $this->laravelRouter->shouldReceive('getGroupStack')->once()->andReturn($groupStack);
        $this->routeLocalizer->shouldReceive('getLocale')->with($groupStack[0]['as'])->once()->andReturn($locale);

        $url = '/url' . $locale;
        $routeObject = Mockery::mock(Route::class);

        $this->url
            ->shouldReceive('getUnlocaleRouteUrl')
            ->with($locale, $route, [])
            ->once()
            ->andReturn($url);

        $this->laravelRouter
            ->shouldReceive($method)
            ->with($url, ['as' => $route, 'uses' => $action])
            ->once()
            ->andReturn($routeObject);

        $this->router->$method($route, $action);
    }
}
