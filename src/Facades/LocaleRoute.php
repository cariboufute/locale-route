<?php

namespace CaribouFute\LocaleRoute\Facades;

use Illuminate\Support\Facades\Facade;

class LocaleRoute extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'locale-route';
    }
}
