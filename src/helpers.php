<?php

if (!function_exists('locale_route')) {
    /**
     * Generate the URL to a named route.
     *
     * @param  string  $name
     * @param  array   $parameters
     * @param  bool    $absolute
     * @return string
     */
    function locale_route($locale = null, $name = null, $parameters = [], $absolute = true)
    {
        return app('locale-url')->route($name, $parameters, $absolute);
    }
}
