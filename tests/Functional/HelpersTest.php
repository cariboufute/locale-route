<?php

namespace Tests\Functional;

use CaribouFute\LocaleRoute\Facades\LocaleRoute;
use CaribouFute\LocaleRoute\TestHelpers\EnvironmentSetUp;
use Orchestra\Testbench\TestCase;
use Session;

class HelpersTest extends TestCase
{
    use EnvironmentSetUp;

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

    public function testLocaleRouteWithNoParametersReturnsNoParameters()
    {
        LocaleRoute::get('article.show', function () {
            return 'route';
        }, ['fr' => 'article/{id}', 'en' => 'article/{id}']);

        $response = $this->call('get', 'fr/article/2');

        $this->assertSame(url('fr/article'), locale_route('fr', 'article.show'));
        $this->assertSame(url('en/article'), locale_route('en', 'article.show'));
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
