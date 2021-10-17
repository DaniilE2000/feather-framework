<?php

namespace application\components\native\helpers;

/**
 * Consists of static utility array functions.
 */
class ArrayHelper 
{
    /* Array combining constants. */
    public const LAST_KEY_WRAP     = 0;
    public const VALUES_CUT        = 1;

    /**
     * Get the first element from the array.
     * 
     * @param array $array An array from which to get element.
     * 
     * @return mixed The first array element.
     */
    public static function first(array $array): mixed
    {
        return $array[array_key_first($array)];
    }


    /**
     * Get the last element from the array.
     * 
     * @param array $array An array from which to get element.
     * 
     * @return mixed The last array element.
     */
    public static function last(array $array): mixed
    {
        return $array[array_key_last($array)];
    }

    /**
     * Combines two arrays, treating them as keys and values respectively.
     * 
     * @param array $keys   An array representing keys.
     * @param array $values An array representing values.
     * @param int $keyPoliy An option setting how to treat entries from ```$values``` 
     *                      that exceedes ```$keys``` array count.
     *                      Available options:<br>
     *                          LAST_KEY_WRAP -- exceeding tail of ```$values``` will be gathered into
     *                          last key of ```$keys```.
     *                          VALUES_CUT    -- exceeding tail of ```$values``` will be trimmed.
     */
    public static function combine(
        array $keys,
        array $values,
        int $keyPolicy = self::LAST_KEY_WRAP
    ): array {
        $keysCount = count($keys);

        if ($keysCount >= count($values)) {
            return array_combine($keys, array_pad($values, $keysCount, null));
        }

        switch($keyPolicy) {
            case self::LAST_KEY_WRAP:
                $returnArray = array_combine
                    (array_slice($keys, 0, $keysCount - 1), array_slice($values, 0, $keysCount - 1));
                $returnArray[self::last($keys)] = array_slice($values, $keysCount - 1) ?: null;
                return $returnArray;
            case self::VALUES_CUT:
                $returnArray = array_combine($keys, array_slice($values, 0, $keysCount));
                return $returnArray;
        }
        return $values;
    }
}