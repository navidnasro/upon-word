<?php

namespace engine\admin;

use engine\Loader;

defined('ABSPATH') || exit;

if (is_admin())
{
    Loader::require(__DIR__);
    Loader::autoLoaders(__DIR__);
    Loader::require(__DIR__.'/*');
}

require_once __DIR__.'/taxonomies/Register.php';
Loader::require(__DIR__.'/taxonomies','Taxonomy.php');