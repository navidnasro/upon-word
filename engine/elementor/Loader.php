<?php

namespace engine\elementor;

use engine\Loader;
use engine\utils\Theme;

defined('ABSPATH') || exit;

if (Theme::pluginExists('elementor'))
{
    Loader::require(__DIR__);
    Loader::autoLoaders(__DIR__);
}
