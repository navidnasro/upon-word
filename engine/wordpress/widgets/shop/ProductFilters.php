<?php

namespace engine\wordpress\widgets\shop;

use engine\utils\Woocommerce;
use engine\wordpress\widgets\Register;
use WP_Widget;

defined('ABSPATH') || exit;

class ProductFilters extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'ProductFilters-widget', // Base ID
            'فیلتر ویژگی محصولات', // Name
            ['description' => 'فیلتر محصولات براساس ویژگی'],
        );

        //adds meta query to wocoommerce product loop
//        if (isset($_GET['stock']))
//            add_filter('woocommerce_product_query_meta_query',[$this,'addCustomMetaQuery'],10,2);
    }

    /**
     * Adding custom meta query
     *
     * @param array $metaQuery
     * @return array
     */
    public function addCustomMetaQuery(array $metaQuery): array
    {
        if (is_shop() || is_product_category())
        {
            $metaQuery[] = [
                'key'     => '_stock_status',
                'value'   => 'instock',
            ];
        }

        return $metaQuery;
    }

    /**
     * Widget output in front-end
     *
     * @param $args
     * @param $instance
     * @return void
     */
    public function widget($args , $instance): void
    {
        $attributes = Woocommerce::getAllAttributes();

        echo $args['before_widget'];

        foreach ($attributes as $attributeLabel => $data)
        {
            ?>
            <div class="product-filter w-full">
                <div class="product-attribute-label flex items-center justify-between w-full py-5 px-[5px] cursor-pointer">
                    <span class="font-bold text-[15px] text-darkblue">
                        <?php echo $attributeLabel ?>
                    </span>
                    <span class="flex items-center justify-center rotate-180">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6" viewBox="0 0 11 6" fill="none">
                            <path d="M4.78227 0.28283C4.73296 0.333115 4.69133 0.388788 4.65513 0.446616L0.876256 4.32738C0.507777 4.70614 0.507603 5.31998 0.876431 5.69892C1.24509 6.07767 1.84301 6.07767 2.21201 5.69892L5.46642 2.35656L8.73796 5.7158C9.10643 6.09473 9.70453 6.09473 10.0735 5.7158C10.2577 5.52633 10.3499 5.27832 10.3499 5.0303C10.3499 4.78229 10.2577 4.53373 10.0732 4.34481L6.2777 0.446616C6.2415 0.388788 6.20005 0.333295 6.15056 0.28283C5.96168 0.0888724 5.7137 -0.00415516 5.46642 0.000154972C5.21896 -0.00433493 4.97062 0.0888724 4.78227 0.28283Z" fill="#0E1935"></path>
                        </svg>
                    </span>
                </div>
                <div class="product-attribute-values-box max-h-0 w-full overflow-hidden">
                    <div class="product-attribute-search search-item-box-fields relative w-full h-[50px] pt-[7px] px-2.5 pb-[3px] mb-3.5 border border-solid border-[#dfe1e8] bg-white rounded-2xl">
                        <button type="button" class="absolute flex items-center justify-center top-[7px] right-[7px] w-8 h-8 bg-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <path d="M12.9742 12.1508L12.9742 12.1508L11.2255 10.4458C12.1333 9.42855 12.6838 8.09702 12.6838 6.63956C12.6838 3.43893 10.0316 0.85 6.76692 0.85C3.50227 0.85 0.85 3.43893 0.85 6.63956C0.85 9.84018 3.50227 12.4291 6.76692 12.4291C8.11591 12.4291 9.36008 11.9872 10.3557 11.243L12.1375 12.9806L12.1372 12.9809L12.1456 12.9879L12.1955 13.0299L12.1952 13.0302L12.2042 13.0367C12.4363 13.2047 12.7646 13.1861 12.9753 12.9795C13.2087 12.7507 13.2081 12.3789 12.9742 12.1508ZM2.03826 6.63956C2.03826 4.09064 4.15218 2.01864 6.76692 2.01864C9.38167 2.01864 11.4956 4.09064 11.4956 6.63956C11.4956 9.18848 9.38167 11.2605 6.76692 11.2605C4.15218 11.2605 2.03826 9.18848 2.03826 6.63956Z" fill="#0E1935" stroke="#0E1935" stroke-width="0.3"></path>
                            </svg>
                        </button>
                        <input type="text" placeholder="<?php echo 'جست و جو در '.$attributeLabel.'...' ?>"
                               class="w-full pr-10 h-10 font-medium text-[15px] pb-1">
                    </div>
                    <div class="product-attribute-values search-items flex flex-col items-start w-full space-y-3.5 max-h-[300px] overflow-y-auto pl-2.5">
                        <?php
                        foreach($data['terms'] as $termName => $termId)
                        {
                            ?>
                            <div class="product-attribute-value search-item flex items-center justify-start w-full cursor-pointer space-x-2.5 space-x-reverse"
                                 data-termId="<?php echo $termId ?>"
                                 data-taxonomy="<?php echo $data['taxonomy'] ?>">
                                <span class="product-attribute-value-checkbox search-item-checkbox relative flex items-center justify-center w-6 h-6 rounded-md bg-mediumstone">
                                    <span class="product-attribute-value-checkmark absolute flex items-center justify-center hidden">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="10" viewBox="0 0 9 7" fill="none">
                                            <path d="M4.07394 6.68073C3.66589 7.10642 3.00381 7.10642 2.59596 6.68073L0.306033 4.29179C-0.102011 3.86631 -0.102011 3.1756 0.306033 2.75012C0.713879 2.32443 1.37597 2.32443 1.78401 2.75012L3.14836 4.17325C3.25135 4.28049 3.41855 4.28049 3.52174 4.17325L7.21599 0.319265C7.62384 -0.106422 8.28592 -0.106422 8.69397 0.319265C8.88991 0.523685 9 0.801039 9 1.0901C9 1.37917 8.88991 1.65652 8.69397 1.86094L4.07394 6.68073Z" fill="#FFFFFF"/>
                                        </svg>
                                    </span>
                                </span>
                                <span class="font-bold search-item-text text-[15px] pt-1">
                                    <?php echo $termName ?>
                                </span>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php
        }
        echo $args['after_widget'];
    }

    /**
     * Widget inputs in admin panel
     *
     * @param $instance
     * @return void
     */
    public function form($instance): void
    {
        // no settings
    }

    /**
     * Updates widget data inserted in admin panel
     *
     * @param $new_instance
     * @param $old_instance
     * @return array
     */
    public function update($new_instance , $old_instance): array
    {
        // no data

        return [];
    }
}

Register::register(new ProductFilters());