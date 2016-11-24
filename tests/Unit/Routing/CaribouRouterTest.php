<?php

namespace Tests\Unit\Routing;

use CaribouFute\LocaleRoute\Routing\CaribouRouter;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router as IlluminateRouter;
use Mockery;
use Orchestra\Testbench\TestCase;

class ClassTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->illuminateRouter = Mockery::mock(IlluminateRouter::class);
        $this->router = new CaribouRouter($this->illuminateRouter);
    }

    public function testGetActionLocalesReturnNullWhenNoLocaleKey()
    {
        $action = ['as' => 'route', 'uses' => 'Controller@action'];

        $route = Mockery::mock(Route::class);
        $route->shouldReceive('getAction')->once()->andReturn($action);

        $this->assertNull($this->router->getActionLocales($route));
    }

    public function testGetActionLocalesReturnsLocaleKey()
    {
        $locales = ['fr', 'en'];
        $action = ['locale' => $locales, 'as' => 'route', 'uses' => 'Controller@action'];

        $route = Mockery::mock(Route::class);
        $route->shouldReceive('getAction')->once()->andReturn($action);

        $this->assertSame($locales, $this->router->getActionLocales($route));
    }

    public function testGetActionLocaleReturnNullWhenNoLocaleKey()
    {
        $action = ['as' => 'route', 'uses' => 'Controller@action'];

        $route = Mockery::mock(Route::class);
        $route->shouldReceive('getAction')->once()->andReturn($action);

        $this->assertNull($this->router->getActionLocale($route));
    }

    public function testGetActionLocaleReturnLocaleKeyWhenString()
    {
        $locale = 'fr';
        $action = ['locale' => $locale, 'as' => 'route', 'uses' => 'Controller@action'];

        $route = Mockery::mock(Route::class);
        $route->shouldReceive('getAction')->once()->andReturn($action);

        $this->assertSame($locale, $this->router->getActionLocale($route));
    }

    public function testGetActionLocaleReturnLastLocaleWhenArray()
    {
        $locale = 'fr';
        $locales = ['en', $locale];
        $action = ['locale' => $locales, 'as' => 'route', 'uses' => 'Controller@action'];

        $route = Mockery::mock(Route::class);
        $route->shouldReceive('getAction')->once()->andReturn($action);

        $this->assertSame($locale, $this->router->getActionLocale($route));
    }

    public function testGet()
    {
        $url = 'url';
        $action = 'Controller@action';
        $route = Mockery::mock(Route::class);

        $this->illuminateRouter->shouldReceive('get')->with($url, $action)->once()->andReturn($route);

        $this->router->get($url, $action);

        $this->assertTrue(true);
    }
}
