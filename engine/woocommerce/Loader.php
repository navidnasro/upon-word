<?php

namespace engine\woocommerce;

use engine\Loader;

defined('ABSPATH') || exit;

//Loads entire classes within namespace
Loader::require(__DIR__);
Loader::require(__DIR__.'/*');

