<?php

namespace engine\utils;

use engine\security\Sanitize;

defined('ABSPATH') || exit;

class Request
{
    private static ?Request $instance = null; // singleton class object
    private static array $params; // request params

    /**
     * Sanitizes GET parameters and returns object
     *
     * @return Request
     */
    public static function get(): Request
    {
        self::$params = Sanitize::variable($_GET);
        return self::getInstance();
    }

    /**
     * Sanitizes POST parameters and returns object
     *
     * @return Request
     */
    public static function post(): Request
    {
        self::$params = Sanitize::variable($_POST);
        return self::getInstance();
    }

    /**
     * Returns the instance of class , if not exist creates one
     *
     * @return Request
     */
    private static function getInstance(): Request
    {
        if (self::$instance == null)
            return new static();

        else
            return self::$instance;
    }

    /**
     * Checks whether the verb contains passed parameter
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset(self::$params[$key]);
    }

    /**
     * Returns a value of http specified verb parameters
     * @param string $key
     * @return string|array
     */
    public function getParam(string $key): string|array
    {
        return self::$params[$key];
    }

    /**
     * Returns all parameter of http specified verb
     *
     * @return array
     */
    public function getParams(): array
    {
        return self::$params;
    }
}