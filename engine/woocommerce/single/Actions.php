<?php

namespace engine\wc\single;

use engine\utils\ElementorUtils;
use engine\utils\TermUtils;
use engine\utils\UserUtils;
use engine\utils\WcUtils;

defined('ABSPATH') || exit;

class Actions
{
    public function __construct()
    {
        //title description
        add_action('woocommerce_single_product_summary',[self::class,'titleDescription'],7);

        //removing product rate
        remove_action('woocommerce_single_product_summary','woocommerce_template_single_rating',10);

        //removing product meta
        remove_action('woocommerce_single_product_summary','woocommerce_template_single_meta',40);

        //removing product excerpt
        remove_action('woocommerce_single_product_summary','woocommerce_template_single_excerpt',20);
        add_action('woocommerce_after_single_product_summary','woocommerce_template_single_excerpt',5);

        //repositioning product price
        remove_action('woocommerce_single_product_summary','woocommerce_template_single_price',10);
        add_action('woocommerce_after_variations_table','woocommerce_template_single_price');
        add_action('woocommerce_before_simple_add_to_cart_form','woocommerce_template_single_price');

        //plus
        add_action('woocommerce_before_add_to_cart_quantity',[self::class,'plusButton']);

        //minus
        add_action('woocommerce_after_add_to_cart_quantity',[self::class,'minusButton']);

        remove_action('woocommerce_before_single_product_summary','woocommerce_show_product_sale_flash',10);

        remove_action('woocommerce_before_shop_loop_item','woocommerce_template_loop_product_link_open',10);
        add_action('woocommerce_before_shop_loop_item',[self::class,'openWrapper'],10);

        remove_action('woocommerce_before_main_content','woocommerce_breadcrumb',20);
        add_action('woocommerce_before_main_content',[self::class,'breadcrumb'],10);

        remove_action('woocommerce_before_shop_loop_item_title','woocommerce_template_loop_product_thumbnail',10);
        remove_action('woocommerce_before_shop_loop_item_title','woocommerce_show_product_loop_sale_flash',10);
        add_action('woocommerce_before_shop_loop_item_title',[self::class,'productImage'],10);
        add_action('woocommerce_before_shop_loop_item_title',[self::class,'productCategories'],11);

        remove_action('woocommerce_shop_loop_item_title','woocommerce_template_loop_product_title',10);
        add_action('woocommerce_shop_loop_item_title',[self::class,'productTitle'],10);

        remove_action('woocommerce_after_shop_loop_item_title','woocommerce_template_loop_rating',5);
        remove_action('woocommerce_after_shop_loop_item_title','woocommerce_template_loop_price',10);
        add_action('woocommerce_after_shop_loop_item_title',[self::class,'separator'],5);
        add_action('woocommerce_after_shop_loop_item_title',[self::class,'productPrice'],10);

        remove_action('woocommerce_after_shop_loop_item','woocommerce_template_loop_product_link_close',5);
        remove_action('woocommerce_after_shop_loop_item','woocommerce_template_loop_add_to_cart',10);
        add_action('woocommerce_after_shop_loop_item',[self::class,'productButton'],5);
        add_action('woocommerce_after_shop_loop_item',[self::class,'closeWrapper'],10);

        //defining woocommerce code inside user code actions for elementor editor display purposes
        remove_action('woocommerce_single_product_summary','woocommerce_template_single_add_to_cart',30);
        add_action('woocommerce_single_product_summary',[self::class,'addToCartButtonAction'],30);

        //defining woocommerce code inside user code actions for elementor editor display purposes
        remove_action('woocommerce_variable_add_to_cart','woocommerce_variable_add_to_cart',30);
        add_action('woocommerce_variable_add_to_cart',[self::class,'woocommerceVariableAddToCart'],30);
    }

    public static function titleDescription(): void
    {
        global $ribar_options;

        if (ElementorUtils::isEditor() || ElementorUtils::isPreview())
            $product = wc_get_product($ribar_options['single-product-sample']);

        else
            $product = wc_get_product(get_the_ID());

        $title = get_post_meta($product->get_id(),'title-desc',true);

        if ($title)
        {
            echo '<div class="text-[15px] text-[#43454D52] font-medium">';
                echo $title;
            echo '</div>';
        }
    }

