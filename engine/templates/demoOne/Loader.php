<?php

namespace engine\templates\demoOne;

use engine\Loader;
use engine\utils\Adaptive;

require_once 'Article.php';
require_once 'Blog.php';
require_once 'CartBox.php';
require_once 'Footer.php';
require_once 'Header.php';
require_once 'Product.php';
require_once 'Shop.php';
require_once 'Factory.php';

if (Adaptive::isMobile())
    Loader::require(__DIR__.'/responsive');