<?php

namespace engine\wc\shop;

defined('ABSPATH') || exit;

class Actions
{
    public function __construct()
    {
        remove_action('woocommerce_archive_description','woocommerce_taxonomy_archive_description',10);
        remove_action('woocommerce_archive_description','woocommerce_product_archive_description',10);
        remove_action('woocommerce_before_shop_loop','woocommerce_result_count',20);

        add_action('woocommerce_before_shop_loop',[self::class,'openGroup'],19);
        add_action('woocommerce_before_shop_loop',[self::class,'displayStyle'],31);
        add_action('woocommerce_before_shop_loop',[self::class,'closeGroup'],32);
    }

    /**
     * Opens opening div for grouping "display styles" and "filtering"
     *
     * @return void
     */
    public static function openGroup(): void
    {
        echo '<div class="flex w-full items-center justify-between pr-[21px] pl-[13px] py-3 rounded-[5px] bg-white mb-5">';
    }

    /**
     * Displays styles for showing products (grid and linear)
     *
     * @return void
     */
    public static function displayStyle()
    {
        echo '<div class="flex items-center justify-center">'.
                '<span id="linear" class="flex items-center justify-center rounded-r-[6px] w-[38px] h-10">'.
                    '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">'.
                      '<path d="M21 8H3V4H21V8ZM21 10H3V14H21V10ZM21 16H3V20H21V16Z" fill="#43454D"/>'.
                    '</svg>'.
                '</span>'.
                '<span id="grid" class="active flex items-center justify-center rounded-l-[6px] w-[38px] h-10 bg-[#466CFF12]">'.
                    '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">'.
                      '<path d="M11 3H3V11H11V3Z" fill="#43454D"/>'.
                      '<path d="M11 13H3V21H11V13Z" fill="#43454D"/>'.
                      '<path d="M21 3H13V11H21V3Z" fill="#43454D"/>'.
                      '<path d="M21 13H13V21H21V13Z" fill="#43454D"/>'.
                    '</svg>'.
                '</span>'.
             '</div>';
    }

    /**
     * Closes div for grouping "display styles" and "filtering"
     *
     * @return void
     */
    public static function closeGroup(): void
    {
        echo '</div>';
    }
}

new Actions();