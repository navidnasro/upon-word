<?php

namespace engine\utils;

defined('ABSPATH') || exit;

class Session
{
    /**
     * Stores a value in session
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function put(string $key,mixed $value): void
    {
        if (is_array($value))
            $value = serialize($value);

        $_SESSION[$key] = $value;
    }

    /**
     * Pops a value from session and returns it
     *
     * @param string $key
     * @return mixed
     */
    public static function pop(string $key): mixed
    {
        $value = $_SESSION[$key];
        unset($_SESSION[$key]);

        return is_serialized($value) ? unserialize($value) : $value;
    }

    /**
     * Returns a value from session
     *
     * @param string $key
     * @return mixed
     */
    public static function get(string $key): mixed
    {
        return is_serialized($_SESSION[$key]) ? unserialize($_SESSION[$key]) : $_SESSION[$key];
    }

    /**
     * Checks if a value exists in session
     *
     * @param string $key
     * @return bool
     */
    public static function exists(string $key): bool
    {
        return isset($_SESSION[$key]);
    }
}