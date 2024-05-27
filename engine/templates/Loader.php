<?php

namespace engine\settings;

use engine\Loader;

defined('ABSPATH') || exit;

Loader::require(__DIR__,'TemplateFactory.php');

/**
 * if demo 1 is activated
 * require 'demoOne/Loader.php';
 *
 * if demo 2 is activated
 * require 'demoTwo/Loader.php';
 */