    /**
     * Breadcrumb
     *
     * @return void
     */
    public static function breadcrumb(): void
    {
        woocommerce_breadcrumb(
            [
                'delimiter'   => '<span class="mx-2.5">&#62</span>',
                'wrap_before' => '<nav class="woocommerce-breadcrumb">',
                'wrap_after'  => '</nav>',
            ]
        );
    }

    /**
     * Quantity plus button
     *
     * @return void
     */
    public static function plusButton(): void
    {
        echo '<button type="button" class="plus" >+</button>';
    }

    /**
     * Quantity minus button
     *
     * @return void
     */
    public static function minusButton(): void
    {
        echo '<button type="button" class="minus" >-</button>';
    }

    /**
     * Product card content wrapper start
     *
     * @return void
     */
    public static function openWrapper(string $style): void
    {
        if ($style == 'linear')
            echo '<div class="linear-view pt-[30px] pb-4 px-3 flex flex-col items-start justify-center w-full bg-white rounded-lg">';

        else if(is_shop() || is_product_category())
            echo '<div class="grid-view pt-[30px] pb-4 px-3 flex flex-col items-center justify-center w-[23%] bg-white rounded-lg">';

        else
            echo '<div class="pt-[30px] pb-4 px-3 flex flex-col items-center justify-center w-full bg-white rounded-lg">';

    }

    /**
     * Product card content image
     *
     * @return void
     */
    public static function productImage(string $style): void
    {
        global $ribar_options;

        if (ElementorUtils::isEditor() || ElementorUtils::isPreview())
            $product = wc_get_product($ribar_options['single-product-sample']);

        else
            $product = wc_get_product(get_the_ID());

        $image = get_the_post_thumbnail_url($product->get_id(),[95,117]);

        if ($style == 'linear')
            echo '<div class="flex items-center space-x-8 space-x-reverse">';

        echo '<img class="mb-[15px]" src="'.$image.'">';
    }

    /**
     * Product card content categories
     *
     * @return void
     */
    public static function productCategories(string $style): void
    {
        global $ribar_options;

        if (ElementorUtils::isEditor() || ElementorUtils::isPreview())
            $product = wc_get_product($ribar_options['single-product-sample']);

        else
            $product = wc_get_product(get_the_ID());

        $cats = get_the_terms($product->get_id(),'product_cat');
        $ancestors = '';

        if($cats)
            $ancestors = TermUtils::getTermAncestors(end($cats),true); //var_dump

        if ($style == 'linear')
            echo '<div class="flex flex-col items-center">';

        echo '<div class="cats text-[var(--sub-title)] text-[10px] mb-2 font-medium w-full text-right">';
        echo $ancestors ? $ancestors.'/'.end($cats)->name : end($cats)->name;
        echo '</div>';
    }

    /**
     * Product card content title
     *
     * @return void
     */
    public static function productTitle(string $style): void
    {
        global $ribar_options;

        if (ElementorUtils::isEditor() || ElementorUtils::isPreview())
            $product = wc_get_product($ribar_options['single-product-sample']);

        else
            $product = wc_get_product(get_the_ID());

        echo '<div class="product-title text-[var(--title)] h-8 overflow-hidden text-[10px] w-full text-right font-medium mb-[9px]">';
        echo  $product->get_title();
        echo '</div>';

        if ($style == 'linear')
        {
            echo '</div>';
            echo '</div>';
        }
    }

    /**
     * Product card content separator
     *
     * @return void
     */
    public static function separator(string $style): void
    {
        echo '<hr class="bg-[var(--separator)] w-full">';
    }

    /**
     * Product card content price
     *
     * @return void
     */
    public static function productPrice(string $style): void
    {
        global $ribar_options;

        if (ElementorUtils::isEditor() || ElementorUtils::isPreview())
            $product = wc_get_product($ribar_options['single-product-sample']);

        else
            $product = wc_get_product(get_the_ID());

        if ($style == 'linear')
            echo '<div class="flex w-full items-center justify-between space-x-5 space-x-reverse">';

        echo '<div class="price flex w-full mt-[15px] mb-2.5 items-center justify-between">'.
                '<span class="text-[var(--title)] text-sm font-bold">';

                if ($product->is_type('simple'))
                    echo 'قیمت:';

                else if ($product->is_type('variable'))
                    echo 'قیمت از:';

            echo '</span>'.
                '<span class="text-[var(--title)] text-sm font-bold">';

                if ($product->is_on_sale())
                    echo WcUtils::getSalePrice($product).' تومان';

                else
                    echo WcUtils::getRegularPrice($product).' تومان';

            echo '</span>'.
            '</div>';
    }

