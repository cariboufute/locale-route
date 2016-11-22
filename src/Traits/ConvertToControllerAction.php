<?php

namespace CaribouFute\LocaleRoute\Traits;

use Closure;

trait ConvertToControllerAction
{
    protected function convertToControllerAction($action)
    {
        return is_string($action) || is_a($action, Closure::class) ? ['uses' => $action] : $action;
    }
}
