<?php

namespace engine\wc\userpanel;

defined('ABSPATH') || exit;

class Filters
{
    public function __construct()
    {
        add_filter('woocommerce_account_menu_items',[self::class,'navigationItems']);
        add_filter('woocommerce_my_account_my_address_description',[self::class,'userAddressesTitle']);
    }

    /**
     * Modifies user panel navigation sidebar items
     *
     * @param array $items
     * @return array
     */
    public static function navigationItems(array $items): array
    {
        unset($items['customer-logout']);
        $items['favorites'] = 'محصولات مورد علاقه';
        $items['recents'] = 'بازدید های اخیر';
        $items['comments'] = 'نظرات و پرسش ها';
        $items['customer-logout'] = __( 'Log out', 'woocommerce' );

        return $items;
    }

    /**
     * Modifies address page title on user account
     *
     * @return string
     */
    public static function userAddressesTitle() : string
    {
        return 'آدرس های من';
    }
}

new Filters();