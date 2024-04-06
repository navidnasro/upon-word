<?php

namespace engine\utils;

use engine\enums\Constants;

defined('ABSPATH') || exit;

class CodeStar
{
    public static function getOptions(): mixed
    {
        return get_option(Constants::SettingsObjectID);
    }

    public static function getOption(string $optionName): mixed
    {
        $options = get_option(Constants::SettingsObjectID);

        return $options[$optionName] ?? ''; // if not exit return empty string
    }
}