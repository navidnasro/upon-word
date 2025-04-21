<?php

namespace engine\ajax;

foreach (glob(__DIR__.'/*.php') as $file)
{
    require_once $file;

    $className = pathinfo($file, PATHINFO_FILENAME);
    $fullClassName = 'engine\ajax'. '\\' .$className;

    if (class_exists($fullClassName) && is_subclass_of($fullClassName, AjaxHandler::class))
    {
        $handler = new $fullClassName();
        $handler->init();
    }
}