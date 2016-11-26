<?php

namespace CaribouFute\LocaleRoute\Routing;

use Illuminate\Routing\RouteCollection as IlluminateRouteCollection;

class RouteCollection extends IlluminateRouteCollection
{
    public function hydrate(IlluminateRouteCollection $coll)
    {
        $this->routes = $coll->routes;
        $this->allRoutes = $coll->allRoutes;
        $this->nameList = $coll->nameList;
        $this->actionList = $coll->actionList;
    }

    public function refresh()
    {
        $routes = $this->getRoutes();

        $this->routes = [];
        $this->allRoutes = [];
        $this->nameList = [];
        $this->actionList = [];

        foreach ($routes as $route) {
            $this->add($route);
        }
    }
}
