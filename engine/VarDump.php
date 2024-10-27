<?php

namespace engine;

defined('ABSPATH') || exit;

class VarDump
{
    public static function exit(mixed $variable): void
    {
        echo '<pre>';
        var_dump($variable);
        echo '</pre>';
        exit;
    }

    public static function pre(mixed $variable): void
    {
        echo '<pre>';
        var_dump($variable);
        echo '</pre>';
    }

    public static function dump(mixed $variable): void
    {
        var_dump($variable);
    }
}