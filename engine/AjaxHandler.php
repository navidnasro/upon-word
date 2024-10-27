<?php

namespace engine;

use engine\enums\Constants;
use engine\enums\Defaults;
use engine\security\Escape;
use engine\security\Sanitize;
use engine\security\Spam;
use engine\storage\Storage;
use engine\utils\Adaptive;
use engine\utils\Cart;
use engine\utils\CodeStar;
use engine\utils\Notice;
use engine\utils\Request;
use engine\utils\User;
use engine\utils\Woocommerce;
use engine\woocommerce\ProductVariations;
use WC_Product_Variation;
use WP_Query;

defined('ABSPATH') || exit;

class AjaxHandler
{
    function __construct()
    {
        // handle ajax requests here
        add_action('wp_ajax_liveSearch',[$this,'liveSearch']);
        add_action('wp_ajax_nopriv_liveSearch',[$this,'liveSearch']);
    }

    public function liveSearch(): void
    {
        // if it is not a wordpress ajax call
        if (!Request::isAjax())
            wp_die();

        $data = Request::post(true)->getParams();

        $search_input = $data['input'];
        $device = $data['device'];

        $liClasses = '';
        $liID = '';
        $emClasses = 'not-italic text-cyan font-medium';
        $spanClasses = 'text-darkblue font-bold';

        if ($device == 'desktop')
        {
            $liID = 'desktop-search-category-result-';
            $liClasses = 'w-full rounded-2xl bg-white shadow-md py-2.5 px-5 text-darkblue cursor-pointer text-[14px]';
        }

        elseif ($device == 'mobile')
        {
            $liID = 'mobile-search-category-result-';
            $liClasses = 'w-full rounded-2xl bg-white shadow-md p-2.5 text-darkblue cursor-pointer text-[13px]';
        }

        $success = true; // Whether Results Found
        $cats = [];
        $categoriesHtml = '';
        $productsHtml = '';

        $terms = get_terms(
            [
                'taxonomy' => [
                    'category',
                    'product_cat',
                ],
                'hide_empty' => 1, //MUST BE 1 , IT IS 0 FOR TESTING
                'name__like' => $search_input,
            ]
        );

        if($terms)
        {
            ob_start();

            foreach($terms as $term)
            {
                $cats[] = $term->term_id;
                ?>
                <li id="<?php echo $liID.$term->term_id ?>"
                    class="<?php echo $liClasses ?>">
                    <a href="<?php echo get_term_link($term) ?>">
                        <em class="<?php echo $emClasses ?>">
                            <?php echo $search_input ?>
                        </em> در
                        <span class="<?php echo $spanClasses ?>">
                            <?php echo $term->name ?>
                        </span>
                    </a>
                </li>
                <?php
            }

            $categoriesHtml = ob_get_clean();
        }

        $products = new WP_Query(
            [
                'post_type' => 'product',
                'posts_per_page' => 10,
                'tax_query' => [
                    [
                        'taxonomy' => 'product_cat',
                        'field' => 'term_id',
                        'terms' => array_values($cats),
                        'operator' => 'NOT IN',
                    ]
                ],
                's' => $search_input,
            ]
        );

        if ($products->have_posts())
        {
            ob_start();

            while ($products->have_posts())
            {
                $products->the_post();
                ?>
                <li class="w-[49%] mb-0.5 cursor-pointer inline-block rounded-[15px] bg-white mt-[5px] py-2.5 px-5">
                    <a class="flex items-center"
                       href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail([60,60]); ?>
                        <p style="width: calc(100% - 70px)" class="text-[#333] h-20 mr-2.5 overflow-hidden font-medium text-[15px]">
                            <?php the_title() ?>
                        </p>
                    </a>
                </li>
                <?php
            }

            $productsHtml = ob_get_clean();

            wp_reset_postdata();
        }

        else
            $success = false;

        wp_send_json([
            'success' => json_encode($success),
            'categories' => json_encode($categoriesHtml),
            'products' => json_encode($productsHtml)
        ]);

        wp_die();
    }
}

new AjaxHandler();