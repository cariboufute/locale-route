<?php

namespace Tests\Functional\Routing;

use CaribouFute\LocaleRoute\Facades\LocaleRoute;
use CaribouFute\LocaleRoute\TestHelpers\EnvironmentSetUp;
use Orchestra\Testbench\TestCase;

class ResourceRegistrarTest extends TestCase
{
    use EnvironmentSetUp;

    public function testResource()
    {
        LocaleRoute::resource('article', 'ArticleController');
        $routeInfo = $this->getRouteInfo();

        $this->assertLocaleIndexRoutes($routeInfo);
        $this->assertLocaleCreateRoutes($routeInfo);
        $this->assertLocaleShowRoutes($routeInfo);
        $this->assertLocaleEditRoutes($routeInfo);

        $this->assertStoreRoute($routeInfo);
        $this->assertUpdateRoute($routeInfo);
        $this->assertDeleteRoute($routeInfo);
    }

    public function testResourceWithExcept()
    {
        LocaleRoute::resource('article', 'ArticleController', ['except' => 'index']);

        $routeInfo = $this->getRouteInfo();

        $this->assertLocaleIndexRoutes($routeInfo, false);
        $this->assertLocaleCreateRoutes($routeInfo);
        $this->assertLocaleShowRoutes($routeInfo);
        $this->assertLocaleEditRoutes($routeInfo);

        $this->assertStoreRoute($routeInfo);
        $this->assertUpdateRoute($routeInfo);
        $this->assertDeleteRoute($routeInfo);
    }

    public function testResourceWithOnly()
    {
        LocaleRoute::resource('article', 'ArticleController', ['only' => ['create', 'edit']]);

        $routeInfo = $this->getRouteInfo();

        $this->assertLocaleIndexRoutes($routeInfo, false);
        $this->assertLocaleCreateRoutes($routeInfo);
        $this->assertLocaleShowRoutes($routeInfo, false);
        $this->assertLocaleEditRoutes($routeInfo);

        $this->assertStoreRoute($routeInfo, false);
        $this->assertUpdateRoute($routeInfo, false);
        $this->assertDeleteRoute($routeInfo, false);
    }

    public function assertLocaleIndexRoutes($routeInfo, $contains = true)
    {
        $this->assertLocaleRoutes($routeInfo, 'index', '', $contains);
    }

    public function assertLocaleCreateRoutes($routeInfo, $contains = true)
    {
        $this->assertLocaleRoutes($routeInfo, 'create', '/create', $contains);
    }

    public function assertLocaleShowRoutes($routeInfo, $contains = true)
    {
        $this->assertLocaleRoutes($routeInfo, 'show', '/{article}', $contains);
    }

    public function assertLocaleEditRoutes($routeInfo, $contains = true)
    {
        $this->assertLocaleRoutes($routeInfo, 'edit', '/{article}/edit', $contains);
    }

    public function assertLocaleRoutes($routeInfo, $name, $uri = "", $contains = true)
    {
        foreach ($this->locales as $locale) {
            $this->assertLocaleRoute($routeInfo, $locale, $name, $uri, $contains);
        }
    }

    public function getAssertVerb($contains)
    {
        return $contains ? 'assertContains' : 'assertNotContains';
    }

    public function assertLocaleRoute($routeInfo, $locale, $name, $uri = "", $contains = true)
    {
        $assert = $this->getAssertVerb($contains);

        $this->$assert([
            "methods" => [
                0 => "GET",
                1 => "HEAD",
            ],
            "name" => $locale . ".article." . $name,
            "uri" => $locale . "/article" . $uri,
        ], $routeInfo);
    }

    public function assertStoreRoute($routeInfo, $contains = true)
    {
        $this->assertRoute($routeInfo, ['POST'], 'store', '', $contains);
    }

    public function assertUpdateRoute($routeInfo, $contains = true)
    {
        $this->assertRoute($routeInfo, ['PUT', 'PATCH'], 'update', '/{article}', $contains);
    }

    public function assertDeleteRoute($routeInfo, $contains = true)
    {
        $this->assertRoute($routeInfo, ['DELETE'], 'destroy', '/{article}', $contains);
    }

    public function assertRoute($routeInfo, $methods, $name, $uri, $contains = true)
    {
        $assert = $this->getAssertVerb($contains);

        $this->$assert([
            "methods" => $methods,
            "name" => "article." . $name,
            "uri" => "article" . $uri,
        ], $routeInfo);
    }
}
