<?php

namespace engine\security;

defined('ABSPATH') || exit;

class Nonce
{
    public static function generate(string $action): string
    {
        return wp_create_nonce($action);
    }

    public static function verify(string $nonce,string $action): bool
    {
        if (wp_verify_nonce($nonce,$action))
            return true;

        return false;
    }
}