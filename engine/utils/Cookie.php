<?php

namespace engine\utils;

defined('ABSPATH') || exit;

class Cookie
{
    /**
     * Sets a cookie
     *
     * @param string $cookieName
     * @param mixed $value
     * @param int $expire
     * @param string $path
     * @return bool
     */
    public static function set(string $cookieName,mixed $value,int $expire,string $path = '/'): bool
    {
        $value = base64_encode(serialize($value));

        $options = [
            'expires' => $expire,
            'path' => $path,
            'httponly' => true,
        ];

        if (isset($_SERVER['HTTPS']))
            $options['secure'] = true;

        return setcookie($cookieName,$value,$options);
    }

    /**
     * Returns a cookie
     *
     * @param string $cookieName
     * @return string|null
     */
    public static function get(string $cookieName): mixed
    {
        if (!self::exists($cookieName))
            return null;

        // decode , unslash , sanitize , unserialize
        return unserialize(sanitize_text_field(wp_unslash(base64_decode($_COOKIE[$cookieName]))));
    }

    /**
     * Checks for cookie existence
     *
     * @param string $cookieName
     * @return bool
     */
    public static function exists(string $cookieName): bool
    {
        return isset($_COOKIE[$cookieName]);
    }
}