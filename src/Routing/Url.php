<?php

namespace CaribouFute\LocaleRoute\Routing;

use App;
use Config;
use Illuminate\Routing\Router as IlluminateRouter;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Translation\Translator;

class Url
{
    protected $url;
    protected $router;
    protected $translator;

    public function __construct(UrlGenerator $url, IlluminateRouter $router, Translator $translator)
    {
        $this->url = $url;
        $this->router = $router;
        $this->translator = $translator;
    }

    public function locales()
    {
        return Config::get('localeroute.locales');
    }

    public function addLocaleConfig()
    {
        return Config::get('localeroute.add_locale_to_url');
    }

    public function getRouteUrl($locale, $route, array $urls = [])
    {
        $unlocaleUrl = isset($urls[$locale]) ? $urls[$locale] : $this->translator->get('routes.' . $route, [], $locale);
        $url = $this->addLocale($locale, $unlocaleUrl);

        return $url;
    }

    public function addLocale($locale, $url)
    {
        return $this->addLocaleConfig() ? $locale . '/' . $url : $url;
    }

    public function removeLocale($locale, $url)
    {
        return $this->addLocaleConfig() ? str_replace($locale . '/', '', $url) : $url;
    }

    public function localeRoute($locale = null, $name = null, $parameters = [], $absolute = true)
    {
        $locale = $locale ?? App::getLocale();
        $name = $name ?? $this->router->currentRouteName();

        $localeRoute = $this->switchRouteLocale($locale, $name);
        $localeUrl = $this->url->route($localeRoute, $parameters, $absolute);

        return $localeUrl;
    }

    public function switchRouteLocale($locale, $route)
    {
        $unlocaleRoute = $this->removeLocaleFromRouteName($route);
        $localeRoute = $this->addLocaleToRouteName($locale, $unlocaleRoute);

        return $localeRoute;
    }

    public function removeLocaleFromRouteName($route)
    {
        $localePrefix = $this->getRouteNameLocalePrefix($route);
        $unlocaleRoute = str_replace($localePrefix, '', $route);

        return $unlocaleRoute;
    }

    protected function getRouteNameLocalePrefix(string $route)
    {
        foreach ($this->locales() as $locale) {
            $localePrefix = $locale . '.';

            if (strpos($route, $localePrefix) === 0) {
                return $localePrefix;
            }
        }

        return '';
    }

    public function addLocaleToRouteName($locale, $route)
    {
        return $locale . '.' . $route;
    }

}
