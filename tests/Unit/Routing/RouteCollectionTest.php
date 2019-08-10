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

    public function setUp(): void
    {
        parent::setUp();
        $this->router = app()->make(Router::class);
        $this->coll = app()->make(RouteCollection::class);
    }

    public function testHydrate()
    {
        Route::get('test', ['as' => 'test', 'uses' => 'Controller@action']);
        $coll = $this->router->getRoutes();
        $route = $coll->getRoutes()[0];

        $this->coll->hydrate($coll);

        $testRoute = $this->coll->getRoutes()[0];
        $this->assertEquals($route, $testRoute);
    }
}
