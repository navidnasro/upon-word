<?php

namespace engine;

use engine\utils\TermUtils;
use engine\utils\UserUtils;
use engine\utils\WcUtils;
use WP_Query;

defined('ABSPATH') || exit;

class AjaxHandler
{
    function __construct()
    {
        add_action('wp_ajax_ribar_ajax',[$this,'ribar_ajax']);
        add_action('wp_ajax_nopriv_ribar_ajax',[$this,'ribar_ajax']);

        add_action('wp_ajax_data_fetch',[$this,'data_fetch']);
        add_action('wp_ajax_nopriv_data_fetch',[$this,'data_fetch']);

        add_action('wp_footer',[$this,'ajax_fetch']);

        add_action('wp_ajax_userFavorite',[$this,'userFavorite']);
        add_action('wp_ajax_nopriv_userFavorite',[$this,'userFavorite']);

        add_action('wp_ajax_compareProducts',[$this,'compareProducts']);
        add_action('wp_ajax_nopriv_compareProducts',[$this,'compareProducts']);

        add_action('wp_ajax_addProductToCompare',[$this,'addProductToCompare']);
        add_action('wp_ajax_nopriv_addProductToCompare',[$this,'addProductToCompare']);

        add_action('wp_ajax_removeProductFromCompare',[$this,'removeProductFromCompare']);
        add_action('wp_ajax_nopriv_removeProductFromCompare',[$this,'removeProductFromCompare']);

        add_action('wp_ajax_searchResult',[$this,'searchResult']);
        add_action('wp_ajax_nopriv_searchResult',[$this,'searchResult']);
    }

