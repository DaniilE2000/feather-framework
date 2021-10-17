<?php

namespace application\components\native\core\routing;

use application\components\native\core\base\BaseObject;
use application\components\native\core\exceptions\RouteException;
use application\components\native\helpers\ArrayHelper;

class Route extends BaseObject
{
    private const PARTS_KEYS = [
        'action',
        'controller',
        'moduleOrFolder',
        'modules',
    ];

    private array $parsedParts;
    public array $queryParameters = [];
    public string $url;
    public bool $illFormed = false;
    public string $path;

    public function getAction()
    {
        return $this->parsedParts['action'];
    }

    public function __construct(string $url)
    {
        $this->url = \parse_url($url, PHP_URL_PATH);
        $this->parsedParts = $this->parse();
        $queryString = \parse_url($url, PHP_URL_QUERY) ?? '';
        if (!empty($queryString)) {
            parse_str($queryString, $this->queryParameters);
        }
        $this->path = $this->resolve();
    }

    private function parse(): array
    {
        $parts = \explode('/', trim($this->url, '/'));
        $parts = array_map([$this, 'normalize'], $parts);
        $parsedParts = ArrayHelper::combine(self::PARTS_KEYS, array_reverse($parts));
        
        if (is_array($parsedParts['modules'])) {
            $parsedParts['modules'] = array_reverse($parsedParts['modules']);
        }

        return $parsedParts;
    }

    public function normalize(string $urlPart)
    {
        return lcfirst(join('',
        \array_map(function($piece) {
            return \ucfirst($piece);
        }, \explode('-', $urlPart))));
    }

    /**
     * Composes a filesystem route from ```$parsedRouteArray``` elements.
     * 
     * @return string A valid filesystem route.
     * @throws RouteException If there's no valid filesystem route that 
     *                        matches provided elements configuration.
     */
    private function resolve(): string 
    {
        \extract($this->parsedParts);
        if(empty($controller)) {
            // throw new RouteException('Ill-formed url: ' . $this->url);
            $this->illFormed = true;
            return '';
        }
        $controller = ucfirst($controller) . 'Controller';
        $modules = $this->getModulesString();

        $pathPrefix = 'application\\';
        $pathOptions;

        // if ```$moduleOrFolder``` is folder (or there aren't any modules).
        $pathOptions['MOFisFolder'] = $pathPrefix . \trim(\join('\\', [$modules, 'controllers', $moduleOrFolder, $controller]), '\\');
        $pathOptions['MOFisFolder'] = strtr($pathOptions['MOFisFolder'], ['\\\\' => '\\']);
        if (class_exists($pathOptions['MOFisFolder'])) {
            return $pathOptions['MOFisFolder'];
        }

        if (!empty($moduleOrFolder)) {
            // if ```$moduleOrFolder``` is module.
            $pathOptions['MOFisModule'] = $pathPrefix . \trim(\join('\\', [$modules, 'modules', $moduleOrFolder, 'controllers', $controller]), '\\');
            $pathOptions['MOFisFolder'] = strtr($pathOptions['MOFisFolder'], ['\\\\' => '\\']);
            if (class_exists($pathOptions['MOFisModule'])) {
                return $pathOptions['MOFisModule'];
            }
        }

        // throw new RouteException('Unable to resolve route. Assumed ' . \join(' and ', $pathOptions));
        $this->illFormed = true;
        return '';
    }

    private function getModulesString(): string
    {
        $modules = $this->parsedParts['modules'];
        if (!empty($modules)) {
            return 'modules\\' . (is_array($modules) ? \join('\\modules\\', $modules) : $modules);
        }

        return '';
    }
}
