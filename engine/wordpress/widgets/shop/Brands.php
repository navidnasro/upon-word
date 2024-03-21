<?php

namespace engine\wordpress\widgets\shop;

use engine\utils\Woocommerce;
use engine\wordpress\widgets\Register;
use WP_Widget;

defined('ABSPATH') || exit;

class Brands extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'brands-widget', // Base ID
            'فیلتر محصولات براساس برند', // Name
            ['description' => 'فیلتر محصولات براساس موجود برندی که به آن تعلق دارند'],
        );

        //setting the tax query to woocommerce loop only if filter is by brand
        if (!empty($_GET['filter']) && $_GET['filter'] == 'byBrand')
            add_filter('woocommerce_product_query_tax_query',[$this,'addCustomTaxQuery']);
    }

    /**
     * Adding custom tax query
     *
     * @param array $taxQuery
     * @return array
     */
    public function addCustomTaxQuery(array $taxQuery): array
    {
        $terms = [];

        $brands = Woocommerce::getBrands();

        if (!empty($brands))
            foreach ($brands as $brandName)
                if (isset($_GET[$brandName]))
                    $terms[] = $_GET[$brandName];

        if ((is_shop() || is_product_category()) && !empty($terms))
        {
            $taxQuery[] = [
                'taxonomy' => 'brands',
                'field' => 'term_id',
                'terms' => $terms
            ];
        }

        return $taxQuery;
    }

    /**
     * Widget output in front-end
     *
     * @param $args
     * @param $instance
     * @return void
     */
    public function widget($args, $instance): void
    {
        echo $args['before_widget'];
        echo $args['before_title'];
        echo $instance['stock-title'];
        echo $args['after_title'];

        $brands = Woocommerce::getBrands();

        if (!empty($brands))
        {
            $i = 1;
            ?>
            <form class="flex flex-col items-start justify-start space-y-5 w-full">
                <?php
                foreach ($brands as $brandID => $brandName)
                {
                    ?>
                    <div class="brand cursor-pointer w-full flex items-center justify-start space-x-2.5 space-x-reverse <?php echo $i++ > 4 ? 'hidden' : '' ?>">
                        <input class="hover:border-[var(--theme-secondary)] hover:bg-[var(--theme-secondary)] rounded-[5px] border-[2px] w-5 h-[21px] border-solid" type="checkbox" name="<?php echo $brandName ?>" value="<?php echo $brandID ?>" <?php echo isset($_GET[$brandName]) ? 'checked' : '' ?>>
                        <span class="text-[var(--sidebar-item)] font-bold text-[15px]"><?php echo $brandName ?></span>
                    </div>
                    <?php
                }
                ?>
                <?php
                if ($i > 5)
                {
                    //if there are more than 4 print see more
                    ?>
                    <span class="more-text less cursor-pointer text-[var(--theme-secondary)] text-[15px] font-bold">مشاهده همه</span>
                    <?php
                }
                ?>
                <input type="hidden" name="filter" value="byBrand">
                <button type="submit" class="button self-end">فیلتر</button>
            </form>
            <script>
                $(document).ready(function (){

                    $('.more-text').on('click',function (){

                        //show more
                        if ($(this).hasClass('less'))
                        {
                            $(this).text('مشاهده کمتر');
                            $(this).removeClass('less');

                            $(this).siblings('.brand').each(function (){

                                $(this).removeClass('hidden');

                            });
                        }

                        //show less
                        else
                        {
                            $(this).text('مشاهده بیشتر');
                            $(this).addClass('less');

                            $(this).siblings('.brand').each(function (index){

                                if (index > 3)
                                    $(this).addClass('hidden');

                            });
                        }
                    });

                });
            </script>
            <?php
        }

        else
            echo 'هیچ برندی جهت فیلتر وجود ندارد';

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
        ?>
        <label for="<?php echo $this->get_field_name('stock-title'); ?>">عنوان :</label>
        <input id="<?php echo $this->get_field_id('stock-title'); ?>" name="<?php echo $this->get_field_name('stock-title'); ?>" placeholder="برند ها" type="text">
        <?php
    }

    /**
     * Updates widget data inserted in admin panel
     *
     * @param $new_instance
     * @param $old_instance
     * @return array
     */
    public function update($new_instance, $old_instance): array
    {
        $instance = [];

        $instance['stock-title'] = !empty($new_instance['stock-title']) ? $new_instance['stock-title']: 'موجودی';

        return $instance;
    }
}

Register::register(new Brands());