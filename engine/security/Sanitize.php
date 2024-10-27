<?php

namespace engine\security;

defined('ABSPATH') || exit;

class Sanitize
{
    /**
     * Sanitizes a general text
     *
     * @param string $text
     * @return string
     */
    public static function text(string $text): string
    {
        return sanitize_text_field($text);
    }

    /**
     * @param string $value
     * @return int
     */
    public static function number(string $value): int
    {
        return intval(self::text($value));
    }

    /**
     * Sanitizes a variable's value
     *
     * @param mixed $variable
     * @param bool $htmlOnly
     * @return array|string
     */
    public static function variable(mixed $variable,bool $htmlOnly = false): array|string
    {
        if (is_array($variable))
        {
            $array = [];

            foreach ($variable as $key => $value)
            {
                if ($htmlOnly)
                    $key = Escape::htmlTags($key);
                else
                    $key = sanitize_key($key);

                // if the value is array , sanitize it recursively
                if (is_array($value))
                    $array[$key] = self::variable($value);

                else
                {
                    if ($htmlOnly)
                        $array[$key] = Escape::htmlTags($value);

                    else
                        $array[$key] = self::text($value);
                }
            }

            return $array;
        }

        return self::text($variable);
    }
}