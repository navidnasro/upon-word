<?php

namespace engine\elementor\widgets\main;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Plugin;
use engine\elementor\WidgetControls;
use engine\elementor\widgets\Register;
use engine\enums\Constants;
use engine\enums\Defaults;
use engine\security\Escape;
use engine\utils\CodeStar;
use engine\utils\Query;
use engine\utils\Woocommerce;
use WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Products extends Widget_Base
{
    public function get_name(): string
    {
        return 'Products';
    }
    
    public function get_title(): string
    {
        return Escape::htmlWithTranslation('محصولات');
    }
    
    public function get_icon(): string
    {
        return 'eicon-products-archive';
    }
    
    public function get_categories(): array
    {
        return [ 'main-category' ];
    }
    
    protected function register_controls(): void
    {
        $controls = new WidgetControls($this);

        $controls->startContentSection(
            'display_settings',
            'تنظیمات',
        );

        $controls->addSelectControl(
            'display_style',
            'استایل نمایش',
            [
                'desktop' => Escape::htmlWithTranslation('دسکتاپ'), // Desktop
                'mobile' => Escape::htmlWithTranslation('موبایل'), // Desktop
            ],
            'desktop',
        );

        $controls->endSection();

        $controls->startContentSection(
            'box_settings',
            'تنظیمات باکس'
        );

        $controls->addTextControl(
            'box_title',
            'عنوان باکس',
            'عنوان'
        );

        $controls->addSelectControl(
            'product_cat_select',
            'انتخاب دسته بندی',
            Woocommerce::getProductCats(),
        );

        $controls->addSelectControl(
            'filter_type',
            'نمایش بر اساس',
            Query::getFilterKeys(),
            'date'
        );
        
        $controls->endSection();

        $controls->startStyleSection(
            'style_settings',
            'استایل باکس'
        );

        $controls->addHeading(
            'box_title_heading',
            'عنوان باکس'
        );

        $controls->addTypographyControl(
            'box_title_typography',
            '#category-content-box-title'
        );

        $controls->addColorControl(
            'box_title_color',
            'رنگ',
            [
                '{{WRAPPER}} #category-content-box-title::before' => 'background-color: {{VALUE}} !important;',
                '{{WRAPPER}} #category-content-box-title' => 'border-color: {{VALUE}} !important;',
            ]
        );

        $controls->addHeading(
            'more_button_heading',
            'تنظیمات بیشتر',
            'before'
        );

        $controls->addTypographyControl(
            'more_button_typography',
            '#category-content-box-more-btn'
        );

        $controls->addColorControl(
            'more_button_bg_color',
            'رنگ پس زمینه',
            [
                '{{WRAPPER}} #category-content-box-more-btn' => 'background-color: {{VALUE}} !important;',
                '{{WRAPPER}} #category-content-box-more-btn::before' => 'background-color: {{VALUE}} !important;',
            ]
        );

        $controls->addColorControl(
            'more_button_text_color',
            'رنگ متن',
            [
                '{{WRAPPER}} #category-content-box-more-btn' => 'color: {{VALUE}} !important;',
            ]
        );

        $controls->addColorControl(
            'more_button_icon_color',
            'رنگ آیکن',
            [
                '{{WRAPPER}} #category-content-box-more-btn span svg path' => 'fill: {{VALUE}} !important;',
            ]
        );
        
        $controls->endSection();
    }
    
    protected function render(): void
    {
        
        $settings = $this->get_settings_for_display();
        
        //ID of Selected Term , if nothing selected defaults to uncategorized category (term_id = 1 or 15)
        if(empty($settings['product_cat_select']) || $settings['product_cat_select'] == 0)
        {
            echo 'دسته بندی انتخاب نشده';
        }
        
        else
        {
            //Term Object
            $term = get_term($settings['product_cat_select']);
            //Category Url
            $cat_url = get_term_link($term->term_id);

            $category_query_args = Query::filterQuery('product',$settings['filter_type'],6,$term->taxonomy,'term_id',$settings['product_cat_select']);

            $category_query = new WP_Query( $category_query_args );
    ?>
            <div id="category-content-box">
                <div id="category-content-box-heading"
                     class="border-b border-solid border-stone-200/70 mb-6 flex">
                    <h2 id="category-content-box-title"
                        class="relative text-lg font-bold leading-7 text-darkblue pb-3 pr-3.5 border-b border-solid border-green">
                        <?php echo $settings['box_title']; ?>
                    </h2>
                </div>
                <div id="category-content-box-content-holder"
                     class="flex items-center justify-between space-x-5 space-x-reverse w-full">
                    <div id="category-content-box-content"
                         class="flex items-center justify-start space-x-5 space-x-reverse">
    <?php
                        if( $category_query->have_posts() )
                        {
                            $productCard = CodeStar::getOption('product-card');

                            while( $category_query->have_posts() )
                            {
                                $category_query->the_post();
                                //Product Object
                                $product = wc_get_product( get_the_ID() );

                                if ($productCard == 'default')
                                    require Constants::Templates.'/productCards/default.php';

                                elseif ($product == 'special')
                                    require Constants::Templates.'/productCards/special.php';
                            }

                            wp_reset_postdata();
                        }
    ?>
                    </div>
                    <a id="category-content-box-more-btn"
                       class="relative flex flex-col items-center justify-center rounded-[10px] h-[230px] bg-[#dfe1e8] text-darkblue p-1.5"
                       href="<?php echo $cat_url ?>">
                        <span class="flex items-center justify-center w-full mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="12" viewBox="0 0 16 12" fill="none">
                                <path d="M8.4727 0C10.0227 0 11.4923 0.538182 12.7717 1.53455C14.0512 2.52364 15.1406 3.97091 15.9229 5.78182C15.9814 5.92 15.9814 6.08 15.9229 6.21091C14.3583 9.83273 11.5727 12 8.4727 12H8.46539C5.37272 12 2.58712 9.83273 1.02251 6.21091C0.964015 6.08 0.964015 5.92 1.02251 5.78182C2.58712 2.16 5.37272 0 8.46539 0H8.4727ZM8.4727 3.09091C6.85691 3.09091 5.54819 4.39273 5.54819 6C5.54819 7.6 6.85691 8.90182 8.4727 8.90182C10.0812 8.90182 11.3899 7.6 11.3899 6C11.3899 4.39273 10.0812 3.09091 8.4727 3.09091ZM8.47358 4.18022C9.47523 4.18022 10.2941 4.99476 10.2941 5.9984C10.2941 6.99476 9.47523 7.80931 8.47358 7.80931C7.46462 7.80931 6.64576 6.99476 6.64576 5.9984C6.64576 5.87476 6.66038 5.7584 6.68232 5.64204H6.71887C7.53042 5.64204 8.18844 5.00204 8.21768 4.20204C8.29811 4.18749 8.38584 4.18022 8.47358 4.18022Z" fill="#0E1935"></path>
                            </svg>
                        </span>
                        <span class="text-[11px] font-bold leading-5">مشاهده</span>
                        <span class="text-[11px] font-bold leading-5">بیشتر</span>
                    </a>
                </div>
            </div>
    <?php
        }
    }
}

Register::register(new Products());