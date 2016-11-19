<?php

namespace Tests\Functional\Routing;

use CaribouFute\LocaleRoute\Facades\LocaleRoute;
use CaribouFute\LocaleRoute\Routing\Router;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;

class RouterTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $this->locales = ['fr', 'en'];
        $this->addLocaleOption = true;

        $app['config']->set('localeroute.locales', $this->locales);
        $app['config']->set('localeroute.add_locale_to_url', $this->addLocaleOption);
        $app['locale-route'] = app()->make(Router::class);
    }

    public function testLocaleRouteInGroupHasLocalePrefixFirstInRouteName()
    {
        LocaleRoute::group([], function () {
            Route::group(['as' => 'foo.', 'prefix' => 'foo'], function () {
                Route::group(['as' => 'bar.', 'prefix' => 'bar'], function () {
                    Route::get('test', function () {}, ['fr' => 'test', 'en' => 'test', 'es' => 'test']);
                });
            });
        });

        $this->call('get', 'fr/foo/bar/test');
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
