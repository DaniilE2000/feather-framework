<?php

namespace application\components\native\core\base;

use application\components\native\core\exceptions\RouteException;
use application\components\native\core\base\BaseView;
use application\components\native\core\base\interfaces\IControllerLike;
use application\components\native\core\routing\Route;
use ReflectionClass;

/**
 * Provides common functions for Feather Controllers.
 * 
 * All custom controllers must implement ```IControllerLike``` interface.
 */
class BaseController implements IControllerLike
{
    /** @var string $layout Defines which layout to use. */
    public string $layout = 'default';
    /** @var Route $route */
    public Route $route;

    /** Assigning route. */
    public function __construct(Route $route)
    {
        $this->route = $route;
    }
    /**
     * Invokes controllers action by name passed in as first parameter.
     * 
     * @param string $actionName A name of the action.
     * @param mixed $args A spread array of action arguments, if any.
     * 
     * @return string An action result.
     * @throws RouteException If action isn't present.
     */
    public function action(string $actionName): string
    {
        $actionFullName = 'action' . \ucfirst($actionName);
        if (!\method_exists($this, $actionFullName)) {
            throw new RouteException(
                'There\'s no action named ' . $actionFullName . ' in controller ' . static::class
            );
        }
        $queryParameters = $this->route->queryParameters;
        if (!empty($queryParameters)) {
            return \call_user_func([$this, $actionFullName], ...$queryParameters);
        }
        return \call_user_func([$this, $actionFullName]);
    }

    /** 
     * {@inheritdoc}
     */
    public function render(string $view, string $title = '', array $args = []): string
    {
        if (empty($title)) {
            $title = \join(' ', \array_map(function($part) {
                return \ucfirst($part);
            }, \explode('-', $view)));
        }
        return (new BaseView($this->getClassViewsFolder(), $view, $title, $args))->setLayout($this->layout)->render();
    }

    /**
     * Retrieves views folder for specified controller.
     * 
     * @return string The folder path.
     */
    private function getClassViewsFolder(): string
    {
        $reflectorFileName = (new ReflectionClass(get_class($this)))->getFileName();
        // 11 is the string length of 'controller'
        $viewsDir = \substr(\dirname($reflectorFileName), 0, \strlen(\dirname($reflectorFileName)) - 11);
        $className = \lcfirst(\substr(\basename($reflectorFileName), 0, \strpos(\basename($reflectorFileName), 'Controller')));

        return $viewsDir . 'views\\' . $className;
    }
}

?>