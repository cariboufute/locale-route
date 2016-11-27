<?php

namespace CaribouFute\LocaleRoute\Routing;

use CaribouFute\LocaleRoute\Routing\LocaleRouter;
use Illuminate\Routing\ResourceRegistrar as IlluminateResourceRegistrar;
use Illuminate\Routing\Router as IlluminateRouter;

class ResourceRegistrar extends IlluminateResourceRegistrar
{
    protected $localeRouter;

    public function __construct(LocaleRouter $localeRouter, IlluminateRouter $router)
    {
        $this->localeRouter = $localeRouter;
        parent::__construct($router);
    }

    protected function getLocaleResourceAction($controller, $method)
    {
        return $controller . '@' . $method;
    }

    protected function addResourceIndex($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name);
        $name = $this->getResourceName($name, 'index', $options);
        $action = $this->getLocaleResourceAction($controller, 'index');

        return $this->localeRouter->get($name, $action, $uri);
    }

    protected function addResourceCreate($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name) . '/create';
        $name = $this->getResourceName($name, 'create', $options);
        $action = $this->getLocaleResourceAction($controller, 'create');

        return $this->localeRouter->get($name, $action, $uri);
    }

    protected function addResourceShow($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name) . '/{' . $base . '}';
        $name = $this->getResourceName($name, 'show', $options);
        $action = $this->getLocaleResourceAction($controller, 'show');

        return $this->localeRouter->get($name, $action, $uri);
    }

    protected function addResourceEdit($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name) . '/{' . $base . '}/edit';
        $name = $this->getResourceName($name, 'edit', $options);
        $action = $this->getLocaleResourceAction($controller, 'edit');

        return $this->localeRouter->get($name, $action, $uri);
    }

}
