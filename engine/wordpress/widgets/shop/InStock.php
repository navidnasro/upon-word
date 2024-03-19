<?php

namespace engine\wordpress\widgets\shop;

use engine\wordpress\widgets\Register;
use WP_Widget;

defined('ABSPATH') || exit;

class InStock extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'instock-widget', // Base ID
            'فیلتر محصولات موجود', // Name
            ['description' => 'فیلتر محصولات براساس موجود بودن یا نبودن'],
        );

        //adds meta query to wocoommerce product loop
        if (isset($_GET['stock']))
            add_filter('woocommerce_product_query_meta_query',[$this,'addCustomMetaQuery'],10,2);
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
        echo $args['before_widget'];
        echo $args['before_title'];
        echo $instance['stock-title'];
        echo $args['after_title'];
        ?>
        <form class="cursor-pointer flex items-center justify-start space-x-2.5 space-x-reverse">
            <input class="stock-widget hover:border-[var(--theme-secondary)] hover:bg-[var(--theme-secondary)] rounded-[5px] border-[2px] w-5 h-[21px] border-solid" type="checkbox" name="stock" value="instock" <?php echo isset($_GET['stock']) ? 'checked' : '' ?>>
            <span class="textvar(--sidebar-item)] font-bold text-[15px]">نمایش محصولات موجود</span>
        </form>
        <script>
            $(document).ready(function (){

                $('.stock-widget').on('click',function (){

                    $(this).parent().submit();

                });

            });
        </script>
        <?php
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
        <input id="<?php echo $this->get_field_id('stock-title'); ?>" name="<?php echo $this->get_field_name('stock-title'); ?>" placeholder="موجودی" type="text">
        <?php
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
        $instance = [];

        $instance['stock-title'] = !empty($new_instance['stock-title']) ? $new_instance['stock-title']: 'موجودی';

        return $instance;
    }
}

Register::register(new InStock());