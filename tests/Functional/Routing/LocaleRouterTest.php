<?php

namespace Tests\Functional\Routing;

use CaribouFute\LocaleRoute\Facades\LocaleRoute;
use CaribouFute\LocaleRoute\TestHelpers\EnvironmentSetUp;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;

class LocaleRouterTest extends TestCase
{
    use EnvironmentSetUp;

    public function testSessionLocaleIsKeptInUnlocaleRoute()
    {
        Route::group(['as' => 'group.', 'prefix' => 'group'], function () {
            LocaleRoute::get('create', function () {
                return 'creer';
            }, ['fr' => 'creer', 'en' => 'create']);

            Route::post('store', function () {
                return redirect(other_route('group.create'));
            });
        });

        $this->call('get', '/fr/group/creer');
        $this->assertResponseOk();

        $this->call('post', '/group/store');
        $this->assertRedirectedTo('fr/group/creer');

        $this->call('get', '/en/group/create');
        $this->assertResponseOk();

        $this->call('post', '/group/store');
        $this->assertRedirectedTo('en/group/create');

    }
    public function testGetMakesTwoRoutes()
    {
        LocaleRoute::get('article', function () {
            return 'route';
        }, ['fr' => 'article_fr', 'en' => 'article_en']);

        $this->call('get', '/fr/article_fr');
        $this->assertResponseOk();

        $this->call('get', '/en/article_en');
        $this->assertResponseOk();
    }

    public function testGetMakesTwoRoutesWithSameUrl()
    {
        LocaleRoute::get('article', function () {
            return 'route';
        }, ['fr' => 'article', 'en' => 'article']);

        $this->call('get', '/fr/article');
        $this->assertResponseOk();

        $this->call('get', '/en/article');
        $this->assertResponseOk();
    }

    public function testLocaleRouteUnderRouteGroup()
    {
        Route::group(['locale' => 'es', 'as' => 'article.', 'prefix' => 'article'], function () {
            LocaleRoute::get('create', function () {
                return 'Yes!';
            }, ['fr' => 'creer', 'en' => 'create']);
        });

        $this->call('get', '/fr/article/creer');
        $this->assertResponseOk();

        $this->call('get', '/en/article/create');
        $this->assertResponseOk();
    }

    public function testGet()
    {
        $this->makeMethodTest('get');
    }

    public function testPost()
    {
        $this->makeMethodTest('post');
    }

    public function testPut()
    {
        $this->makeMethodTest('put');
    }

    public function testPatch()
    {
        $this->makeMethodTest('patch');
    }

    public function testDelete()
    {
        $this->makeMethodTest('delete');
    }

    public function testOptions()
    {
        $this->makeMethodTest('options');
    }

    public function makeMethodTest($method)
    {
        LocaleRoute::$method('index', function () {
            return 'Yé!';
        }, ['fr' => 'francais', 'en' => 'english']);

        $this->call($method, '/fr/francais');
        $this->assertResponseOk('No OK response for ' . $method . ' FR route.');

        $this->call($method, '/en/english');
        $this->assertResponseOk('No OK response for ' . $method . ' EN route.');
    }
}
