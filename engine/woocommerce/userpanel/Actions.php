<?php

namespace engine\wc\userpanel;

defined('ABSPATH') || exit;

class Actions
{
    public function __construct()
    {
        add_action('woocommerce_account_dashboard',[self::class,'dashboardTabItems']);
        add_action('woocommerce_account_favorites_endpoint',[self::class,'renderFavorites']);
        add_action('woocommerce_account_recents_endpoint',[self::class,'renderRecents']);
        add_action('woocommerce_account_comments_endpoint',[self::class,'renderComments']);
    }

    /**
     * Includes dashboard tab templates
     *
     * @return void
     */
    public static function dashboardTabItems(): void
    {
        require TEMPLATES.'/userpanel/dashboard/tab-items.php';
        echo '<div class="tabs-content w-full">';
        require TEMPLATES.'/userpanel/dashboard/orders-pane.php';
        require TEMPLATES.'/userpanel/dashboard/addresses-pane.php';
        require TEMPLATES.'/userpanel/dashboard/account-pane.php';
        require TEMPLATES.'/userpanel/dashboard/lastseen-pane.php';
        echo '</div>';
    }

    /**
     * Renders Favorite Tab Related Page
     *
     * @return void
     */
    public static function renderFavorites(): void
    {
        require_once DIRECTORY.'/woocommerce/myaccount/favorites.php';
    }

    /**
     * Renders Recently visited Tab Related Page
     *
     * @return void
     */
    public static function renderRecents(): void
    {
        require_once DIRECTORY.'/woocommerce/myaccount/recent-visits.php';
    }

    /**
     * Renders Comments Tab Related Page
     *
     * @return void
     */
    public static function renderComments(): void
    {
        require_once DIRECTORY.'/woocommerce/myaccount/comments.php';
    }
}

new Actions();