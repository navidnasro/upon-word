<?php

namespace engine;

use Elementor\Plugin;
use engine\enums\Constants;
use engine\enums\Defaults;
use engine\security\Sanitize;
use engine\templates\DemoFactory;
use engine\templates\TemplateFactory;
use engine\utils\Adaptive;
use engine\utils\Cookie;
use engine\utils\Label;

defined('ABSPATH') || exit;

class ThemeInitializer
{
    public static ?TemplateFactory $template;

    function __construct()
    {
        add_action('wp_enqueue_scripts',[$this,'enqueueFiles'],999999);
        add_action('widgets_init',[$this,'themeSidebars']);
        add_action('init',[$this,'themeMenus']);
        add_action('after_setup_theme',[$this,'themeSetup']);
		add_action('admin_enqueue_scripts',[$this,'enqueueAdminFiles']);
        add_action('elementor/editor/after_enqueue_scripts',[$this,'elementorEnqueue']);
        add_filter('register_post_type_args',[$this,'editPostTypes'],20,2);
        add_action('init',[$this,'addPostTypes']);

        // defining the template
        self::$template = DemoFactory::getDemoFactory();
    }

    /**
     * Enqueues theme scripts and styles
     *
     * @return void
     */
    public function enqueueFiles(): void
    {
        echo $this->adaptiveScript();

        wp_enqueue_style('fontawesome-css',Defaults::FontawesomeCss);
        wp_enqueue_style('swiper-bundle-css',Defaults::SwiperCss);
        wp_enqueue_style('styles',Defaults::CssFile);

        wp_enqueue_script('tailwind-js',Defaults::Tailwind);
        wp_enqueue_script('cust-jquery-js',Defaults::Jquery);
        wp_enqueue_script('jq',Defaults::JsFile,[],false,'true');
        wp_enqueue_script('fontawesome-js',Defaults::FontawesomeJs,[],false,'true');
        wp_enqueue_script('swiper-bundle-js',Defaults::SwiperJs,[],false,'true');
        wp_enqueue_script('compare',Constants::JS.'/modules/compare.js');
        wp_enqueue_script('favorite',Constants::JS.'/modules/favorites.js');
        wp_enqueue_script('clipboard-savor',Constants::JS.'/modules/clipboard.js');
        wp_enqueue_script('message-util',Constants::JS.'/utils/message.js');
        wp_enqueue_script('quantity',Constants::JS.'/modules/quantity.js');
        wp_enqueue_script('add-remove-to-from-cart-ajax',Constants::JS.'/modules/cart.js');
        wp_add_inline_script(
            'jq',
            'const PHPVARS = '.json_encode(
                [
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'root' => ROOT,
                ]
            ),
            'before'
        );

        // util functions
        wp_enqueue_script('wc-nothing-found',Constants::JS.'/utils/nothing-found-message.js');

        if (is_account_page())
        {
            wp_enqueue_style('login-css-styles', Constants::CSS.'/login.css');
            wp_enqueue_script('login-js-scripts',Constants::JS.'/user-panel.js');
        }

        if (is_cart())
        {
            wp_enqueue_style('cart-css-styles', Constants::CSS.'/cart.css');
            wp_enqueue_script('cart-js-scripts',Constants::JS.'/cart.js');
        }

        // Desktop
        if (Adaptive::isDesktop())
        {
            if (is_shop() || is_product_taxonomy())
            {
                wp_enqueue_style('shop', Constants::CSS.'/shop-desktop.css');
                wp_enqueue_script('wc-shop',Constants::JS.'/adaptive/desktop/wc-archive.js');
            }

            else if (is_product())
            {
                wp_enqueue_style('single', Constants::CSS.'/single-desktop.css');
                wp_enqueue_script('wc-single',Constants::JS.'/adaptive/desktop/wc-single.js');
                wp_enqueue_script('wc-find-variation',Constants::JS.'/utils/find-variation.js');
                wp_enqueue_script('wc-submit-comment',Constants::JS.'/utils/submit-comment.js');
                wp_enqueue_script('wc-image-zoom',Constants::JS.'/utils/image-zoom.js');
            }
        }

        // Mobile , Tablet
        else
        {
            if (is_shop() || is_product_taxonomy())
            {
                wp_enqueue_style('shop',Constants::CSS.'/shop-mobile.css');
                wp_enqueue_script('wc-shop',Constants::JS.'/adaptive/mobile/wc-archive.js');
                wp_enqueue_script('wc-product-filter',Constants::JS.'/utils/mobile-product-filter-ajax.js');
            }

            else if (is_product())
            {
                wp_enqueue_style('single',Constants::CSS.'/single-mobile.css');
                wp_enqueue_script('wc-single',Constants::JS.'/adaptive/mobile/wc-single.js');
                wp_enqueue_script('wc-find-variation',Constants::JS.'/utils/find-variation.js');
                wp_enqueue_script('wc-submit-comment',Constants::JS.'/utils/submit-comment.js');
            }
        }
    }

