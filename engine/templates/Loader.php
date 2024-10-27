<?php

namespace engine\settings;

use engine\Loader;
use engine\utils\CodeStar;

defined('ABSPATH') || exit;

Loader::require(__DIR__,'TemplateFactory.php');

if (CodeStar::getOption('active-demo') == 'demo1')
    require 'demoOne/Loader.php';

else if (CodeStar::getOption('active-demo') == 'demo2')
    require 'demoTwo/Loader.php';