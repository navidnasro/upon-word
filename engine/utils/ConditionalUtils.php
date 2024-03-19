<?php

namespace engine\utils;

defined('ABSPATH') || exit;

class ConditionalUtils
{
    /**
     * Checks if the viewing admin page is the passed one
     *
     * @param string $name file or page name
     * @param bool $isFile whether passed name is file name with suffix or is page name
     * @return bool
     */
    public static function isAdminPage(string $name,bool $isFile = false): bool
    {
        if ($isFile)
        {
            if ($name == basename($_SERVER['REQUEST_URI']))
                return true;
        }

        else
        {
            if ($name == basename($_SERVER['REQUEST_URI'],'.php'))
                return true;
        }

        return false;
    }
}