    /**
     * Product card content button
     *
     * @return void
     */
    public static function productButton(string $style): void
    {
        global $ribar_options;

        if (ElementorUtils::isEditor() || ElementorUtils::isPreview())
            $product = wc_get_product($ribar_options['single-product-sample']);

        else
            $product = wc_get_product(get_the_ID());

        $compares = isset($_COOKIE['compare']) ?
            unserialize(base64_decode($_COOKIE['compare'])) : null;
        $pageID = $ribar_options['compare-page-elementor'];

        $compare = !is_null($compares) && in_array(get_the_ID(),$compares) ? 'added' : '';
        $favorite = UserUtils::hasFavorited($product) ? 'added' : '';

        echo '<div class="flex items-center w-full justify-between">'.
            '<a href="'.$product->get_permalink().'"'.
               'class="product-link bg-[var(--theme-secondary)] rounded-[4px] w-[115px] h-[30px] flex items-center justify-center">'.
                '<span class="text-white text-[10px] font-bold">'.
                    'مشاهده محصول'.
                '</span>'.
            '</a>'.
            '<div class="flex items-center justify-center space-x-[3px] space-x-reverse">'.
                '<span class="compare cursor-pointer'.$compare.'w-8 h-[29px] rounded-[4px] bg-[var(--theme-secondary-bg)] flex items-center justify-center"'.
                      'data-product-id="'.get_the_ID().'"'.
                      'data-page-link="'.get_page_link($pageID).'">'.
                    '<svg xmlns="http://www.w3.org/2000/svg" width="33" height="30" viewBox="0 0 33 30" fill="none">'.
                      '<rect x="0.110199" width="32.6216" height="29.656" rx="3.77677" fill="var(--theme-secondary)" fill-opacity="0.1"/>'.
                      '<path d="M18.875 17.5217L22.0243 12.2318C22.0537 12.2373 22.0837 12.2406 22.115 12.2406C22.1459 12.2406 22.1763 12.2373 22.2058 12.2318L25.3546 17.5217H18.875ZM7.45671 14.1248L10.606 8.83538C10.6355 8.84091 10.6659 8.84413 10.6967 8.84413C10.7276 8.84413 10.758 8.84091 10.7875 8.83538L13.9363 14.1248H7.45671ZM25.7813 17.6779C25.7818 17.6502 25.7751 17.6221 25.7604 17.5973L22.4766 12.0817C22.5517 11.9992 22.5973 11.891 22.5973 11.7726C22.5973 11.5142 22.3816 11.3046 22.115 11.3046C21.9464 11.3046 21.7981 11.3885 21.7121 11.5151L17.106 10.0942C17.1079 10.0753 17.1089 10.0564 17.1089 10.0371C17.1089 9.66031 16.7943 9.35493 16.4057 9.35493C16.1476 9.35493 15.9224 9.49035 15.8003 9.69163L11.1638 8.26148C11.1111 8.05836 10.9224 7.9082 10.6967 7.9082C10.4302 7.9082 10.214 8.11777 10.214 8.37617C10.214 8.49454 10.2596 8.60278 10.3347 8.68523L7.05094 14.2008C7.03621 14.2257 7.02955 14.2538 7.0305 14.2815H7.03003C7.03003 15.6328 8.67167 16.7286 10.6967 16.7286C12.7214 16.7286 14.363 15.6328 14.363 14.2815H14.3625C14.3639 14.2538 14.3568 14.2257 14.3421 14.2008L11.0583 8.68523C11.0892 8.65068 11.1154 8.61245 11.1353 8.57054L15.7053 9.97996C15.7039 9.99884 15.7024 10.0182 15.7024 10.0371C15.7024 10.2586 15.8112 10.4548 15.9799 10.5792V21.2926H13.8128V22.1185H18.9985V21.2926H16.8314V10.5792C16.9041 10.5258 16.9649 10.4594 17.0115 10.383L21.6342 11.809C21.6423 11.9131 21.686 12.0075 21.753 12.0817L18.4692 17.5973C18.4545 17.6221 18.4478 17.6502 18.4488 17.6779H18.4483C18.4483 19.0297 20.09 20.125 22.115 20.125C24.1396 20.125 25.7813 19.0297 25.7813 17.6779Z" fill="var(--theme-secondary)"/>'.
                    '</svg>'.
                '</span>'.
                '<span class="add-user-favorites cursor-pointer'.$favorite.'w-8 h-[29px] rounded-[4px] bg-[var(--theme-secondary-bg)] flex items-center justify-center"'.
                      'data-product-id="'.get_the_ID().'">'.
                    '<svg xmlns="http://www.w3.org/2000/svg" width="34" height="30" viewBox="0 0 34 30" fill="none">'.
                      '<rect x="0.522949" width="32.6216" height="29.656" rx="3.77677" fill="var(--theme-secondary)" fill-opacity="0.1"/>'.
                      '<path fill-rule="evenodd" clip-rule="evenodd" d="M16.1524 9.47614C17.065 8.90293 18.2772 8.74457 19.3245 9.07801C21.6024 9.80792 22.3096 12.2753 21.6771 14.2386C20.7011 17.3222 16.5329 19.6223 16.3562 19.7187C16.2933 19.7533 16.2237 19.7706 16.1541 19.7706C16.0845 19.7706 16.0155 19.7539 15.9526 19.7198C15.777 19.6245 11.6391 17.3584 10.6306 14.2392C10.63 14.2392 10.63 14.2386 10.63 14.2386C9.99696 12.2747 10.7019 9.8068 12.9776 9.07801C14.0462 8.73453 15.2107 8.88564 16.1524 9.47614ZM13.2358 9.87427C11.3944 10.4642 10.932 12.432 11.432 13.9838C12.2188 16.4161 15.3274 18.3827 16.1536 18.8695C16.9825 18.3777 20.1135 16.3893 20.8751 13.986C21.3752 12.4325 20.911 10.4648 19.0669 9.87427C18.1734 9.58934 17.1312 9.76275 16.4117 10.3159C16.2613 10.4308 16.0525 10.433 15.901 10.3192C15.1389 9.74992 14.1433 9.5832 13.2358 9.87427ZM18.5281 10.9816C19.293 11.2275 19.829 11.9005 19.8947 12.6962C19.9132 12.9265 19.7409 13.1284 19.5091 13.1468C19.4973 13.1479 19.4861 13.1485 19.4743 13.1485C19.2571 13.1485 19.073 12.9828 19.0551 12.7643C19.018 12.3059 18.7094 11.9189 18.2699 11.7779C18.0482 11.7065 17.927 11.4706 17.9983 11.2515C18.0707 11.0318 18.3058 10.9125 18.5281 10.9816Z" fill="var(--theme-secondary)"/>'.
                    '</svg>'.
                '</span>'.
            '</div>'.
        '</div>';

        if ($style == 'linear')
            echo '</div>';
    }

