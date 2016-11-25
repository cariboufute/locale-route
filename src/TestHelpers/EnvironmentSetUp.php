<?php

namespace CaribouFute\LocaleRoute\TestHelpers;

use CaribouFute\LocaleRoute\Middleware\SetSessionLocale;
use CaribouFute\LocaleRoute\Prefix\Route as PrefixRoute;
use CaribouFute\LocaleRoute\Routing\LocaleRouter;
use CaribouFute\LocaleRoute\Routing\SubRouter;

trait EnvironmentSetUp
{
    protected $locales;
    protected $addLocaleOption;

    protected function getEnvironmentSetUp($app)
    {
        $this->locales = ['fr', 'en'];
        $this->addLocaleOption = true;

        $app['config']->set('localeroute.locales', $this->locales);
        $app['config']->set('localeroute.add_locale_to_url', $this->addLocaleOption);
        $app['locale-route'] = app()->make(LocaleRouter::class);
        $app['locale-route-url'] = app()->make(PrefixRoute::class);
        $app['sub-route'] = app()->make(SubRouter::class);

        $app['router']->middleware('locale.session', SetSessionLocale::class);
    }
}
