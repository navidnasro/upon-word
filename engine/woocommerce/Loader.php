<?php

namespace engine\woocommerce;

use engine\Loader;
use engine\utils\Adaptive;
use engine\utils\Cookie;

defined('ABSPATH') || exit;

//Loads entire classes within namespace
Loader::require(__DIR__);

add_action('wp',function ()
    {
        return; // if any file needs a condition to be required
    }
);