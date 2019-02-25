<?php

namespace CaribouFute\LocaleRoute\Routing;

use CaribouFute\LocaleRoute\LocaleConfig;
use Illuminate\Routing\ResourceRegistrar as IlluminateResourceRegistrar;
use Illuminate\Routing\Router as IlluminateRouter;
use Illuminate\Support\Str;
use Illuminate\Translation\Translator;

class ResourceRegistrar extends IlluminateResourceRegistrar
{
    protected $localeConfig;
    protected $localeRouter;
    protected $translator;

    public function __construct(
        LocaleConfig $localeConfig,
        LocaleRouter $localeRouter,
        IlluminateRouter $router,
        Translator $translator
    ) {
        $this->localeConfig = $localeConfig;
        $this->localeRouter = $localeRouter;
        $this->translator = $translator;

        parent::__construct($router);
    }

    protected function getLocaleResourceAction($controller, $method)
    {
        return $controller . '@' . $method;
    }

    protected function addResourceIndex($name, $base, $controller, $options): RouteCollection
    {
        $uri = $this->getResourceUri($name);
        $name = $this->getResourceRouteName($name, 'index', $options);
        $action = $this->getLocaleResourceAction($controller, 'index');

        return $this->localeRouter->get($name, $action, $uri);
    }

    protected function addResourceCreate($name, $base, $controller, $options): RouteCollection
    {
        $base = $this->getResourceUri($name);
        $uris = $this->getLocaleUris($base, 'create', $options);
        $name = $this->getResourceRouteName($name, 'create', $options);
        $action = $this->getLocaleResourceAction($controller, 'create');

        return $this->localeRouter->get($name, $action, $uris);
    }

    protected function getLocaleUris($base, $label, $options)
    {
        $uris = [];

        foreach ($this->localeConfig->locales($options) as $locale) {
            $uris[$locale] = $base . '/' . $this->getTranslation($locale, $label);
        }

        return $uris;
    }

    protected function getTranslation($locale, $label)
    {
        $untranslated = 'route-labels.' . $label;
        $translated = $this->translator->get($untranslated, [], $locale);
        return $translated === $untranslated ? $label : $translated;
    }

    protected function addResourceShow($name, $base, $controller, $options): RouteCollection
    {
        $uri = $this->getResourceUri($name) . '/{' . $base . '}';
        $name = $this->getResourceRouteName($name, 'show', $options);
        $action = $this->getLocaleResourceAction($controller, 'show');

        return $this->localeRouter->get($name, $action, $uri);
    }

    protected function addResourceEdit($name, $base, $controller, $options): RouteCollection
    {
        $base = $this->getResourceUri($name) . '/{' . $base . '}';
        $uris = $this->getLocaleUris($base, 'edit', $options);
        $name = $this->getResourceRouteName($name, 'edit', $options);
        $action = $this->getLocaleResourceAction($controller, 'edit');

        return $this->localeRouter->get($name, $action, $uris);
    }

    /**
     * Route a resource to a controller.
     *
     * @param  string $name
     * @param  string $controller
     * @param  array $options
     * @return \Illuminate\Routing\RouteCollection
     */
    public function register($name, $controller, array $options = [])
    {
        if (isset($options['parameters']) && !isset($this->parameters)) {
            $this->parameters = $options['parameters'];
        }

        // If the resource name contains a slash, we will assume the developer wishes to
        // register these resource routes with a prefix so we will set that up out of
        // the box so they don't have to mess with it. Otherwise, we will continue.
        if (Str::contains($name, '/')) {
            $this->prefixedResource($name, $controller, $options);

            return;
        }

        // We need to extract the base resource from the resource name. Nested resources
        // are supported in the framework, but we need to know what name to use for a
        // place-holder on the route parameters, which should be the base resources.
        $base = $this->getResourceWildcard(last(explode('.', $name)));

        $defaults = $this->resourceDefaults;

        $collection = new RouteCollection;

        foreach ($this->getResourceMethods($defaults, $options) as $m) {
            $collection = $this->registerMethod($m, $name, $base, $controller, $options, $collection);
        }

        return $collection;
    }

    protected function registerMethod(
        string $method,
        $name,
        $base,
        $controller,
        $options,
        RouteCollection $collection
    ): RouteCollection {
        $addResourceMethod = 'addResource' . ucfirst($method);
        $routeCollection = $this->$addResourceMethod($name, $base, $controller, $options);

        $routeArray = is_a($routeCollection, RouteCollection::class) ?
            $routeCollection->getRoutes() :
            [$routeCollection];

        foreach ($routeArray as $route) {
            $collection->add($route);
        }

        return $collection;
    }
}
