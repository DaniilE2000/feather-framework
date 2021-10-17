<?php 

namespace application\components\native\core\base;

/**
 * Provides common functions for Feather Objects.
 */
abstract class BaseObject
{
    /**
     * Implements getter logic in form of: 
     * you can declare public method as 'getXXX' ({@example ```getCountOfActions()```})
     * and access it as 'xXX' ({@example ```$obj->countOfActions```}).
     * 
     * @param string $var A property name (a getter function name with omitted 'get'
     *                    and with first letter undercased).
     * 
     * @return mixed A getter function execution result.
     * @throws Exception If there is no such getter function.
     */
    function __get(string $var): mixed
    {
        $varQuery = 'get' . \ucfirst($var);
        if (\method_exists(\static::class, $varQuery)) {
            return \call_user_func([$this, $varQuery]);
        } else {
            throw new \Exception('There is no ' . $var . ' in class ' . \static::class);
        }
    }

    /**
     * Implements setter logic in form of:
     * you can declare public method as 'setXXX' ({@example ```setCountOfActions()```})
     * and access it as 'xXX' ({@example ```$obj->countOfActions = 3```}).
     * 
     * @param string $var A property name (a setter function name with omitted 'set'
     *                    and with first letter undercased).
     * 
     * @return void
     * @throws Exception If there is no such setter function.
     */
    function __set(string $var, mixed $val): void
    {
        $varQuery = 'set' . \ucfirst($var);
        if (\method_exists(\static::class, $varQuery)) {
            \call_user_func([$this, $varQuery], $val);
        } else {
            throw new \Exception('There is no ' . $var . ' in class ' . \static::class);
        }
    }
}