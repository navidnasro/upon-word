<?php

namespace engine\settings;

use engine\enums\Constants;
use engine\Loader;

defined('ABSPATH') || exit;

Loader::require(__DIR__);

if (Constants::Settings == 'redux')
    Loader::require(__DIR__.'/redux');

else if (Constants::Settings == 'codestar')
    Loader::require(__DIR__.'/codestar');