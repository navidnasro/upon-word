<?php

namespace engine\woocommerce;

use engine\Loader;

defined('ABSPATH') || exit;

//Loads entire classes within namespace
Loader::require(__DIR__);

if (!is_admin())
    Loader::require(__DIR__.'/*');