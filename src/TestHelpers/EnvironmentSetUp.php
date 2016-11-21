<?php

namespace CaribouFute\LocaleRoute\TestHelpers;

use CaribouFute\LocaleRoute\Locale\Route as LocaleRouteUrl;
use CaribouFute\LocaleRoute\Middleware\SetSessionLocale;
use CaribouFute\LocaleRoute\Routing\Router;

trait EnvironmentSetUp
{
    protected function getEnvironmentSetUp($app)
    {
        $this->locales = ['fr', 'en'];
        $this->addLocaleOption = true;

        $app['config']->set('localeroute.locales', $this->locales);
        $app['config']->set('localeroute.add_locale_to_url', $this->addLocaleOption);
        $app['locale-route'] = app()->make(Router::class);
        $app['locale-route-url'] = app()->make(LocaleRouteUrl::class);

        $app['router']->middleware('locale.session', SetSessionLocale::class);
    }
}
