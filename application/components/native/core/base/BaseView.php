<?php

namespace application\components\native\core\base;

use application\components\native\core\exceptions\RouteException;
use application\components\native\core\routing\Route;


/**
 * Standard view class for Feather.
 * 
 * You can override standard view class by defining custom controller 
 * class and injecting your custom view class dependencies into it.
 */
class BaseView
{
    /** @var string $filePath A path to the view file. */
    public string $filePath;
    /** @var string $layout A path to the used layout. */
    public string $layoutPath;
    /** @var string $title The web page title. */
    public string $title;
    /** @var array $args Arguments, passed for view rendering. */
    public array $args;

    /** Assigning class fields (except for ```$this->layout```). */
    public function __construct(string $viewsFolder, string $viewPath, string $pageTitle, array $args = [])
    {
        $this->args = $args;
        $this->title = $pageTitle;
        $this->filePath = $viewsFolder . '\\' . $viewPath . '.php';
    }

    /** 
     * Assigning ```$this->layout```.
     * 
     * @param string $layout Layout's name.
     * 
     * @return static ```$this```
     */
    public function setLayout(string $layout): static
    {
        $this->layoutPath = 'application/layouts/' . $layout . '.php';
        return $this;
    }

    /**
     * Renders needed page.
     * 
     * @return string HTML markup.
     * 
     * @throws RouteException {@see ```$this->checkFiles```}
     */
    public function render(): string
    {
        $this->checkFiles();

        $title = $this->title;
        \extract($this->args);

        \ob_start();
        require $this->filePath;
        $content = \ob_get_clean();

        \ob_start();
        require $this->layoutPath;
        $page = \ob_get_clean();

        return $page;
    }

    /**
     * Checking if assigned file paths are valid.
     * 
     * @return void
     * 
     * @throws RouteException If invalid file path was found.
     */
    private function checkFiles(): void
    {
        if (!\file_exists($this->filePath)) {
            throw new RouteException('There is no file by path ' . $this->filePath . ', view file expected.');
        }

        if (!\file_exists($this->layoutPath)) {
            throw new RouteException('There is no file by path ' . $this->layoutPath . ', layout file expected.');
        }

        return;
    }
}

?>