<?php

namespace Tests\Functional\Routing;

use CaribouFute\LocaleRoute\Facades\LocaleRoute;
use CaribouFute\LocaleRoute\Facades\SubRoute;
use CaribouFute\LocaleRoute\TestHelpers\EnvironmentSetUp;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;

class LocaleRouterTest extends TestCase
{
    use EnvironmentSetUp;

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

    public function testSubRoute()
    {
        LocaleRoute::group(['as' => 'article.', 'prefix' => 'article'], function () {
            SubRoute::get('create', function () {
                return 'Yes!';
            }, ['fr' => 'créer', 'en' => 'create']);
        });

        $this->call('get', '/fr/article/créer');
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

    public function testGroupWithoutPrefix()
    {
        LocaleRoute::group([], function () {
            Route::get('/', ['as' => 'index', function () {
                return 'Yé!';
            }]);
        });

        $this->call('get', '/fr');
        $this->assertResponseOk();

        $this->call('get', '/en');
        $this->assertResponseOk();
    }

    public function testGroupWithPrefixAndParameter()
    {
        LocaleRoute::group(['as' => 'article.', 'prefix' => 'article'], function () {
            Route::get('/', ['as' => 'index', function () {
                return 'Yé!';
            }]);

            Route::get('/{id}', ['as' => 'show', function ($id) {
                return $id;
            }]);
        });

        $this->call('get', '/fr/article');
        $this->assertResponseOk();

        $this->call('get', '/en/article');
        $this->assertResponseOk();

        $this->call('get', '/fr/article/1');
        $this->assertResponseOk();

        $this->call('get', '/en/article/2');
        $this->assertResponseOk();
    }
}
