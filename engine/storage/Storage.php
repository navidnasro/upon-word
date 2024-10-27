<?php

namespace engine\storage;

use engine\VarDump;

defined('ABSPATH') || exit;

class Storage
{
    private static array $readData = [];

    public static function getJsonContent(string $path): mixed
    {
        if (isset(self::$readData[$path]))
            return self::$readData[$path];

        $content = file_get_contents($path);
        $content = json_decode($content,true);
        self::$readData[$path] = $content;

        return $content;
    }

    public static function getJsonDataWhere(string $path,string $key)
    {
        $content = self::getJsonContent($path);
        return $content[$key] ?? false;
    }
}