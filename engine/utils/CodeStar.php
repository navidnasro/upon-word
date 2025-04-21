<?php

namespace engine\utils;

use engine\enums\Constants;
use engine\VarDump;

defined('ABSPATH') || exit;

class CodeStar
{
    private static array|bool|null $options = null;

    public static function getOptions(): mixed
    {
        if (is_null(self::$options))
            self::$options = get_option(Constants::SettingsObjectID);

        return self::$options;
    }

    public static function updateOption(string $optionName,string $optionValue): void
    {
        if (isset(self::$options[$optionName]) && self::$options[$optionName] != $optionValue)
        {
            self::$options[$optionName] = $optionValue;

            update_option(Constants::SettingsObjectID,self::$options);
        }
    }

    public static function getOption(string $optionName,mixed $default = false): mixed
    {
        if (is_null(self::$options))
            self::$options = get_option(Constants::SettingsObjectID);

        return self::$options[$optionName] ?? $default; // if not exist return empty string
    }

    public static function isOptionChanged(string $option,array $prevOptions,array $newOptions): bool
    {
        // Checking the option-key change or not.
        return isset($prevOptions[$option]) &&
               isset($newOptions[$option]) &&
               ($prevOptions[$option] !== $newOptions[$option]);
    }
}