<?php

namespace engine\wordpress\widgets\shop;

use engine\wordpress\widgets\Register;
use WP_Widget;

defined('ABSPATH') || exit;

class ProductCondition extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'product-condition-widget', // Base ID
            'فیلتر محصولات براساس وضعیت کارکرد', // Name
            ['description' => 'فیلتر محصولات براساس وضعیت کارکردی که دارند(نو یا دست دوم)'],
        );

        //setting the tax query to woocommerce loop only if filter is by brand
        if (!empty($_GET['condition']))
            add_filter('woocommerce_product_query_meta_query',[$this,'addCustomMetaQuery']);
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
                'key'     => 'condition',
                'value'   => $_GET['condition'],
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
        <form class="flex flex-col items-start justify-start space-y-5 w-full">
            <div class="brand cursor-pointer w-full flex items-center justify-start space-x-2.5 space-x-reverse">
                <input class="hover:border-[var(--theme-secondary)] hover:bg-[var(--theme-secondary)] rounded-[5px] border-[2px] w-5 h-[21px] border-solid" type="radio" name="condition" value="new" <?php echo isset($_GET['condition']) && $_GET['condition'] == 'new' ? 'checked' : '' ?>>
                <span class="text-[var(--sidebar-item)] font-bold text-[15px]">نو (پلمپ)</span>
            </div>
            <div class="brand cursor-pointer w-full flex items-center justify-start space-x-2.5 space-x-reverse">
                <input class="hover:border-[var(--theme-secondary)] hover:bg-[var(--theme-secondary)] rounded-[5px] border-[2px] w-5 h-[21px] border-solid" type="radio" name="condition" value="old" <?php echo isset($_GET['condition']) && $_GET['condition'] == 'old' ? 'checked' : '' ?>>
                <span class="text-[var(--sidebar-item)] font-bold text-[15px]">دست دوم</span>
            </div>
            <button type="submit" class="button self-end">فیلتر</button>
        </form>
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

Register::register(new ProductCondition());