<?php

require './application/components/native/lib/dev.php';
$config = require './application/components/custom/config/main.php';

use application\components\native\core\routing\Route;
use application\components\native\core\routing\Router;
use application\components\native\helpers\ArrayHelper;

spl_autoload_register(function($class) {
    $path = strtr($class . '.php', '\\', '/');
    if (file_exists($path)) {
        require $path;
    }
});

session_start();

class A {
    public static function new(...$args) 
    {
        return new self(...$args);
    }

    public static function sub(int $a, int $b)
    {
        return $a - $b;
    }

    public function __toString()
    {
        return '$a = ' . $this->a . ', $b = ' . $this->b; 
    }

    public function __construct(public int $a, public int $b) {}
}

$router = new Router();
$router->run();