    /**
     * 
     */
    public function ribar_ajax()
    {
        $tab = $_POST['button'];
        $filterProduct = $_POST['filterProduct'];
        $result = '';
        if($filterProduct == 'mostVisited') {
            $count_key                = 'views';

            $the_query_blog = new WP_Query(array(
                'post_type'      => 'product',
                'meta_key'       => $count_key,
                'no_found_rows'  => 1,
                'post_status'    => 'publish',
                'order'          => 'DESC',
                'orderby'        => 'meta_value_num',
                'posts_per_page' => 10,
                'tax_query' => array(
                    array(
                        'taxonomy'  => 'product_cat',
                        'field'     => 'term_id',
                        'terms'     => $tab,
                        'operator'  => 'IN',
                    )
                )
            ));

        } elseif ($filterProduct == 'last'){
            $the_query_blog = new WP_Query(array(
                'post_type' => 'product',
                'posts_per_page' => 8,
                'order'          => 'DESC',
                'tax_query' => [[
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $tab,
                ]],
            ));
        }
        elseif ($filterProduct == 'Bestselling'){
            $the_query_blog = new WP_Query(array(
                'post_type'      => 'product',
                'meta_key'       => 'total_sales',
                'orderby'        => 'meta_value_num',
                'posts_per_page' => 10,
                'tax_query' => array(
                    array(
                        'taxonomy'  => 'product_cat',
                        'field'     => 'term_id',
                        'terms'     => $tab,
                        'operator'  => 'IN',
                    )
                )
            ));
        }
        elseif ($filterProduct == 'MostPopular'){
            $the_query_blog = new WP_Query(array(
                'post_type'      => 'product',
                'meta_key'       => '_wc_average_rating',
                'orderby'        => 'meta_value_num',
                'posts_per_page' => 10,
                'tax_query' => array(
                    array(
                        'taxonomy'  => 'product_cat',
                        'field'     => 'term_id',
                        'terms'     => $tab,
                        'operator'  => 'IN',
                    )
                )
            ));
        }
        elseif ($filterProduct == 'ascending'){
            $the_query_blog = new WP_Query(array(
                'post_type' => 'product',
                'posts_per_page' => 10,
                'order'          => 'ASC',
                'tax_query' => [[
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $tab,
                ]],
            ));
        }
        elseif ($filterProduct == 'Descending'){
            $the_query_blog = new WP_Query(array(
                'post_type' => 'product',
                'posts_per_page' => 10,
                'order'          => 'DESC',
                'tax_query' => [[
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $tab,
                ]],
            ));
        }
        elseif ($filterProduct == 'discounted'){
            $the_query_blog = new WP_Query(array(
                'posts_per_page' => 10,
                'no_found_rows'  => 1,
                'post_status'    => 'publish',
                'post_type'      => 'product',
                'order'          => 'DESC',
                'meta_key'       => '_sale_price',
                'tax_query' => [[
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $tab,
                ]],
                'meta_query'     => array(
                    array(
                        'key'     => '_sale_price',
                        'value'   => '0',
                        'type'    => 'numeric',
                        'compare' => '>',
                    ),
                )
            ));
        }
        elseif ($filterProduct == 'NumberOFcomments'){
            $the_query_blog = new WP_Query(array(
                'post_type'      => 'product',
                'meta_key'       => '_wc_review_count',
                'orderby'        => 'meta_value_num',
                'posts_per_page' => 10,
                'tax_query' => array(
                    array(
                        'taxonomy'  => 'product_cat',
                        'field'     => 'term_id',
                        'terms'     => $tab,
                        'operator'  => 'IN',
                    )
                )
            ));
        }
        else {
            $the_query_blog = new WP_Query(array(
                'post_type' => 'product',
                'posts_per_page' => 10,
                'tax_query' => [[
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $tab,
                ]],
            ));
        }



    //    $args = array(
    //        'post_type'      => 'product',
    //        'meta_key'       => 'total_sales',
    //        'orderby'        => 'meta_value_num',
    //        'posts_per_page' => 10,
    //        'tax_query' => array(
    //            array(
    //                'taxonomy'  => 'product_cat',
    //                'field'     => 'term_id',
    //                'terms'     => $tab,
    //                'operator'  => 'IN',
    //            )
    //        )
    //    );

    //    $loop = new WP_Query( $args );



        if ($the_query_blog->have_posts()):
        while ( $the_query_blog->have_posts() ) : $the_query_blog->the_post();
            global $product;

            $result .= '<div class="swiper-slide">
            <article class="blog__card">
                <div class="blog__card--thumbnail">
                    <a class="blog__card--thumbnail__link display-block ribar-thumbnail--product--padding" href="'. get_the_permalink(). '">';
            if ( has_post_thumbnail($the_query_blog->post->ID) ) {
                $result .= get_the_post_thumbnail($the_query_blog->post->ID, 'shop_catalog');
            } else {
                $result .= '<img src="'.woocommerce_placeholder_img_src().'" alt="product placeholder Image" width="65px" height="115px" />';
            }
            $result .= '</a>
                </div>
                <div class="blog__card--content">
                    <h3 class="blog__card--title ribar__product--title "><a class="ribar_one_line" href="'. get_the_permalink() .'">'. get_the_title() .'</a></h3>
                    <p class='. esc_attr( apply_filters( 'woocommerce_product_price_class', 'price') ).'>'. $product->get_price_html().'</p>
                </div>
            </article>
        </div>';
        endwhile;
        wp_reset_query();
        else: $result .= 'محصولی یافت نشد';
        endif;
        echo $result;
        die;
    }

    /**
     * 
     */
    public function data_fetch()
    {
        if ($_POST['pcat']) {
            $product_cat_id = array(esc_attr( $_POST['pcat'] ));
        }else {
            $terms = get_terms( 'product_cat' );
            $product_cat_id = wp_list_pluck( $terms, 'term_id' );
        }
        $the_query = new WP_Query(
            array(
                'posts_per_page' => 6,
                's' => esc_attr( $_POST['keyword'] ),
                'post_type' => array('product'),
    
                'tax_query' => array(
                    array(
                        'taxonomy'  => 'product_cat',
                        'field'     => 'term_id',
                        'terms'     => $product_cat_id,
                        'operator'  => 'IN',
                    )
                )
            )
        );
        if( $the_query->have_posts() ) :
            echo '<ul>';
            while( $the_query->have_posts() ): $the_query->the_post(); ?>
    
                <li><a href="<?php echo esc_url( post_permalink() ); ?>"><span><?php the_post_thumbnail('product-search')?></span> <?php the_title();?></a></li>
    
            <?php endwhile;
            echo '</ul>';
            wp_reset_postdata();
        endif;
    
        die();
    }

