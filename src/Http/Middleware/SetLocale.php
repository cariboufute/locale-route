<?php

namespace CaribouFute\LocaleRoute\Http\Middleware;

use CaribouFute\LocaleRoute\Session\Locale as SessionLocale;
use Closure;

class SetLocale
{
    protected $sessionLocale;

    public function __construct(SessionLocale $sessionLocale)
    {
        $this->sessionLocale = $sessionLocale;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $locale)
    {
        $this->sessionLocale->set($locale);

        return $next($request);
    }

}
