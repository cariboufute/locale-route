<?php

namespace Tests\Functional;

use CaribouFute\LocaleRoute\Facades\LocaleRoute;
use CaribouFute\LocaleRoute\Locale\Route as LocaleRouteUrl;
use CaribouFute\LocaleRoute\Routing\Router;
use Orchestra\Testbench\TestCase;
use Session;

class HelpersTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $this->locales = ['fr', 'en'];
        $this->addLocaleOption = true;

        $app['config']->set('localeroute.locales', $this->locales);
        $app['config']->set('localeroute.add_locale_to_url', $this->addLocaleOption);
        $app['locale-route'] = app()->make(Router::class);
        $app['locale-route-url'] = app()->make(LocaleRouteUrl::class);
    }

    public function setUp()
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

    public function testOtherLocaleWithDefaultParameters()
    {
        LocaleRoute::get('article.show', function () {
            return 'route';
        }, ['fr' => 'article/{id}', 'en' => 'article/{id}']);

        $response = $this->call('get', 'fr/article/2');

        $this->assertSame(url('fr/article/2'), other_locale('fr'));
        $this->assertSame(url('en/article/2'), other_locale('en'));
    }

    public function testAnotherRouteWithEmptyParameters()
    {
        LocaleRoute::get('article.show', function () {
            return 'route';
        }, ['fr' => 'article/{param}', 'en' => 'article/{param}']);

        LocaleRoute::get('articles', function () {
            return 'route';
        }, ['fr' => 'articles', 'en' => 'articles']);

        $response = $this->call('get', other_route('article.show', 'foobar'));

        $this->assertSame(url('en/articles'), other_route('articles', []));
    }

    public function testLocaleRouteWithDefaultParameters()
    {
        LocaleRoute::get('article.show', function () {
            return 'route';
        }, ['fr' => 'article/{id}', 'en' => 'article/{id}']);

        $response = $this->call('get', 'fr/article/2');

        $this->assertSame(url('fr/article/2'), locale_route('fr', 'article.show'));
        $this->assertSame(url('en/article/2'), locale_route('en', 'article.show'));
    }
}
