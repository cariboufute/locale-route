<?php

namespace Tests\CaribouFute\LocaleRoute\Functional\Routing;

use CaribouFute\LocaleRoute\Facades\LocaleRoute;
use CaribouFute\LocaleRoute\TestHelpers\EnvironmentSetUp;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;

class LocaleRouterTest extends TestCase
{
    use EnvironmentSetUp;

    public function testAny()
    {
        $callback = function () {
            return 'Yé!';
        };
        $routes = ['fr' => 'francais', 'en' => 'english'];

        LocaleRoute::any('index', $callback, $routes);

        foreach (['get', 'post', 'put', 'patch', 'delete', 'options'] as $method) {
            $response = $this->call($method, '/fr/francais');
            $this->assertSame(200, $response->getStatusCode(), 'No OK response for ' . $method . ' FR route.');

            $response = $this->call($method, '/en/english');
            $this->assertSame(200, $response->getStatusCode(), 'No OK response for ' . $method . ' EN route.');
        }
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

        $response = $this->call($method, '/fr/francais');
        $this->assertSame(200, $response->getStatusCode(), 'No OK response for ' . $method . ' FR route.');

        $response = $this->call($method, '/en/english');
        $this->assertSame(200, $response->getStatusCode(), 'No OK response for ' . $method . ' EN route.');
    }

    public function testGetMakesTwoRoutes()
    {
        LocaleRoute::get('article', function () {
            return 'route';
        }, ['fr' => 'article_fr', 'en' => 'article_en']);

        $response = $this->call('get', '/fr/article_fr');
        $this->assertSame(200, $response->getStatusCode());

        $response = $this->call('get', '/en/article_en');
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testGetMakesTwoRoutesWithSameUrl()
    {
        LocaleRoute::get('article', function () {
            return 'route';
        }, ['fr' => 'article', 'en' => 'article']);

        $response = $this->call('get', '/fr/article');
        $this->assertSame(200, $response->getStatusCode());

        $response = $this->call('get', '/en/article');
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testLocaleRouteUnderRouteGroup()
    {
        Route::group(['locale' => 'es', 'as' => 'article.', 'prefix' => 'article'], function () {
            LocaleRoute::get('create', function () {
                return 'Yes!';
            }, ['fr' => 'creer', 'en' => 'create']);
        });

        $response = $this->call('get', '/fr/article/creer');
        $this->assertSame(200, $response->getStatusCode());

        $response = $this->call('get', '/en/article/create');
        $this->assertSame(200, $response->getStatusCode());
    }

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

        $response = $this->call('get', '/fr/group/creer');
        $this->assertSame(200, $response->getStatusCode());

        $response = $this->call('post', '/group/store');
        $this->assertSame(302, $response->getStatusCode());
        //$this->assertRedirectedTo('fr/group/creer');

        $response = $this->call('get', '/en/group/create');
        $this->assertSame(200, $response->getStatusCode());

        $response = $this->call('post', '/group/store');
        $this->assertSame(302, $response->getStatusCode());
        //$this->assertRedirectedTo('en/group/create');
    }

    public function testGetWithRouteddRouteToUrlOption()
    {
        LocaleRoute::get('create', function () {
            return 'create';
        }, ['fr' => 'creer', 'en' => 'create', 'add_locale_to_url' => false]);

        //Default config is true
        LocaleRoute::get('delete', function () {
            return 'create';
        }, ['fr' => 'supprimer', 'en' => 'delete']);

        $response = $this->call('get', 'creer');
        $this->assertSame(200, $response->getStatusCode());
        $response = $this->call('get', 'create');
        $this->assertSame(200, $response->getStatusCode());
        $response = $this->call('get', '/fr/supprimer');
        $this->assertSame(200, $response->getStatusCode());
        $response = $this->call('get', '/en/delete');
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testGetWithRouteLocalesOption()
    {
        LocaleRoute::get('create', function () {
            return 'create';
        }, ['fr' => 'creer', 'en' => 'create', 'de' => 'erstellen', 'locales' => ['fr', 'en', 'de']]);

        $response = $this->call('get', '/fr/creer');
        $this->assertSame(200, $response->getStatusCode());
        $response = $this->call('get', '/en/create');
        $this->assertSame(200, $response->getStatusCode());
        $response = $this->call('get', '/de/erstellen');
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testGetWithStringOptionReturnsSameRawUrlForAllLocales()
    {
        LocaleRoute::get('create', function () {
            return 'create';
        }, 'create/{id}');

        $response = $this->call('get', '/fr/create/2');
        $this->assertSame(200, $response->getStatusCode());

        $response = $this->call('get', '/en/create/3');
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testLocaleRouteCanTreatTrailingMethods()
    {
        LocaleRoute::get('create', function () {
            return 'create';
        }, 'create/{id}')->where(['id' => '[0-3]']);

        $response = $this->call('get', '/fr/create/2');
        $this->assertSame(200, $response->getStatusCode());

        $response = $this->call('get', '/fr/create/4');
        $this->assertSame(404, $response->getStatusCode());
    }
}
