<?php

namespace Tests\CaribouFute\LocaleRoute\Functional\Routing;

use CaribouFute\LocaleRoute\Routing\Router;
use CaribouFute\LocaleRoute\TestHelpers\EnvironmentSetUp;
use Orchestra\Testbench\TestCase;

class RouterTest extends TestCase
{
    use EnvironmentSetUp;

    public function setUp(): void
    {
        parent::setUp();
        $this->router = app()->make(Router::class);
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
        $this->router->$method('test', ['locale' => 'fr', 'as' => 'route', 'uses' => function () {
            return 'yes!';
        }]);

        $this->makeAssertMethodTest($method);
    }

    protected function makeAssertMethodTest($method)
    {
        $response = $this->call($method, 'fr/test');
        $this->assertSame(200, $response->getStatusCode());

        $response = $this->call($method, route('fr.route'));
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testAny()
    {
        $this->router->any('test', ['locale' => 'fr', 'as' => 'route', 'uses' => function () {
            return 'yes!';
        }]);

        $this->makeAssertMethodTest('get');
        $this->makeAssertMethodTest('post');
        $this->makeAssertMethodTest('put');
        $this->makeAssertMethodTest('patch');
        $this->makeAssertMethodTest('delete');
        $this->makeAssertMethodTest('options');
    }

    public function testGetWithSameUriAndDifferentLocale()
    {
        $this->router->get('test', ['locale' => 'fr', 'as' => 'route', 'uses' => function () {
            return 'yé!';
        }]);
        $this->router->get('test', ['locale' => 'en', 'as' => 'route', 'uses' => function () {
            return 'yé!';
        }]);

        //To test that route collection is well refreshed and
        //that there is no duplication of routes
        //after adding changed route.
        $this->assertNotNull($this->router->getRoutes()->get('GET')['fr/test']);
        $this->assertNotNull($this->router->getRoutes()->get('GET')['en/test']);

        $response = $this->call('GET', 'fr/test');
        $this->assertSame(200, $response->getStatusCode());

        $response = $this->call('GET', route('fr.route'));
        $this->assertSame(200, $response->getStatusCode());

        $response = $this->call('GET', 'en/test');
        $this->assertSame(200, $response->getStatusCode());

        $response = $this->call('GET', route('en.route'));
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testGetInGroup()
    {
        $router = $this->router;

        $router->group(['as' => 'group.', 'prefix' => 'group'], function () use ($router) {
            $router->get('test', ['locale' => 'fr', 'as' => 'route', 'uses' => function () {
                return 'yé!';
            }]);
        });

        $response = $this->call('GET', 'fr/group/test');
        $this->assertSame(200, $response->getStatusCode());

        $response = $this->call('GET', route('fr.group.route'));
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testCompiledRoutesPreserveLocalizedGroupPrefixes()
    {
        $router = $this->router;
        $router->group(['prefix' => 'api'], function () use ($router) {
            $router->group(['prefix' => 'work'], function () use ($router) {
                $router->get('{slug}', ['locale' => 'fr', 'as' => 'work', 'uses' => function () {
                    return 'yes';
                }]);
            });
        });

        $before = $router->getRoutes()->getByName('fr.work');
        $this->assertSame('fr/api/work/{slug}', $before->uri());
        $url = route('fr.work', ['slug' => 'example']);
        $this->assertStringEndsWith('/fr/api/work/example', $url);

        $compiled = $router->getRoutes()->compile();
        app('router')->setCompiledRoutes($compiled);
        $this->assertSame($before->uri(), app('router')->getRoutes()->getByName('fr.work')->uri());
        $this->assertSame($url, route('fr.work', ['slug' => 'example']));
    }

    public function testCompiledRoutesPreservePrefixOptionsAndLocaleChanges()
    {
        $router = $this->router;
        $router->get('plain/{slug}', ['locale' => 'fr', 'as' => 'plain', 'uses' => function () {
            return 'yes';
        }]);

        $router->group(['prefix' => 'api'], function () use ($router) {
            $router->get('disabled/{slug}', [
                'locale' => 'fr', 'as' => 'disabled', 'add_locale_to_url' => false, 'uses' => function () {
                    return 'yes';
                },
            ]);
            $route = $router->get('changed/{slug}', ['locale' => 'fr', 'as' => 'changed', 'uses' => function () {
                return 'yes';
            }]);
            $router->switchUrlLocale('en', $route);
            $router->refreshRoutes();
        });

        $router->group(['prefix' => 'fr'], function () use ($router) {
            $router->get('root/{slug}', [
                'locale' => 'en', 'as' => 'root', 'add_locale_to_url' => false, 'uses' => function () {
                    return 'yes';
                },
            ]);
        });

        $expected = [
            'fr.plain' => 'fr/plain/{slug}',
            'fr.disabled' => 'api/disabled/{slug}',
            'fr.changed' => 'en/api/changed/{slug}',
            'en.root' => 'root/{slug}',
        ];
        $urls = [];
        foreach ($expected as $name => $uri) {
            $this->assertSame($uri, $router->getRoutes()->getByName($name)->uri());
            $urls[$name] = route($name, ['slug' => 'example']);
        }

        app('router')->setCompiledRoutes($router->getRoutes()->compile());
        foreach ($expected as $name => $uri) {
            $this->assertSame($uri, app('router')->getRoutes()->getByName($name)->uri());
            $this->assertSame($urls[$name], route($name, ['slug' => 'example']));
        }
    }

    public function testGetInLocaleGroup()
    {
        $router = $this->router;

        $router->group(['locale' => 'fr', 'as' => 'group.', 'prefix' => 'group'], function () use ($router) {
            $router->get('test', ['as' => 'route', 'uses' => function () {
                return 'yé!';
            }]);
        });

        $response = $this->call('GET', 'fr/group/test');
        $this->assertSame(200, $response->getStatusCode());

        $response = $this->call('GET', route('fr.group.route'));
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testLocaleGetInLocaleGroupTakesGetLocale()
    {
        $router = $this->router;

        $router->group(['locale' => 'fr', 'as' => 'group.', 'prefix' => 'group'], function () use ($router) {
            $router->get('test', ['locale' => 'en', 'as' => 'route', 'uses' => function () {
                return 'yé!';
            }]);
        });

        $response = $this->call('GET', 'en/group/test');
        $this->assertSame(200, $response->getStatusCode());

        $response = $this->call('GET', route('en.group.route'));
        $this->assertSame(200, $response->getStatusCode());
    }
}