    /**
     * Enqueues admin scripts and styles
     *
     * @return void
     */
	public function enqueueAdminFiles(): void
    {
		wp_enqueue_media();
	}

    /**
     * Enqueues elementor editor scripts and styles
     *
     * @return void
     */
    public function elementorEnqueue(): void
    {
        if (Plugin::$instance->editor->is_edit_mode())
        {
            wp_enqueue_style('ribar-admin-css',Constants::CSS.'/admin.css');
        }
    }

    /**
     * Setting up theme features
     *
     * @return void
     */
    public function themeSetup(): void
    {
        add_theme_support('title-tag');
        add_theme_support('automatic-feed-links' );
        add_theme_support('post-formats',['video']);
        add_theme_support('post-thumbnails');
        add_theme_support('woocommerce');

//    	add_theme_support('wc-product-gallery-zoom');
//    	add_theme_support('wc-product-gallery-lightbox');
//    	add_theme_support('wc-product-gallery-slider');

        add_image_size('product_thumb',150,150,true);
        add_image_size('archive_category_thumb',50,50,true);


        add_image_size('article', 370,263,true);
        add_image_size('product-small', 116,115,true);
        add_image_size('product-search', 58,58,true);
        add_image_size('product-big', 454,515,true);
        add_image_size('article-single', 1200,600,true);
        add_image_size('article-related', 580,400,true);
        add_image_size('product-offer', 420,420,true);

        $this->createDefaultLogo();
        $this->createDefaultFavicon();
    }

    /**
     * Registering theme menus
     *
     * @return void
     */
    public function themeMenus(): void
    {
        register_nav_menus(
            [
                'main-menu' => 'جایگاه منوی اصلی',
            ]
        );
    }

    /**
     * Registering theme sidebars
     *
     * @return void
     */
    public function themeSidebars(): void
    {
        unregister_sidebar('sidebar-store');

        register_sidebar(
            [
                'name'          => 'بلاگ',
                'id'            => 'blog',
                'before_widget' => '<div id="product-filters" class="relative flex flex-col items-start w-full rounded-3xl bg-white px-3.5">',
                'after_widget'  => '</div>',
            ]
        );

        register_sidebar(
            [
                'name'          => 'فروشگاه',
                'id'            => 'shop',
                'before_widget' => '<div id="product-filters" class="relative flex flex-col items-start w-full rounded-3xl bg-white px-3.5">',
                'after_widget'  => '</div>',
                'before_title' => '<div class="product-attribute-label flex items-center justify-between w-full py-5 px-[5px] cursor-pointer"><span class="font-bold text-[15px] text-darkblue">',
                'after_title' => '</span><span class="flex items-center justify-center rotate-180"><svg xmlns="http://www.w3.org/2000/svg" width="11" height="6" viewBox="0 0 11 6" fill="none"><path d="M4.78227 0.28283C4.73296 0.333115 4.69133 0.388788 4.65513 0.446616L0.876256 4.32738C0.507777 4.70614 0.507603 5.31998 0.876431 5.69892C1.24509 6.07767 1.84301 6.07767 2.21201 5.69892L5.46642 2.35656L8.73796 5.7158C9.10643 6.09473 9.70453 6.09473 10.0735 5.7158C10.2577 5.52633 10.3499 5.27832 10.3499 5.0303C10.3499 4.78229 10.2577 4.53373 10.0732 4.34481L6.2777 0.446616C6.2415 0.388788 6.20005 0.333295 6.15056 0.28283C5.96168 0.0888724 5.7137 -0.00415516 5.46642 0.000154972C5.21896 -0.00433493 4.97062 0.0888724 4.78227 0.28283Z" fill="#0E1935"></path></svg></span></div>',
            ]
        );

        register_sidebar(
            [
                'name'          => 'صفحه محصول',
                'id'            => 'product',
                'before_widget' => '<div id="buy-box" class="relative bg-white rounded-3xl w-full p-5 space-y-2.5 w-full">',
                'after_widget'  => '</div>',
                'before_title' => '<div class="product-attribute-label flex items-center justify-between w-full py-5 px-[5px] cursor-pointer"><span class="font-bold text-[15px] text-darkblue">',
                'after_title' => '</span></div>',
            ]
        );
    }

    /**
     * Adds custom post types
     *
     * @return void
     */
    public function addPostTypes(): void
    {
        $megaMenuLabels = Label::getLabel('مگامنو');

        register_post_type('megamenu',
            [
                'labels' => $megaMenuLabels,
                'public'             => true,
                'show_in_menu'       => true,
                'show_in_nav_menus'  => true,
                'query_var'          => true,
                'capability_type'    => 'post',
                'hierarchical'       => false,
                'menu_position' => null,
            ]
        );
    }

