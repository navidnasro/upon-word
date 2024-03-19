<?php

namespace engine\wc\single;

class Filters
{
    public function __construct()
    {
        add_filter('woocommerce_product_tabs',[self::class,'productTabs']);
        add_filter('woocommerce_product_additional_information_heading',[self::class,'tabHeading']);
        add_filter('woocommerce_product_description_heading',[self::class,'tabHeading']);
        add_filter('woocommerce_product_related_products_heading',[self::class,'relatedHeading']);
        add_filter('woocommerce_output_related_products_args',[self::class,'relatedArgs']);
        add_filter('comment_form_submit_button',[self::class,'submitButton']);
    }

    /**
     * Modifies Product tabs on product single page
     *
     * @param array $tabs
     * @return array
     */
    public static function productTabs(array $tabs = [])
    {
        global $product, $post;

        // Additional information tab - shows attributes.
        if ($product && ( $product->has_attributes() ||
            apply_filters( 'wc_product_enable_dimensions_display', $product->has_weight() ||
                $product->has_dimensions() )))
        {
            $tabs['additional_information'] = [
                'title'    => 'مشخصات محصول',
                'priority' => 10,
                'callback' => 'woocommerce_product_additional_information_tab',
            ];
        }

        // Description tab - shows product content.
        if ($post->post_content)
        {
            $tabs['description'] = [
                'title'    => 'بررسی تخصصی',
                'priority' => 20,
                'callback' => 'woocommerce_product_description_tab',
            ];
        }

        // Reviews tab - shows comments.
        if (comments_open())
        {
            $tabs['reviews'] = [
                'title'    => 'نظر کاربران',
                'priority' => 30,
                'callback' => 'comments_template',
            ];
        }

        return $tabs;
    }

    /**
     * Modifies outputting string of tab headings
     *
     * @param string $heading
     * @return null
     */
    public static function tabHeading(string $heading = '')
    {
        return null;
    }

    /**
     * Modifies outputting string of related products heading
     *
     * @param string $heading
     * @return string
     */
    public static function relatedHeading(string $heading = ''): string
    {
        return '<div class="flex w-full items-center justify-between mb-[46px] mt-[60px]">'.
            '<h3 class="title text-[var(--theme-primary)] text-xl font-bold">'.
                'محصولات مرتبط'.
            '</h3>'.
            '<a href="'.get_post_type_archive_link('product').'"'.
               'class="flex items-center justify-center space-x-[7px] space-x-reverse">'.
                '<span class="text-[var(--theme-primary)] text-xl font-bold">'.
                    'مشاهده همه'.
                '</span>'.
                '<span class="flex items-center justify-center w-8 h-8">'.
                    '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">'.
                      '<path d="M26.6667 16.3657C26.6667 16.8719 26.2905 17.2903 25.8024 17.3566L25.6667 17.3657L8.08802 17.3649L14.4387 23.6896C14.83 24.0793 14.8314 24.7124 14.4417 25.1038C14.0875 25.4596 13.532 25.4931 13.1399 25.2034L13.0275 25.1068L4.96084 17.0748C4.90925 17.0235 4.86444 16.9679 4.82641 16.9092C4.81567 16.8915 4.80474 16.8734 4.79437 16.855C4.78484 16.8392 4.77622 16.8228 4.76809 16.8062C4.75681 16.782 4.74578 16.7571 4.73576 16.7316C4.72762 16.712 4.72083 16.6929 4.71463 16.6737C4.70726 16.6498 4.70011 16.6244 4.69395 16.5986C4.68937 16.5806 4.68575 16.5632 4.68258 16.5458C4.67814 16.5199 4.67435 16.4931 4.67165 16.466C4.66932 16.4453 4.66792 16.4248 4.66714 16.4042C4.66694 16.3918 4.66669 16.3788 4.66669 16.3657L4.66719 16.3269C4.66795 16.3073 4.66929 16.2877 4.67121 16.2681L4.66669 16.3657C4.66669 16.3026 4.67253 16.2408 4.68371 16.181C4.6863 16.1667 4.68939 16.152 4.69281 16.1374C4.69991 16.1073 4.70812 16.0783 4.71758 16.0499C4.72222 16.0358 4.72762 16.0207 4.7334 16.0057C4.74508 15.9756 4.75779 15.9469 4.77176 15.919C4.77825 15.9058 4.78552 15.8921 4.79313 15.8785C4.80563 15.8563 4.8185 15.8352 4.83212 15.8146C4.84172 15.8 4.85236 15.7849 4.86346 15.7699L4.87212 15.7584C4.89906 15.7232 4.92829 15.6899 4.95958 15.6586L4.96078 15.6576L13.0274 7.62432C13.4188 7.2346 14.0519 7.23591 14.4417 7.62724C14.7959 7.983 14.8271 8.53862 14.5358 8.9295L14.4387 9.04146L8.09069 15.3649L25.6667 15.3657C26.219 15.3657 26.6667 15.8134 26.6667 16.3657Z" fill="var(--theme-primary)"/>'.
                    '</svg>'.
                '</span>'.
            '</a>'.
        '</div>';
    }

    /**
     * Modifies args for related products slider
     *
     * @param array $args
     * @return int[]
     */
    public static function relatedArgs(array $args = []): array
    {
        return [
            'posts_per_page' => 12,
            'columns'        => 6,
            'orderby'        => 'rand', // @codingStandardsIgnoreLine.
        ];
    }

    /**
     * Modifies html for comment form submit button
     *
     * @return void
     */
    public static function submitButton(): void
    {
        echo '<input name="submit" type="submit" id="submit" class="submit" value="فرستادن دیدگاه">';
    }
}

new Filters();