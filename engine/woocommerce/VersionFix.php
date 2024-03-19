<?php

namespace engine\woocommerce;

class VersionFix
{
    function __construct()
    {
        add_action('init', [self::class,'versionFix']);
    }

    public static function versionFix(): void
    {
        if(class_exists('WooCommerce'))
        {
            global $woocommerce;

            if(version_compare(get_option('woocommerce_db_version', null), $woocommerce->version, '!='))
            {
                update_option('woocommerce_db_version', $woocommerce->version);

                if(!wc_update_product_lookup_tables_is_running())
                {
                    wc_update_product_lookup_tables();
                }
            }
        }
    }
}