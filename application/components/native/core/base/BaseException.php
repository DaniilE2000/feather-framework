<?php

namespace application\components\native\core\base;

use Exception;

/**
 * Provides common functions for feather exceptions.
 */
abstract class BaseException extends Exception {

    /**
     * Prints out exception (obviously).
     */
    public function __toString(): string
    {
        $error = $this->getMessage();
        $class = self::class;
        $trace = $this->getTraceAsString();

        return <<<ERROR
        Error: $error.
        Type: $class.
        Trace: $trace.
        ERROR;
    }
}