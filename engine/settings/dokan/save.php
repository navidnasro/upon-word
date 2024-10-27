<?php

namespace engine\settings\dokan;

defined('ABSPATH') || exit;

use engine\utils\CodeStar;

class save
{
    public function __construct()
    {
        add_action('dokan_before_saving_settings',[$this,'changeSettings'],999,3);
    }

    public function changeSettings(string $optionName,array $optionValue,array $oldOptions): void
    {
        return; // modify settings as you like on save
    }
}

new save();