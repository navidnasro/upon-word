<?php

namespace engine;

defined('ABSPATH') || exit;

class Loader
{
    public static function load(): void
    {
        require_once 'enums/Loader.php';
        require_once 'security/Loader.php';
        require_once 'utils/Loader.php';
        require_once 'ThemeInitializer.php';
        require_once 'database/Loader.php';

        if (is_admin())
        {
            require_once 'admin/Loader.php';
            require_once 'settings/Loader.php';
        }

        else
        {
            require_once 'AjaxHandler.php';
            require_once 'woocommerce/Loader.php';
        }

        require_once 'walkers/Loader.php';
        require_once 'elementor/Loader.php';
        require_once 'wordpress/Loader.php';
    }

    /**
     * Loads all auto loaders in engine
     *
     * @param string $directory
     * @return void
     */
    public static function autoLoaders(string $directory): void
    {
        $loaders = glob($directory.'/*/{Loader,Register}.php',GLOB_BRACE);

        foreach ($loaders as $file)
            require_once $file;
    }

    /**
     * Loads classes from passed namespace
     *
     * @param string $directory
     * @param string $interface
     * @return void
     */
    public static function require(string $directory,string $interface = ''): void
    {
        $files = glob($directory.'/*.php');

        if (!empty($interface))
            require_once $directory.'/'.$interface;

        foreach($files as $file)
                require_once $file;
    }
}

Loader::load();