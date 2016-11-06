<?php

namespace CaribouFute\LocaleRoute\Middleware;

use CaribouFute\LocaleRoute\Session\Locale as SessionLocale;
use Closure;

class SetSessionLocale
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
     * @param  \Closure     $next
     * @param  string       $locale
     * @return mixed
     */
    public function handle($request, Closure $next, $locale)
    {
        $this->sessionLocale->set($locale);

        return $next($request);
    }

}
