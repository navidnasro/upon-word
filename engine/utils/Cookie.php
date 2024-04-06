<?php

namespace engine\utils;

use engine\security\Sanitize;

defined('ABSPATH') || exit;

class Cookie
{
    /**
     * Sets a cookie
     *
     * @param string $cookieName
     * @param mixed $value
     * @param int $expire
     * @param bool $secure
     * @param string $path
     * @return bool
     */
    public static function set(string $cookieName,mixed $value,int $expire,bool $secure = false,string $path = '/'): bool
    {
        $value = base64_encode(serialize(Sanitize::variable($value)));

        $options = [
            'expires' => $expire,
            'path' => $path,
        ];

        // if cookie must be highly secured
        if ($secure)
        {
            $options['httponly'] = true;

            if (isset($_SERVER['HTTPS']))
                $options['secure'] = true;
        }

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
        return unserialize(Sanitize::text(wp_unslash(base64_decode($_COOKIE[$cookieName]))));
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