    /**
     * Product card content wrapper end
     *
     * @return void
     */
    public static function closeWrapper(string $style): void
    {
        echo '</div>';
    }

    /**
     * Fires woocommerce add to cart action , used for elementor editor display purposes
     *
     * @return void
     */
    public static function addToCartButtonAction(): void
    {
        global $ribar_options;

        if (ElementorUtils::isEditor() || ElementorUtils::isPreview())
            $product = wc_get_product($ribar_options['single-product-sample']);

        else
            $product = wc_get_product(get_the_ID());

        do_action( 'woocommerce_' . $product->get_type() . '_add_to_cart' );
    }

    /**
     * Fires woocommerce add to cart action , used for elementor editor display purposes
     *
     * @return void
     */
    public static function woocommerceVariableAddToCart(): void
    {
        global $ribar_options;

        if (ElementorUtils::isEditor() || ElementorUtils::isPreview())
            $product = wc_get_product($ribar_options['single-product-sample']);

        else
            $product = wc_get_product(get_the_ID());

        // Enqueue variation scripts.
        wp_enqueue_script( 'wc-add-to-cart-variation' );

        // Get Available variations?
        $get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );

        // Load the template.
        wc_get_template(
            'single-product/add-to-cart/variable.php',
            array(
                'available_variations' => $get_variations ? $product->get_available_variations() : false,
                'attributes'           => $product->get_variation_attributes(),
                'selected_attributes'  => $product->get_default_attributes(),
            )
        );
    }
}

new Actions();