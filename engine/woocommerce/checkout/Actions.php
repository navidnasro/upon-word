<?php

namespace engine\wc\checkout;

defined('ABSPATH') || exit;

class Actions
{
    public function __construct()
    {
        remove_action('woocommerce_before_checkout_form','woocommerce_checkout_coupon_form',10);
    }
}

new Actions();