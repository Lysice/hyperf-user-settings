<?php


if (! function_exists('array_set')) {
    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $value
     * @return array
     */
    function array_set(&$array, $key, $value)
    {
        return \Hyperf\Utils\Arr::set($array, $key, $value);
    }
}

if (! function_exists('array_get')) {
    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function array_get($array, $key, $default = null)
    {
        return \Hyperf\Utils\Arr::get($array, $key, $default);
    }
}


if (!function_exists('setting')) {
    /**
     * Helper function for Setting facade.
     * @param null $userId
     * @return \App\UserSettings\Setting
     */
    function setting($userId = null)
    {
        return new \Lysice\HyperfUserSettings\Setting($userId);
    }
}
