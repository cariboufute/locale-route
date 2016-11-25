<?php

namespace Tests\Unit\Routing;

use CaribouFute\LocaleRoute\Routing\RouteCollection;
use CaribouFute\LocaleRoute\Routing\Router;
use CaribouFute\LocaleRoute\TestHelpers\EnvironmentSetUp;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;

class RouteCollectionTest extends TestCase
{
    use EnvironmentSetUp;

    public function setUp()
    {
        parent::setUp();
        $this->router = app()->make(Router::class);
        $this->coll = app()->make(RouteCollection::class);
    }

    public function testClone()
    {
        Route::get('test', ['as' => 'test', 'uses' => 'Controller@action']);
        $coll = $this->router->getRoutes();
        $route = $coll->getRoutes()[0];

        $this->coll->clone($coll);

        $testRoute = $this->coll->getRoutes()[0];
        $this->assertEquals($route, $testRoute);
    }
}
