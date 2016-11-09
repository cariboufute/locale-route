<?php

if (!function_exists('locale_route')) {

    /**
     * Generate the URL to given locale and route name.
     *
     * @param  string  $locale
     * @param  string  $name
     * @param  array   $parameters
     * @param  bool    $absolute
     * @return string
     */
    function locale_route($locale = null, $name = null, $parameters = null, $absolute = true)
    {
        return app('locale-route-url')->localeRoute($locale, $name, $parameters, $absolute);
    }

    /**
     * Generate the URL for same route name but different locale
     *
     * @param  string  $locale
     * @param  array   $parameters
     * @param  bool    $absolute
     * @return string
     */
    function other_locale($locale, $parameters = null, $absolute = true)
    {
        return app('locale-route-url')->localeRoute($locale, null, $parameters, $absolute);
    }

    /**
     * Generate the URL for same route name but different locale
     *
     * @param  string  $name
     * @param  array   $parameters
     * @param  bool    $absolute
     * @return string
     */
    function other_route($name, $parameters = null, $absolute = true)
    {
        return app('locale-route-url')->localeRoute(null, $name, $parameters, $absolute);
    }
}