    /**
     * Edits Post types
     *
     * @param $args
     * @param $postType
     * @return mixed
     */
    public function editPostTypes($args,$postType): mixed
    {
        if ($postType == "post")
            $args['has_archive'] = true;

        return $args;
    }

    /**
     * Creates default logo file and stores it in uploads folder
     *
     * @return void
     */
    private function createDefaultLogo(): void
    {
        $logo = get_option('theme_default_logo',0);

        // Validate current setting if set. If set, return.
        if (!empty($logo))
        {
            if (!is_numeric($logo))
                return;

             else if (wp_attachment_is_image($logo))
                return;
        }

        $uploadDir = wp_upload_dir();
        $source     = Defaults::Logo;
        $filename   = $uploadDir['basedir'].'/theme_logo.png';

        if (!file_exists($source))
            return;

        //if file doesn't exist in upload directory
        if (!file_exists($filename))
            copy($source, $filename); // @codingStandardsIgnoreLine.

        else
            return;

        $filetype = wp_check_filetype(basename($filename));

        $attachment = [
            'guid'           => $uploadDir['url'].'/'.basename($filename),
            'post_mime_type' => $filetype['type'],
            'post_title'     => preg_replace('/\.[^.]+$/','',basename($filename)),
            'post_content'   => '',
            'post_status'    => 'inherit',
        ];

        $attachmentId = wp_insert_attachment($attachment,$filename);

        if (is_wp_error($attachmentId))
        {
            update_option('theme_default_logo',0);
            return;
        }

        update_option('theme_default_logo',$attachmentId);

        // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
        require_once ABSPATH.'wp-admin/includes/image.php';

        // Generate the metadata for the attachment, and update the database record.
        $attachmentData = wp_generate_attachment_metadata($attachmentId,$filename);
        wp_update_attachment_metadata($attachmentId,$attachmentData);
    }

    /**
     * Creates default favicon file and stores it in uploads folder
     *
     * @return void
     */
    private function createDefaultFavicon(): void
    {
        $favicon = get_option('theme_default_favicon',0);

        // Validate current setting if set. If set, return.
        if (!empty($favicon))
        {
            if (!is_numeric($favicon))
                return;

            else if (wp_attachment_is_image($favicon))
                return;
        }

        $uploadDir = wp_upload_dir();
        $source     = Defaults::Favicon;
        $filename   = $uploadDir['basedir'].'/theme_favicon.png';

        if (!file_exists($source))
            return;

        //if file doesn't exist in upload directory
        if (!file_exists($filename))
            copy($source, $filename); // @codingStandardsIgnoreLine.

        else
            return;

        $filetype = wp_check_filetype(basename($filename));

        $attachment = [
            'guid'           => $uploadDir['url'].'/'.basename($filename),
            'post_mime_type' => $filetype['type'],
            'post_title'     => preg_replace('/\.[^.]+$/','',basename($filename)),
            'post_content'   => '',
            'post_status'    => 'inherit',
        ];

        $attachmentId = wp_insert_attachment($attachment,$filename);

        if (is_wp_error($attachmentId))
        {
            update_option('theme_default_favicon',0);
            return;
        }

        update_option('theme_default_favicon',$attachmentId);

        // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
        require_once ABSPATH.'wp-admin/includes/image.php';

        // Generate the metadata for the attachment, and update the database record.
        $attachmentData = wp_generate_attachment_metadata($attachmentId,$filename);
        wp_update_attachment_metadata($attachmentId,$attachmentData);
    }

    /**
     * Implements adaptive design
     *
     * @return string
     */
    private function adaptiveScript(): string
    {
        ob_start();

        //codes outside php tags are javascript
        ?>
        <script>

            //user screen size
            var screenWidth = screen.width;

            <?php
            //if screenwidth cookie is not set , set it and reload the page to get added among request sent cookies
            if(!Cookie::exists('screenwidth'))
            {
                ?>
                document.cookie = 'screenwidth='+screenWidth+';path=/';
                location.reload();
                <?php
            }

            //screenwidth cookie is set
            else
            {
                ?>
                var screenWidthCookie = <?php echo Sanitize::number($_COOKIE['screenwidth']) ?>;

                //if current width is not equal to cookie value , update the cookie and reload the page
                if(screenWidth !== screenWidthCookie)
                {
                    //overwriting the old cookie
                    document.cookie = 'screenwidth='+screenWidth+';path=/';
                    location.reload();
                }
                <?php
            }
            ?>
        </script>
        <?php

        return ob_get_clean();
    }
}

new ThemeInitializer();