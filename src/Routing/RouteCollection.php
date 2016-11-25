<?php

namespace CaribouFute\LocaleRoute\Routing;

use Illuminate\Routing\RouteCollection as IlluminateRouteCollection;

class RouteCollection extends IlluminateRouteCollection
{
    function clone (IlluminateRouteCollection $coll) {
        $this->routes = $coll->routes;
        $this->allRoutes = $coll->allRoutes;
        $this->nameList = $coll->nameList;
        $this->actionList = $coll->actionList;
    }
}
