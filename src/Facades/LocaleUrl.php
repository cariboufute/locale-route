<?php

namespace CaribouFute\LocaleRoute\Facades;

use Illuminate\Support\Facades\Facade;

class LocaleUrl extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'locale-url';
    }
}
