<?php

namespace engine\utils;

defined('ABSPATH') || exit;

class Notice
{
    public static function nothingToShow(): string
    {
        return '<div class="w-full text-lg text-rose-500 font-bold">آیتمی جهت نمایش وجود ندارد</div>';
    }

    public static function custom(string $message): void
    {
        echo '<div class="w-full text-lg text-rose-500 font-bold">'.$message.'</div>';
    }
}