<?php

namespace application\components\native\core\base\interfaces;

use application\components\native\core\routing\Route;

/**
 * Interface providing the common controller behaviour contract.
 * 
 * You can define a custom controller class by implementing ```IControllerLike``` interface.
 */
interface IControllerLike
{
    /** 
     * Controller object initialization.
     * 
     * @param Route $route A route object passed by router.
     */
    public function __construct(Route $route);

    /** 
     * Action query processing.
     * 
     * @param string $actionName A name of the action to invoke.
     * 
     * @return string HTML markup.
     */
    public function action(string $actionName): string;
    
    /** 
     * Rendering a response using views.
     * 
     * @param string $view A name of the view file.
     * @param string $title [optional] A page title.
     * @param array $args [optional] Parameters passed from action.
     */
    public function render(string $view, string $title = '', array $args = []): string;
}

?>