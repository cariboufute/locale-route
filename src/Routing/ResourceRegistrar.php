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

    protected function addResourceIndex($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name);
        $rawRouteName = $this->getResourceName($name, 'index', $options);
        $action = $this->getLocaleResourceAction($controller, 'index');

        return $this->localeRouter->get($rawRouteName, $action, $uri);
    }

    protected function getLocaleResourceAction($controller, $method)
    {
        return $controller . '@' . $method;
    }

}
