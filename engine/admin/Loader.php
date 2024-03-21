<?php

namespace engine\admin;

use engine\Loader;

defined('ABSPATH') || exit;

Loader::require(__DIR__);
Loader::autoLoaders(__DIR__);
Loader::require(__DIR__.'/*');