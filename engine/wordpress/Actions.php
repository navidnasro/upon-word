<?php

namespace engine\wordpress;

defined('ABSPATH') || exit;

class Actions
{
    public function __construct()
    {
        add_action('init',[$this,'addCustomTabsPages']);
        add_action('wp_loaded',[$this,'flushRewriteRules']);
        add_action('wp_head',[$this,'headParams']);
        add_action('admin_notices',[$this,'adminNotices']);
    }

    /**
     * Adds custom endpoints to wordpress system
     *
     * @return void
     */
    public function addCustomTabsPages(): void
    {
        add_rewrite_endpoint( 'favorites', EP_ROOT | EP_PAGES );
        add_rewrite_endpoint( 'recents', EP_ROOT | EP_PAGES );
        add_rewrite_endpoint( 'comments', EP_ROOT | EP_PAGES );
    }

    /**
     * Removes rewrite rules and then recreate rewrite rules.
     *
     * @return void
     */
    public function flushRewriteRules(): void
    {
        flush_rewrite_rules();
    }

    public function headParams(): void
    {
        global $ribar_options;
        ?>
        <style>
            :root{
                --global-font: <?php echo $ribar_options['global-font'] ?>
            }
        </style>
        <?php
    }

    public function adminNotices(): void
    {
        $plugins = apply_filters('active_plugins',get_option('active_plugins'));

        if (!in_array('elementor/elementor.php',$plugins))
        {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p>شما افزونه المنتور رو روی سایت خود نصب ندارید!<br>برای کارکرد بهتر قالب نیاز هست تا افزونه المنتور رو نصب کنید.</p>
            </div>
            <?php
        }

        if (!in_array('woocommerce/woocommerce.php',$plugins))
        {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p>شما افزونه ووکامرس رو روی سایت خود نصب ندارید!<br>برای کارکرد بهتر قالب نیاز هست تا افزونه ووکامرس رو نصب کنید.</p>
            </div>
            <?php
        }

    }
}

new Actions();