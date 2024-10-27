<?php

namespace engine\settings;

use engine\enums\Constants;

defined('ABSPATH') || exit;

class ThemeColors
{
    public static function modify(array $options): void
    {
        ob_start();
        ?>
        :root{
        --body: <?php echo $options['body-color'] ?>;
        --menu-item: <?php echo $options['menu-item-color'] ?>;
        --theme-primary: <?php echo $options['theme-primary-color'] ?>;
        --theme-primary-border: <?php echo $options['theme-primary-border-color'] ?>;
        --theme-primary-shadow: <?php echo $options['theme-primary-shadow-color'] ?>;
        --theme-primary-bg: <?php echo $options['theme-primary-bg-color'] ?>;
        --theme-secondary: <?php echo $options['theme-secondary-color'] ?>;
        --theme-secondary-border: <?php echo $options['theme-secondary-border-color'] ?>;
        --theme-secondary-shadow: <?php echo $options['theme-secondary-shadow-color'] ?>;
        --theme-secondary-bg: <?php echo $options['theme-secondary-bg-color'] ?>;
        --theme-secondary-title-bg: <?php echo $options['theme-secondary-title-bg-color'] ?>;
        --separator: <?php echo $options['separator-color'] ?>;
        --title: <?php echo $options['title-color'] ?>;
        --title-light: <?php echo $options['title-light-color'] ?>;
        --sub-title: <?php echo $options['sub-title-color'] ?>;
        --sidebar-item: <?php echo $options['sidebar-item-color'] ?>;
        }
        <?php
        $content = ob_get_clean();

        $file = fopen(get_template_directory().'/assets/css/colors.css','w');
        fwrite($file,$content);
        fclose($file);
    }

    /**
     * Checks if the given array has any of color options
     *
     * @param array $colorOptions
     * @return bool
     */
    public static function hasColorChanged(array $colorOptions): bool
    {
        foreach ($colorOptions as $color => $value)
            if (str_contains($color,'-color'))
                return true;

        return false;
    }
}