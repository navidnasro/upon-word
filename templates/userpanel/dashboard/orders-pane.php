<?php

/**
 * Template for orders pane of dashboard tabs
 * Displayed in my-account page
 */

use engine\utils\TermUtils;
use engine\utils\UserUtils;

defined('ABSPATH') || exit;

global $ribar_options;

$userOrders = get_posts(
    [
        'numberposts' => 4,
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => wc_get_order_types( 'view-orders' ),
        'post_status' => array_keys( wc_get_order_statuses() ),
    ]
);

$compares = isset($_COOKIE['compare']) ?
    unserialize(base64_decode($_COOKIE['compare'])) : null;
$pageID = $ribar_options['compare-page-elementor'];

?>

<div
    id="userorders"
    class="tab-panel active flex w-full flex-col items-start space-y-7">
    <div class="flex w-full items-center justify-between">
        <div class="flex items-center justify-between space-x-3 space-x-reverse">
            <span class="flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22" fill="none">
                  <path d="M8.07615 1.8335L4.75781 5.161" stroke="#43454D" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M13.9238 1.8335L17.2422 5.161" stroke="#43454D" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M1.83398 7.19564C1.83398 5.4998 2.74148 5.3623 3.86898 5.3623H18.1323C19.2598 5.3623 20.1673 5.4998 20.1673 7.19564C20.1673 9.16647 19.2598 9.02897 18.1323 9.02897H3.86898C2.74148 9.02897 1.83398 9.16647 1.83398 7.19564Z" stroke="#43454D" stroke-width="1.5"/>
                  <path d="M8.94727 12.8335V16.0877" stroke="#43454D" stroke-width="1.5" stroke-linecap="round"/>
                  <path d="M13.1641 12.8335V16.0877" stroke="#43454D" stroke-width="1.5" stroke-linecap="round"/>
                  <path d="M3.20898 9.1665L4.50148 17.0865C4.79482 18.8648 5.50065 20.1665 8.12232 20.1665H13.6498C16.5007 20.1665 16.9223 18.9198 17.2523 17.1965L18.7923 9.1665" stroke="#43454D" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </span>
            <span class="text-[#313131] text-[15px] font-bold">آخرین سفارش‌ ها</span>
        </div>
        <a href="<?php echo wc_get_account_endpoint_url('orders') ?>"
           class="flex items-center justify-center space-x-2.5 space-x-reverse">
            <span class="text-[#313131] text-[15px] font-bold">
                مشاهده همه
            </span>
            <span class="flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="8" height="13" viewBox="0 0 8 13" fill="none">
                  <path d="M6.88188 0.182569C7.10318 0.403864 7.1233 0.750155 6.94224 0.994177L6.88188 1.06409L1.50519 6.44106L6.88188 11.818C7.10318 12.0393 7.1233 12.3856 6.94224 12.6296L6.88188 12.6996C6.66059 12.9208 6.3143 12.941 6.07028 12.7599L6.00036 12.6996L0.182632 6.88182C-0.0386639 6.66053 -0.0587816 6.31423 0.122278 6.07021L0.182632 6.0003L6.00036 0.182569C6.24379 -0.0608562 6.63846 -0.0608562 6.88188 0.182569Z" fill="#43454D"/>
                </svg>
            </span>
        </a>
    </div>
    <div class="user-account-orders-slider w-full overflow-hidden flex flex-col items-center justify-center space-y-5">
        <div class="swiper-wrapper">
            <?php
            if (!empty($userOrders))
            {
                foreach ($userOrders as $userOrder)
                {
                    $order = wc_get_order($userOrder);
                    $products = $order->get_items();
                    ?>
                    <div class="swiper-slide">
                        <div class="rounded-[15px] flex items-center justify-start space-x-[17px] space-x-reverse w-full border border-solid border-[#EBEBEB] bg-white py-7 px-8">
                            <?php
                            foreach ($products as $product)
                            {
                                $variationID = $product->get_data()['variation_id'];
                                $productID = $product->get_data()['product_id'];
                                $title = $product->get_data()['name'];
                                $paidPrice = $product->get_data()['total'];
                                $quantity = $product->get_quantity();
                                $cats = get_the_terms($productID,'product_cat');
                                $productObj = wc_get_product($productID);
                                $ancestors = '';

                                if($cats)
                                    $ancestors = TermUtils::getTermAncestors(end($cats),true);

                                $productImg = '';

                                if ($variationID != 0)
                                {
                                    $productImg = get_the_post_thumbnail_url($variationID,[80,100]);

                                    if (!$productImg)
                                        $productImg = get_the_post_thumbnail_url($productID,[80,100]);
                                }

                                else
                                    $productImg = get_the_post_thumbnail_url($productID,[80,100]);

                                ?>
                                <div class="flex-1 relative pt-5 px-2.5 pb-3.5 flex flex-col items-center rounded-md bg-white border border-solid border-[#F0F0F0]">
                                    <img class="w-[80px] h-[100px] mb-3" src="<?php echo $productImg ?>">
                                    <div class="cats text-[var(--sub-title)] text-[10px] mb-2 font-medium w-full text-right">
                                        <?php
                                        echo $ancestors ? $ancestors.'/'.end($cats)->name : end($cats)->name;
                                        ?>
                                    </div>
                                    <div class="product-title overflow-hidden text-[var(--title)] h-[32px] text-[13px] w-full text-right font-medium mb-[9px]">
                                        <?php echo $title?>
                                    </div>
                                    <hr class="bg-[var(--separator)] w-full">
                                    <div class="price flex w-full mt-[15px] mb-2.5 items-center justify-between">
                                        <span class="text-[var(--title)] text-sm font-bold">قیمت</span>
                                        <span class="text-[var(--title)] text-sm font-bold">
                                            <?php
                                                echo number_format($paidPrice,0,'/','/').' تومان';
                                            ?>
                                        </span>
                                    </div>
                                    <div class="flex items-center w-full justify-between">
                                        <a href="<?php echo $productObj->get_permalink() ?>"
                                           class="product-link bg-[var(--theme-secondary)] rounded-[4px] w-[115px] h-[30px] flex items-center justify-center">
                                            <span class="text-white text-[10px] font-bold">
                                                مشاهده محصول
                                            </span>
                                        </a>
                                        <div class="flex items-center justify-center space-x-[3px] space-x-reverse">
                                            <span class="compare cursor-pointer <?php echo !is_null($compares) && in_array(get_the_ID(),$compares) ? 'added' : '' ?> w-8 h-[29px] rounded-[4px] bg-[var(--theme-secondary-bg)] flex items-center justify-center"
                                                  data-product-id="<?php echo $productID ?>"
                                                  data-page-link="<?php echo get_page_link($pageID) ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="33" height="30" viewBox="0 0 33 30" fill="none">
                                                  <rect x="0.110199" width="32.6216" height="29.656" rx="3.77677" fill="var(--theme-secondary)" fill-opacity="0.1"/>
                                                  <path d="M18.875 17.5217L22.0243 12.2318C22.0537 12.2373 22.0837 12.2406 22.115 12.2406C22.1459 12.2406 22.1763 12.2373 22.2058 12.2318L25.3546 17.5217H18.875ZM7.45671 14.1248L10.606 8.83538C10.6355 8.84091 10.6659 8.84413 10.6967 8.84413C10.7276 8.84413 10.758 8.84091 10.7875 8.83538L13.9363 14.1248H7.45671ZM25.7813 17.6779C25.7818 17.6502 25.7751 17.6221 25.7604 17.5973L22.4766 12.0817C22.5517 11.9992 22.5973 11.891 22.5973 11.7726C22.5973 11.5142 22.3816 11.3046 22.115 11.3046C21.9464 11.3046 21.7981 11.3885 21.7121 11.5151L17.106 10.0942C17.1079 10.0753 17.1089 10.0564 17.1089 10.0371C17.1089 9.66031 16.7943 9.35493 16.4057 9.35493C16.1476 9.35493 15.9224 9.49035 15.8003 9.69163L11.1638 8.26148C11.1111 8.05836 10.9224 7.9082 10.6967 7.9082C10.4302 7.9082 10.214 8.11777 10.214 8.37617C10.214 8.49454 10.2596 8.60278 10.3347 8.68523L7.05094 14.2008C7.03621 14.2257 7.02955 14.2538 7.0305 14.2815H7.03003C7.03003 15.6328 8.67167 16.7286 10.6967 16.7286C12.7214 16.7286 14.363 15.6328 14.363 14.2815H14.3625C14.3639 14.2538 14.3568 14.2257 14.3421 14.2008L11.0583 8.68523C11.0892 8.65068 11.1154 8.61245 11.1353 8.57054L15.7053 9.97996C15.7039 9.99884 15.7024 10.0182 15.7024 10.0371C15.7024 10.2586 15.8112 10.4548 15.9799 10.5792V21.2926H13.8128V22.1185H18.9985V21.2926H16.8314V10.5792C16.9041 10.5258 16.9649 10.4594 17.0115 10.383L21.6342 11.809C21.6423 11.9131 21.686 12.0075 21.753 12.0817L18.4692 17.5973C18.4545 17.6221 18.4478 17.6502 18.4488 17.6779H18.4483C18.4483 19.0297 20.09 20.125 22.115 20.125C24.1396 20.125 25.7813 19.0297 25.7813 17.6779Z" fill="var(--theme-secondary)"/>
                                                </svg>
                                            </span>
                                            <span class="add-user-favorites cursor-pointer <?php echo UserUtils::hasFavorited($productObj) ? 'added' : '' ?> w-8 h-[29px] rounded-[4px] bg-[var(--theme-secondary-bg)] flex items-center justify-center"
                                                  data-product-id="<?php echo $productID ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="34" height="30" viewBox="0 0 34 30" fill="none">
                                                  <rect x="0.522949" width="32.6216" height="29.656" rx="3.77677" fill="var(--theme-secondary)" fill-opacity="0.1"/>
                                                  <path fill-rule="evenodd" clip-rule="evenodd" d="M16.1524 9.47614C17.065 8.90293 18.2772 8.74457 19.3245 9.07801C21.6024 9.80792 22.3096 12.2753 21.6771 14.2386C20.7011 17.3222 16.5329 19.6223 16.3562 19.7187C16.2933 19.7533 16.2237 19.7706 16.1541 19.7706C16.0845 19.7706 16.0155 19.7539 15.9526 19.7198C15.777 19.6245 11.6391 17.3584 10.6306 14.2392C10.63 14.2392 10.63 14.2386 10.63 14.2386C9.99696 12.2747 10.7019 9.8068 12.9776 9.07801C14.0462 8.73453 15.2107 8.88564 16.1524 9.47614ZM13.2358 9.87427C11.3944 10.4642 10.932 12.432 11.432 13.9838C12.2188 16.4161 15.3274 18.3827 16.1536 18.8695C16.9825 18.3777 20.1135 16.3893 20.8751 13.986C21.3752 12.4325 20.911 10.4648 19.0669 9.87427C18.1734 9.58934 17.1312 9.76275 16.4117 10.3159C16.2613 10.4308 16.0525 10.433 15.901 10.3192C15.1389 9.74992 14.1433 9.5832 13.2358 9.87427ZM18.5281 10.9816C19.293 11.2275 19.829 11.9005 19.8947 12.6962C19.9132 12.9265 19.7409 13.1284 19.5091 13.1468C19.4973 13.1479 19.4861 13.1485 19.4743 13.1485C19.2571 13.1485 19.073 12.9828 19.0551 12.7643C19.018 12.3059 18.7094 11.9189 18.2699 11.7779C18.0482 11.7065 17.927 11.4706 17.9983 11.2515C18.0707 11.0318 18.3058 10.9125 18.5281 10.9816Z" fill="var(--theme-secondary)"/>
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="rounded-tl-md text-white py-1.5 px-3.5 font-bold text-sm bg-[var(--theme-primary)] absolute -top-[1px] -left-[1px]">
                                        <?php echo $quantity ?>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
            }

            else
            {
                ?>
                <span class="text-lg text-rose-500 font-bold">
                    آدرسی ثبت نشده
                </span>
                <?php
            }
            ?>
        </div>
        <div class="pagination flex items-center justify-center w-full space-x-2.5 space-x-reverse"></div>
    </div>
</div>