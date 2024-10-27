<?php

namespace engine\security;

use engine\enums\Constants;

defined('ABSPATH') || exit;

class Escape
{
    /**
     * Escapes data with translation
     *
     * @param string $data
     * @return string
     */
    public static function htmlWithTranslation(string $data): string
    {
        return esc_html__($data,Constants::TextDomain);
    }

    /**
     * Strips all html tags
     *
     * @param string $text
     * @return string
     */
    public static function htmlTags(string $text): string
    {
        return wp_strip_all_tags($text);
    }
}