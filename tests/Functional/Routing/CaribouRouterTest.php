<?php

namespace Tests\Functional\Routing;

use CaribouFute\LocaleRoute\Routing\CaribouRouter;
use CaribouFute\LocaleRoute\TestHelpers\EnvironmentSetUp;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;

class CaribouRouterTest extends TestCase
{
    use EnvironmentSetUp;

    public function setUp()
    {
        parent::setUp();
        $this->router = app()->make(CaribouRouter::class);
    }

    public function testGet()
    {
        $this->router->get('test', ['locale' => 'fr', 'as' => 'route', 'uses' => function () {return 'yé!';}]);

        $this->call('GET', 'fr/test');
        $this->assertResponseOk();

        $this->call('GET', route('fr.route'));
        $this->assertResponseOk();
    }

    public function testGetInGroup()
    {
        $router = $this->router;

        $router->group(['as' => 'group.', 'prefix' => 'group'], function () use ($router) {
            $router->get('test', ['locale' => 'fr', 'as' => 'route', 'uses' => function () {return 'yé!';}]);
        });

        $this->call('GET', 'fr/group/test');
        $this->assertResponseOk();

        $this->call('GET', route('fr.group.route'));
        $this->assertResponseOk();
    }

    public function testGetInLocaleGroup()
    {
        $router = $this->router;

        $router->group(['locale' => 'fr', 'as' => 'group.', 'prefix' => 'group'], function () use ($router) {
            $router->get('test', ['as' => 'route', 'uses' => function () {return 'yé!';}]);
        });

        $this->call('GET', 'fr/group/test');
        $this->assertResponseOk();

        $this->call('GET', route('fr.group.route'));
        $this->assertResponseOk();
    }

    public function testLocaleGetInLocaleGroupTakesGetLocale()
    {
        $router = $this->router;

        $router->group(['locale' => 'fr', 'as' => 'group.', 'prefix' => 'group'], function () use ($router) {
            $router->get('test', ['locale' => 'en', 'as' => 'route', 'uses' => function () {return 'yé!';}]);
        });

        $this->call('GET', 'en/group/test');
        $this->assertResponseOk();

        $this->call('GET', route('en.group.route'));
        $this->assertResponseOk();
    }
}
