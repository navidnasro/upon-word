<?php

namespace engine\wordpress\widgets;

use engine\Loader;
use WP_Widget;

defined('ABSPATH') || exit;

class Register
{
    /**
     * Registers Widgets
     *
     * @param WP_Widget $widget
     * @return void
     */
    public static function register(WP_Widget $widget): void
    {
        add_action('widgets_init',function () use ($widget) {

            register_widget($widget);

        });
    }
}
//Loads entire classes within namespace
Loader::require(__DIR__.'/*');