    /**
     * 
     */
    public function ajax_fetch()
    {
?>
	<script type="text/javascript">
        var ribar_searching = 0;
        ribar_search_ajax = new XMLHttpRequest();
        function fetch(){
            if($('#keyword').val().length > 2 ) {

                if (ribar_searching = 1) {
                    ribar_search_ajax.abort()
                }
                ribar_searching = 1;
                $('.ribar_lds-ring').show()
                ribar_search_ajax = jQuery.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'post',
                    data: {action: 'data_fetch', keyword: jQuery('#keyword').val(), pcat: jQuery('#cat').val()},
                    success: function (data) {
                        jQuery('#datafetch').html(data);
                        ribar_searching = 0;
                        $('.ribar_lds-ring').hide()
                    }
                });
            }
        }
	</script>

<?php
    }

    public function userFavorite()
    {
        $postID = json_decode($_POST['product_id']);
        $isFavorite = json_decode($_POST['is_favorite']);
        
        $success = false;
        $operation = 'add';
        
        $userID = get_current_user_id();

        if (!$userID)
        {
            wp_send_json([
                'success' => json_encode($success),
                'operation' => json_encode('notlogged'),
            ]);

            wp_die();
        }
        
        if($isFavorite)
        {
            $operation = 'remove';
            $data = get_user_meta($userID,'favorites',true);
            
            if(in_array($postID,$data))
                unset($data[array_search($postID,$data)]);
            
            $success = update_user_meta($userID,'favorites',$data);
        }
        else
        {
            $data = get_user_meta($userID,'favorites',true);

            if(!$data)
                $data = array();

            $data[] = $postID;

            $success = update_user_meta($userID,'favorites',$data);
        }

        wp_send_json([
            'success' => json_encode($success),
            'operation' => json_encode($operation),
        ]);
    
        wp_die();
    }

    public function compareProducts()
    {
        $productID = $_POST['product_id'];

        $operation = 'add';

        //products ids
        $product_ids = array();
        
        //if user already has seen some products and cookie's been set
        if(isset($_COOKIE['compare']))
        {
            $product_ids = unserialize(base64_decode($_COOKIE['compare']));
            
            //cookie must be updated if a new product is viewed
            if(!in_array($productID,$product_ids))
            {
                $product_ids[] = $productID;
                $product_ids = base64_encode(serialize($product_ids));

                setcookie('compare',$product_ids,
                    [
                        'expires' => 0,
                        'path' => '/',
                        'httponly' => true,
                    ]
                );
            }

            else
            {
                $key = array_search($productID,$product_ids);
                unset($product_ids[$key]);

                $product_ids = base64_encode(serialize($product_ids));

                setcookie('compare',$product_ids,
                    [
                        'expires' => 0,
                        'path' => '/',
                        'httponly' => true,
                    ]
                );

                $operation = 'remove';
            }
        }

        //else the user is viewing their first product and cookie must be set
        else
        {
            $product_ids[] = $productID;
            $product_ids = base64_encode(serialize($product_ids));

            setcookie('compare',$product_ids,
                [
                    'expires' => 0,
                    'path' => '/',
                    'httponly' => true,
                ]
            );
        }

        wp_send_json([
            'success' => true,
            'operation' => json_encode($operation)
        ]);
    
        wp_die();
    }

    public function addProductToCompare()
    {
        $productID = $_GET['product_id'] ?? -1;

        if ($productID == -1 || !is_numeric($productID))
            wp_die();

        if (isset($_COOKIE['compare']))
        {
            //adding new product id among other ids in compare cookie
            $product_ids = unserialize(base64_decode($_COOKIE['compare']));

            //cookie must be updated if a new product is viewed
            if(!in_array($productID,$product_ids))
            {
                $product_ids[] = $productID;
                $product_ids = base64_encode(serialize($product_ids));

                setcookie('compare',$product_ids,
                    [
                        'expires' => 0,
                        'path' => '/',
                        'httponly' => true,
                    ]
                );
            }
        }

        //else the user is viewing their first product and cookie must be set
        else
        {
            $product_ids[] = $productID;
            $product_ids = base64_encode(serialize($product_ids));

            setcookie('compare',$product_ids,
                [
                    'expires' => 0,
                    'path' => '/',
                    'httponly' => true,
                ]
            );
        }

        $product = wc_get_product($productID);
        $image = get_the_post_thumbnail_url($productID,[150,150]);
        $cats = get_the_terms($product->get_id(),'product_cat');
        $ancestors = '';

        if($cats)
            $ancestors = TermUtils::getTermAncestors(end($cats),true); //var_dump
        ?>
        <div class="pt-[30px] pb-4 px-3 flex flex-col items-center justify-center w-1/4 bg-white rounded-lg">
            <span class="remove-from-compare cursor-pointer flex items-center rounded-[5px] justify-center bg-[#F85A3E0F] p-1 mb-2.5"
                  data-product-id="<?php echo $productID ?>">
                <svg class="rotate-45" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                  <path d="M18 12.998H13V17.998C13 18.2632 12.8946 18.5176 12.7071 18.7051C12.5196 18.8926 12.2652 18.998 12 18.998C11.7348 18.998 11.4804 18.8926 11.2929 18.7051C11.1054 18.5176 11 18.2632 11 17.998V12.998H6C5.73478 12.998 5.48043 12.8926 5.29289 12.7051C5.10536 12.5176 5 12.2632 5 11.998C5 11.7328 5.10536 11.4784 5.29289 11.2909C5.48043 11.1033 5.73478 10.998 6 10.998H11V5.99799C11 5.73277 11.1054 5.47842 11.2929 5.29088C11.4804 5.10334 11.7348 4.99799 12 4.99799C12.2652 4.99799 12.5196 5.10334 12.7071 5.29088C12.8946 5.47842 13 5.73277 13 5.99799V10.998H18C18.2652 10.998 18.5196 11.1033 18.7071 11.2909C18.8946 11.4784 19 11.7328 19 11.998C19 12.2632 18.8946 12.5176 18.7071 12.7051C18.5196 12.8926 18.2652 12.998 18 12.998Z" fill="var(--theme-primary)"/>
                </svg>
            </span>
            <img class="mb-[15px]" src="<?php echo $image ?>">
            <div class="cats text-[var(--sub-title)] text-[10px] mb-2 font-medium w-full text-right">
                <?php
                echo $ancestors ? $ancestors.'/'.end($cats)->name : end($cats)->name;
                ?>
            </div>
            <div class="product-title text-[var(--title)] h-[32px] text-[13px] w-full text-right overflow-hidden font-medium mb-[9px]">
                <?php echo $product->get_title()?>
            </div>
            <hr class="bg-[var(--separator)] w-full">
            <div class="price flex w-full mt-[15px] mb-2.5 items-center justify-between">
                <span class="text-[var(--title)] text-sm font-bold">
                    <?php
                    if ($product->is_type('simple'))
                        echo 'قیمت:';

                    else if ($product->is_type('variable'))
                        echo 'قیمت از:';
                    ?>
                </span>
                <span class="text-[var(--title)] text-sm font-bold">
                    <?php
                    if ($product->is_on_sale())
                        echo WcUtils::getSalePrice($product).' تومان';

                    else
                        echo WcUtils::getRegularPrice($product).' تومان';
                    ?>
                </span>
            </div>
            <div class="flex items-center w-full justify-between">
                <a href="<?php echo $product->get_permalink() ?>"
                   class="product-link bg-[var(--theme-secondary)] rounded-[4px] w-[115px] h-[30px] flex items-center justify-center">
                    <span class="text-white text-[10px] font-bold">
                        مشاهده محصول
                    </span>
                </a>
                <div class="flex items-center justify-center space-x-[3px] space-x-reverse">
                    <span class="add-user-favorites cursor-pointer <?php echo UserUtils::hasFavorited($product) ? 'added' : '' ?> w-8 h-[29px] rounded-[4px] bg-[var(--theme-secondary-bg)] flex items-center justify-center"
                          data-product-id="<?php echo get_the_ID() ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="34" height="30" viewBox="0 0 34 30" fill="none">
                          <rect x="0.522949" width="32.6216" height="29.656" rx="3.77677" fill="var(--theme-secondary)" fill-opacity="0.1"/>
                          <path fill-rule="evenodd" clip-rule="evenodd" d="M16.1524 9.47614C17.065 8.90293 18.2772 8.74457 19.3245 9.07801C21.6024 9.80792 22.3096 12.2753 21.6771 14.2386C20.7011 17.3222 16.5329 19.6223 16.3562 19.7187C16.2933 19.7533 16.2237 19.7706 16.1541 19.7706C16.0845 19.7706 16.0155 19.7539 15.9526 19.7198C15.777 19.6245 11.6391 17.3584 10.6306 14.2392C10.63 14.2392 10.63 14.2386 10.63 14.2386C9.99696 12.2747 10.7019 9.8068 12.9776 9.07801C14.0462 8.73453 15.2107 8.88564 16.1524 9.47614ZM13.2358 9.87427C11.3944 10.4642 10.932 12.432 11.432 13.9838C12.2188 16.4161 15.3274 18.3827 16.1536 18.8695C16.9825 18.3777 20.1135 16.3893 20.8751 13.986C21.3752 12.4325 20.911 10.4648 19.0669 9.87427C18.1734 9.58934 17.1312 9.76275 16.4117 10.3159C16.2613 10.4308 16.0525 10.433 15.901 10.3192C15.1389 9.74992 14.1433 9.5832 13.2358 9.87427ZM18.5281 10.9816C19.293 11.2275 19.829 11.9005 19.8947 12.6962C19.9132 12.9265 19.7409 13.1284 19.5091 13.1468C19.4973 13.1479 19.4861 13.1485 19.4743 13.1485C19.2571 13.1485 19.073 12.9828 19.0551 12.7643C19.018 12.3059 18.7094 11.9189 18.2699 11.7779C18.0482 11.7065 17.927 11.4706 17.9983 11.2515C18.0707 11.0318 18.3058 10.9125 18.5281 10.9816Z" fill="var(--theme-secondary)"/>
                        </svg>
                    </span>
                </div>
            </div>
        </div>
        <?php

        wp_die();
    }

    public function removeProductFromCompare()
    {
        $productID = $_GET['product_id'] ?? -1;

        if ($productID == -1 || !is_numeric($productID))
            wp_die();

        if (isset($_COOKIE['compare']))
        {
            //adding new product id among other ids in compare cookie
            $product_ids = unserialize(base64_decode($_COOKIE['compare']));

            //if is among other ids, remove it!
            if(in_array($productID,$product_ids))
            {
                $key = array_search($productID,$product_ids);
                unset($product_ids[$key]);

                $product_ids = base64_encode(serialize($product_ids));

                setcookie('compare',$product_ids,
                    [
                        'expires' => 0,
                        'path' => '/',
                        'httponly' => true,
                    ]
                );
            }
        }
    }

    public function searchResult()
    {
        if (empty($_GET['input'])) //existence check
        {
            echo '<p class="text-lg text-rose-500 font-bold">محصولی یافت نشد</p>';

            wp_die();
        }

        $input = sanitize_text_field($_GET['input']);

        $query = new WP_Query(
            [
                'post_type' => 'product',
                'posts_per_page' => 10,
                's' => $input,
            ]
        );

        if ($query->have_posts())
        {
            while ($query->have_posts())
            {
                $query->the_post();
                $product = wc_get_product(get_the_ID());
                $imgUrl = get_the_post_thumbnail_url($product->get_id(),[48,48]);
                ?>
                <div class="w-full flex items-center pb-2.5 hover:bg-white/[.1] justify-start space-x-3.5 space-x-reverse">
                    <span class="w-12 h-12">
                        <img class="rounded-lg" src="<?php echo $imgUrl ?>">
                    </span>
                    <a href="<?php echo $product->get_permalink() ?>"
                       class="text-sm text-[var(--title)] hover:text-[var(--theme-primary)] font-bold">
                        <?php echo $product->get_title() ?>
                    </a>
                </div>
                <?php
            }
            ?>
            <a href="<?php echo add_query_arg('s',$input,wc_get_page_permalink('shop')) ?>"
               class="self-center py-1.5 px-2 bg-[var(--theme-secondary)] rounded-lg text-white text-sm font-bold">
                مشاهده همه
            </a>
            <?php
        }

        else
            echo '<p class="text-sm text-[var(--theme-primary)] font-bold">محصولی یافت نشد</p>';

        wp_reset_postdata();

        wp_die();
    }
}

new AjaxHandler();