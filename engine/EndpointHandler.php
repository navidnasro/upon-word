<?php

namespace engine;

class EndpointHandler
{
    public function __construct()
    {
        add_action('init',[$this,'addEndPoints']);
        add_action('wp_loaded',[$this,'flushRewriteRules']);
        add_action('woocommerce_account_bookmarks_endpoint',[$this,'renderBookMarks']);
        add_action('woocommerce_account_recent-visits_endpoint',[$this,'renderRecentVisits']);
        add_action('woocommerce_account_change-password_endpoint',[$this,'renderChangePassword']);
        add_action('woocommerce_account_invoice_endpoint',[$this,'renderInvoice']);
        add_action('woocommerce_account_comments_endpoint',[$this,'renderComments']);
    }

    /**
     * Adds custom endpoints to wordpress system
     *
     * @return void
     */
    public function addEndPoints(): void
    {
        add_rewrite_endpoint('bookmarks',EP_ROOT | EP_PAGES);
        add_rewrite_endpoint('recent-visits',EP_ROOT | EP_PAGES);
        add_rewrite_endpoint('change-password',EP_ROOT | EP_PAGES);
        add_rewrite_endpoint('invoice',EP_ROOT | EP_PAGES);
        add_rewrite_endpoint('comments',EP_ROOT | EP_PAGES);
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

    /**
     * Renders Favorite Tab Related Page
     *
     * @return void
     */
    public static function renderBookMarks(): void
    {
        require_once THEME_ROOT.'/woocommerce/myaccount/bookmarks.php';
    }

    /**
     * Renders Recently visited Tab Related Page
     *
     * @return void
     */
    public static function renderRecentVisits(): void
    {
        require_once THEME_ROOT.'/woocommerce/myaccount/recent-visits.php';
    }

    /**
     * Renders Comments Tab Related Page
     *
     * @return void
     */
    public static function renderComments(): void
    {
        require_once THEME_ROOT.'/woocommerce/myaccount/comments.php';
    }

    /**
     * Renders Password Change Tab Related Page
     *
     * @return void
     */
    public static function renderChangePassword(): void
    {
        require_once THEME_ROOT.'/woocommerce/myaccount/change-password.php';
    }

    /**
     * Renders Invoice Page
     *
     * @return void
     */
    public static function renderInvoice(): void
    {
        require_once THEME_ROOT.'/woocommerce/myaccount/invoice.php';
    }
}

new EndpointHandler();