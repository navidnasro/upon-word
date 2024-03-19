<?php

namespace engine\wordpress;

use engine\Loader;

defined('ABSPATH') || exit;

//Loads entire classes within namespace
Loader::require(__DIR__);
Loader::autoLoaders(__DIR__);