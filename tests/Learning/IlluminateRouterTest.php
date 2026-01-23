<?php

namespace Tests\CaribouFute\LocaleRoute\Learning;

use CaribouFute\LocaleRoute\TestHelpers\EnvironmentSetUp;
use Illuminate\Routing\Router;
use Orchestra\Testbench\TestCase;

class IlluminateRouterTest extends TestCase
{
    use EnvironmentSetUp;

    public function setUp(): void
    {
        parent::setUp();
        $this->router = app()->make(Router::class);
    }

    public function testActionKeepsLocale()
    {
        $locale = 'fr';
        $route = $this->router->get('test', ['locale' => $locale, 'as' => 'route', 'uses' => 'Controller@action']);

        $this->assertSame($locale, $route->getAction()['locale']);
    }

    public function testRouteGroupKeepsLocale()
    {
        $locale = 'fr';
        $this->router->group(['prefix' => 'group', 'as' => 'group.', 'locale' => $locale], function () {
            $this->router->get('test', ['as' => 'route', 'uses' => 'Controller@action']);
        });

        $routes = collect($this->router->getRoutes()->getRoutes());
        $route = $routes->firstWhere('uri', 'group/test');
        $routeLocale = $route->getAction()['locale'];

        $this->assertSame($locale, $routeLocale);
    }

    public function testRouteGroupAndGetCombineBothLocalesIntoArray()
    {
        $this->router->group(['locale' => 'fr', 'prefix' => 'group', 'as' => 'group.'], function () {
            $this->router->get('test', ['locale' => 'en', 'as' => 'route', 'uses' => 'Controller@action']);
        });

        $routes = collect($this->router->getRoutes()->getRoutes());
        $route = $routes->firstWhere('uri', 'group/test');
        $routeLocales = $route->getAction()['locale'];

        $this->assertSame(['fr', 'en'], $routeLocales);
    }
}
