<?php

namespace Tests\CaribouFute\LocaleRoute\Functional;

use CaribouFute\LocaleRoute\Facades\LocaleRoute;
use CaribouFute\LocaleRoute\TestHelpers\EnvironmentSetUp;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Orchestra\Testbench\TestCase;

class HelpersTest extends TestCase
{
    use EnvironmentSetUp;

    public function setUp(): void
    {
        parent::setUp();
        Session::start();
    }

    public function testLocaleRoute()
    {
        LocaleRoute::get('route', function () {
            return 'route';
        }, ['fr' => 'route_fr', 'en' => 'route_en']);

        $this->assertSame(url('fr/route_fr'), locale_route('fr', 'route'));
        $this->assertSame(url('en/route_en'), locale_route('en', 'route'));
    }

    public function testLocaleRouteWithNoParametersReturnsNoParameters()
    {
        LocaleRoute::get('article.show', function () {
            return 'route';
        }, ['fr' => 'article/{id}', 'en' => 'article/{id}']);

        $this->assertSame(url('fr/article/2'), locale_route('fr', 'article.show', ['id' => 2]));
        $this->assertSame(url('en/article/2'), locale_route('en', 'article.show', ['id' => 2]));
    }

    public function testLocaleRouteWithNonLocaleRouteReturnsItsUrl()
    {
        Route::get('route', [
            'as' => 'route',
            'uses' => function () {
                return 'route';
            }
        ]);

        $this->assertSame(url('route'), locale_route('fr', 'route'));
        $this->assertSame(url('route'), locale_route('en', 'route'));
    }

    public function testLocaleRouteWithLocaleAndNonLocaleRoutesReturnsLocaleRouteUrl()
    {
        LocaleRoute::get('route', function () {
            return 'route';
        }, ['fr' => 'route_fr', 'en' => 'route_en']);

        Route::get('route', [
            'as' => 'route',
            'uses' => function () {
                return 'route';
            }
        ]);

        $this->assertSame(url('fr/route_fr'), locale_route('fr', 'route'));
        $this->assertSame(url('en/route_en'), locale_route('en', 'route'));
    }

    public function testOtherLocaleWithDefaultParameters()
    {
        LocaleRoute::get('article.show', function () {
            return 'route';
        }, ['fr' => 'article/{id}', 'en' => 'article/{id}']);

        $response = $this->call('get', 'fr/article/2');

        $this->assertSame(url('fr/article/2'), other_locale('fr'));
        $this->assertSame(url('en/article/2'), other_locale('en'));
    }

    public function testOtherLocaleReturnsNonLocaleUrlWhenNotLocalized()
    {
        Route::get('route', [
            'as' => 'route',
            'uses' => function () {
                return 'route';
            }
        ]);

        $response = $this->call('get', 'route');

        $this->assertSame(url('route'), other_locale('fr'));
        $this->assertSame(url('route'), other_locale('en'));
    }

    public function testOtherRouteWithEmptyParameters()
    {
        LocaleRoute::get('article.show', function () {
            return 'route';
        }, ['fr' => 'article/{param}', 'en' => 'article/{param}']);

        LocaleRoute::get('articles', function () {
            return 'route';
        }, ['fr' => 'articles', 'en' => 'articles']);

        $response = $this->call('get', locale_route('en', 'article.show', ['param' => 'foo']));

        $this->assertSame(url('en/articles'), other_route('articles'));
    }

}
