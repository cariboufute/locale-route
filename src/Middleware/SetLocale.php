<?php

namespace CaribouFute\LocaleRoute\Middleware;

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
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        app()->setLocale($this->sessionLocale->get());

        return $next($request);
    }

}
