<?php

namespace engine\security;

use engine\VarDump;

defined('ABSPATH') || exit;

class Spam
{
    /**
     * Check whether the request is spam or not
     *
     * @param string $nonce
     * @param string $nonceAction
     * @param string $honeypot
     * @param int $retries
     * @return bool true on spam detection
     */
    public static function checkForSpam(string $nonce = '',string $nonceAction = '',string $honeypot = '',int $retries = 4): bool
    {
        return self::checkNonce($nonce,$nonceAction) ||
               self::checkHoneypot($honeypot) ||
               self::checkTransient($retries);
    }

    /**
     * Checks nonce validity
     *
     * @param string $nonce
     * @param string $nonceAction
     * @return bool
     */
    public static function checkNonce(string $nonce,string $nonceAction): bool
    {
        if (!empty($nonce) && !empty($nonceAction))
            return !Nonce::verify($nonce,$nonceAction);

        return false;
    }

    /**
     * Checks honeypot existence
     *
     * @param string $honeypot
     * @return bool
     */
    public static function checkHoneypot(string $honeypot): bool
    {
        if (!empty($honeypot) && !empty($_POST[$honeypot]))
            return true;

        return false;
    }

    /**
     * Checks the transient to track user requests
     *
     * @param int $retries
     * @return bool
     */
    public static function checkTransient(int $retries): bool
    {
        $userIp = $_SERVER['REMOTE_ADDR'];
        $transientName = 'ajax_request_'.md5($userIp);
        $transient = get_transient($transientName);

        // transient exists and is not 1 , 1 means no more requests for 60 seconds
        if ($transient && $transient > 1)
        {
            $transient--;
            set_transient($transientName,$transient,10*60);
            // user still can request
            return false;
        }

        // spam detected
        elseif ($transient == 1)
            return true;

        set_transient($transientName,$retries,10*60);
        return false;
    }
}