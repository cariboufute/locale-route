<?php

namespace CaribouFute\LocaleRoute\Routing;

use CaribouFute\LocaleRoute\Routing\LocaleRouter;
use CaribouFute\LocaleRoute\Traits\ConfigParams;
use Illuminate\Routing\ResourceRegistrar as IlluminateResourceRegistrar;
use Illuminate\Routing\Router as IlluminateRouter;
use Illuminate\Translation\Translator;

class ResourceRegistrar extends IlluminateResourceRegistrar
{
    use ConfigParams;

    protected $localeRouter;
    protected $translator;

    public function __construct(LocaleRouter $localeRouter, IlluminateRouter $router, Translator $translator)
    {
        $this->localeRouter = $localeRouter;
        $this->translator = $translator;

        parent::__construct($router);
    }

    protected function getLocaleResourceAction($controller, $method)
    {
        return $controller . '@' . $method;
    }

    /**
     *  Get resource name for both Laravel 5.4 (getResourceRouteName) and Laravel <5.4 (getResourceName)
     *  @param $resource: the resource name
     *  @param $method: the controller method
     *  @param $options: different options
     *  @return the route name
     */
    protected function getResourceName($resource, $method, $options)
    {
        if (method_exists($this, 'getResourceRouteName')) {
            return $this->getResourceRouteName($resource, $method, $options);
        } else {
            return parent::getResourceName($resource, $method, $options);
        }
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
        $base = $this->getResourceUri($name);
        $uris = $this->getLocaleUris($base, 'create', $options);
        $name = $this->getResourceName($name, 'create', $options);
        $action = $this->getLocaleResourceAction($controller, 'create');

        return $this->localeRouter->get($name, $action, $uris);
    }

    protected function getLocaleUris($base, $label, $options)
    {
        $uris = [];

        foreach ($this->locales($options) as $locale) {
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

    protected function addResourceShow($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name) . '/{' . $base . '}';
        $name = $this->getResourceName($name, 'show', $options);
        $action = $this->getLocaleResourceAction($controller, 'show');

        return $this->localeRouter->get($name, $action, $uri);
    }

    protected function addResourceEdit($name, $base, $controller, $options)
    {
        $base = $this->getResourceUri($name) . '/{' . $base . '}';
        $uris = $this->getLocaleUris($base, 'edit', $options);
        $name = $this->getResourceName($name, 'edit', $options);
        $action = $this->getLocaleResourceAction($controller, 'edit');

        return $this->localeRouter->get($name, $action, $uris);
    }

}
