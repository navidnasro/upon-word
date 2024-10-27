<?php

namespace engine\utils;

use engine\security\Sanitize;

defined('ABSPATH') || exit;

class Adaptive
{
    public static function isDesktop(): bool
    {
        return Cookie::exists('screenwidth') &&
            Sanitize::number($_COOKIE['screenwidth']) >= 1024;
    }

    public static function isMobile(): bool
    {
        return Cookie::exists('screenwidth') &&
            Sanitize::number($_COOKIE['screenwidth']) < 1024;
    }
}