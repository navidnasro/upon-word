<?php

namespace engine\elementor\widgets;

use elementor\Elements_Manager;
use elementor\Widget_Base;
use elementor\Widgets_Manager;
use engine\Loader;
use engine\security\Escape;

defined('ABSPATH') || exit;

class Register
{
    public static Widgets_Manager $widgetsManager;

    /**
     * Adding appropriate actions
     * @return void
     */
    public static function addAction(): void
    {
        add_action('elementor/widgets/register',[self::class,'require']);
        add_action('elementor/elements/categories_registered',[self::class,'registerCategories']);
    }

    /**
     * Requiring widget files in time of register action
     *
     * @param Widgets_Manager $widgetsManager
     * @return void
     */
    public static function require(Widgets_Manager $widgetsManager): void
    {
        self::$widgetsManager = $widgetsManager; //storing widget manager instance

        Loader::require(__DIR__.'/*'); //loads all widgets in subdirectories
    }

    public static function register(Widget_Base $widget): void
    {
        //using widget manager instance to register widgets
        self::$widgetsManager->register($widget);
    }

    /**
     * Registering and reordering categories
     *
     * @param Elements_Manager $widgetsManager
     * @return void
     */
    public static function registerCategories(Elements_Manager $widgetsManager): void
    {
        $categories = [
            'main-category' => [
                'title' => Escape::htmlWithTranslation('اصلی'),
                'icon' => 'fa fa-plug',
            ],
        ];

        $oldCategories = $widgetsManager->get_categories();
        $categories = array_merge($categories,$oldCategories);

        $set_categories = function ($categories) {
            $this->categories = $categories;
        };

        $set_categories->call($widgetsManager,$categories);
    }
}

Register::addAction();