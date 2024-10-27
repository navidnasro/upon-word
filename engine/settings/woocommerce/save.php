<?php

namespace engine\settings\woocommerce;

use engine\utils\CodeStar;

defined('ABSPATH') || exit;

class save
{
    public function __construct()
    {
        add_action('updated_option',[$this,'changeSettings'],99999,3);
    }

    public function changeSettings(string $optionName,mixed $oldValue,mixed $newOption): void
    {
        return; // modify settings as you like on save
    }
}

new Save();