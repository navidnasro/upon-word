<?php

namespace engine\utils;

use engine\security\Escape;
use engine\security\Sanitize;
use engine\VarDump;
use WP_Error;

defined('ABSPATH') || exit;

class Request
{
    private static ?Request $instance = null; // singleton class object
    private static array $params; // request params

    /**
     * Determines whether the current request is a WordPress Ajax request
     *
     * @return bool
     */
    public static function isAjax(): bool
    {
        return wp_doing_ajax();
    }

    /**
     * Sanitizes GET parameters and returns object
     *
     * @param bool $htmlOnly , whether to only escape html
     * @return Request|null
     */
    public static function get(bool $htmlOnly = false): ?Request
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        {
            self::$params = Sanitize::variable($_GET,$htmlOnly);
            return self::getInstance();
        }

        else
            return null;
    }

    /**
     * Sanitizes POST parameters and returns object
     *
     * @param bool $htmlOnly , whether to only escape html
     * @return Request|null
     */
    public static function post(bool $htmlOnly = false): ?Request
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            self::$params = Sanitize::variable($_POST,$htmlOnly);
            return self::getInstance();
        }

        else
            return null;
    }

    /**
     * Returns the instance of class , if not exist creates one
     *
     * @return Request
     */
    private static function getInstance(): Request
    {
        if (self::$instance == null)
            self::$instance = new static();

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

    /**
     * Validates request fields
     *
     * @param array $requestFields
     * @param WP_Error $errors
     * @return array|bool
     */
    public function validate(array $requestFields, WP_Error &$errors): array|bool
    {
        $validationResults = [];

        // for each field in the request
        foreach ($requestFields as $field => $params)
        {
            $rules = $params['rules'];
            $dictionary = $params['dictionary'];

            if (!$this->has($field) || empty($this->getParam($field)))
            {
                $validationResults[$field] = '';
                continue;
            }

            // extract rules data
            $parts = explode('|', $rules, 3);

            // Ensure all parts have a value, or set a default if missing
            $rule = $parts[0] ?? '';
            $dataType = $parts[1] ?? '';
            $checkParams = $parts[2] ?? '';

            // input value
            $input = $this->getParam($field);

            // checking rules

            if ($rule == 'charNum' &&
                Validator::checkInputLength($dictionary,$input,$dataType,$checkParams,$errors))
            {
                $validationResults[$field] = $input;
            }

            else if ($rule == 'intVal' &&
                Validator::checkIntValue($dictionary,$input,$dataType,$checkParams,$errors))
            {
                $validationResults[$field] = $input;
            }

            else if ($rule == 'email' &&
                Validator::checkEmail($dictionary,$input,$errors))
            {
                $validationResults[$field] = $input;
            }

            else if ($rule == 'gender' &&
                Validator::checkGender($dictionary,$input,$errors))
            {
                $validationResults[$field] = $input;
            }

            else if ($rule == 'state' &&
                Validator::checkState($dictionary,$input,$errors))
            {
                $validationResults[$field] = $input;
            }

            else if ($rule == 'city' &&
                Validator::checkCity($dictionary,$input,$errors))
            {
                $validationResults[$field] = $input;
            }
        }

        // if all data is validated , two lengths are equal
        if (count($requestFields) == count($validationResults))
            return $validationResults;

        else
            return false;
    }
}