<?php

namespace CaribouFute\LocaleRoute\Middleware;

use Closure;
use Lang;

class TranslateUrlParameters
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->route()->parameters() && Lang::has('routes.!parameters')) {
            $parameters = $request->route()->parameters();
            $translated_parameters = Lang::get('routes.!parameters');
            foreach ($parameters as $key => $value) {
                if ($parameter = array_search($value, $translated_parameters)) {
                    $request->route()->setParameter($key, $parameter);
                }
            }
        }

        return $next($request);
    }
}
