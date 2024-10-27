<?php

namespace engine\wordpress;

defined('ABSPATH') || exit;

class Filters
{
    public function __construct()
    {
        add_filter('excerpt_length',[$this,'excerptLength'], 999);
    }

    /**
     * @param $length
     * @return int
     */
    public function excerptLength($length): int
    {
        return 20;
    }
}

new Filters();