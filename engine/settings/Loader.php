<?php

namespace engine\settings;

use engine\enums\Constants;
use engine\Loader;
use engine\utils\Theme;

defined('ABSPATH') || exit;

Loader::require(__DIR__);

if (Constants::Settings == 'redux')
    Loader::require(__DIR__.'/redux');

else if (Constants::Settings == 'codestar')
{
    require_once THEME_ROOT.'/codestar/codestar-framework.php';
    Loader::require(__DIR__.'/codestar');
}

if (Theme::pluginExists('dokan'))
    Loader::require(__DIR__.'/dokan');

if (Theme::pluginExists('woocommerce'))
    Loader::require(__DIR__.'/woocommerce');