<?php

namespace CaribouFute\LocaleRoute\Routing;

use Illuminate\Routing\Route;
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

    public function remove(Route $route)
    {
        $this->removeFromCollections($route);
        $this->removeLookups($route);
    }

    protected function removeFromCollections(Route $route)
    {
        $domainAndUri = $route->domain() . $route->getUri();

        foreach ($route->methods() as $method) {
            unset($this->routes[$method][$domainAndUri]);
        }

        unset($this->allRoutes[$method . $domainAndUri]);
    }

    protected function removeLookups(Route $route)
    {
        $action = $route->getAction();

        if (isset($action['as'])) {
            unset($this->nameList[$action['as']]);
        }

        if (isset($action['controller'])) {
            $this->removeFromActionList($action, $route);
        }
    }

    protected function removeFromActionList($action)
    {
        unset($this->actionList[trim($action['controller'], '\\')]);
    }
}
