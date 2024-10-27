<?php

namespace engine\utils;

use engine\security\Cryptography;
use engine\security\Escape;
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
        $crypto = new Cryptography(true);
        $value = $crypto->encrypt(serialize(Sanitize::variable($value)));

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

        $crypto = new Cryptography(false);
        $value = $crypto->decrypt($_COOKIE[$cookieName]);

        // if cookie value is modified
        if ($value == -1)
        {
            unset($_COOKIE[$cookieName]); // remove the cookie

            wp_die(
                Escape::htmlWithTranslation('شما اجازه دسترسی به این بخش رو ندارید'),
                Escape::htmlWithTranslation('خطای امنیتی'),
                503
            );
        }

        // data was not decrypted successfully
        elseif (!$value)
        {
            wp_die(
                Escape::htmlWithTranslation('سرور میزبان نتوانست اطلاعات رو استخراج کند!لطفا بعدا تلاش کنید'),
                Escape::htmlWithTranslation('خطای سرور'),
                500
            );
        }

        return unserialize($value);
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