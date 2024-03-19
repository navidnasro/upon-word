<?php

namespace engine\security;

use engine\enums\Constants;

defined('ABSPATH') || exit;

class Escape
{
    public static function htmlWithTranslation(string $data): string
    {
        return esc_html__($data,Constants::TextDomain);
    }
}