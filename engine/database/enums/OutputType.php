<?php

namespace engine\database\enums;

defined('ABSPATH') || exit;

enum OutputType: string
{
    case OBJECT = 'OBJECT';
    case OBJECT_K = 'OBJECT_K';
    case ASSOCIATIVE_ARRAY = 'ARRAY_A';
    case NUMERIC_ARRAY = 'ARRAY_N';
}