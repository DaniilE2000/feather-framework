<?php 

ini_set('display_errors', 1);
error_reporting(E_ALL);

/**
 * Prints data in variable ```$str``` in human-readable format, 
 * then shuts execution down.
 * 
 * @param mixed $str A data to print out.
 * 
 * @return void
 */
function debug(mixed $data): void
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    exit;
}