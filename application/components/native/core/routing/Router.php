<?php

namespace application\components\native\core\routing;

use application\components\native\core\base\UtilityController;
use application\controllers;

class Router
{
    protected Route $route;

    public function match()
    {
        $url = trim($_SERVER['REQUEST_URI'], '/');
        // debug($url);
        foreach($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    public function notFound()
    {
        return (new UtilityController($this->route))->action('notFound');
    }

    public function run(): void
    {
        $this->route = new Route($_SERVER['REQUEST_URI']);
        if ($this->route->illFormed) {
            die($this->notFound());
        }
        /** @var \application\components\native\core\base\interfaces\IControllerLike $controller */
        $controller = new ($this->route->path)($this->route);
        echo $controller->action($this->route->action);
    }
}
