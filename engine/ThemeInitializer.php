<?php

namespace engine;

use Elementor\Plugin;
use engine\enums\Constants;
use engine\enums\Defaults;
use engine\security\Sanitize;
use engine\utils\Adaptive;
use engine\utils\Cookie;
use engine\utils\Label;

defined('ABSPATH') || exit;

class ThemeInitializer
{
    function __construct()
    {
        add_action('wp_enqueue_scripts',[$this,'enqueueFiles']);
        add_action('widgets_init',[$this,'themeSidebars']);
        add_action('init',[$this,'themeMenus']);
        add_action('after_setup_theme',[$this,'themeSetup']);
        add_action('admin_enqueue_scripts',[$this,'enqueueAdminFiles']);
//        add_action('elementor/editor/after_enqueue_scripts',[$this,'elementorEnqueue']);
        add_filter('register_post_type_args',[$this,'editPostTypes'],20,2);
        add_action('init',[$this,'addPostTypes']);
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
        wp_enqueue_script('compare',Constants::JS.'/compare.js');
        wp_enqueue_script('favorite',Constants::JS.'/favorites.js');
        wp_add_inline_script(
            'jq',
            'const PHPVARS = ' . json_encode(array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'root' => ROOT,
            )),
            'before'
        );

        // Desktop
        if (Adaptive::isDesktop())
        {
            if (is_shop())
                wp_enqueue_style('shop', Constants::CSS . '/shop-desktop.css');

            else if (is_product())
                wp_enqueue_style('single', Constants::CSS . '/single-desktop.css');
        }

        // Mobile , Tablet
        else
        {
            if (is_shop())
                wp_enqueue_style('shop',Constants::CSS.'/shop-mobile.css');

            else if (is_product())
                wp_enqueue_style('single',Constants::CSS.'/single-mobile.css');
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
//            wp_enqueue_script( 'ribar-admin-js', JS.'/admin.js');
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

//        remove_theme_support( 'widgets-block-editor' );

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
        register_sidebar( array(
            'name'          => 'بلاگ',
            'id'            => 'blog',
            'before_widget' => '<div class="wp-widget overflow-hidden flex flex-col w-full border-b border-solid border-[#DEE2E7]">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="flex mb-5 items-center justify-between w-full"><span class="text-[var(--title)] text-[15px] font-bold">',
            'after_title'   => '</span><span class="flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
  <path d="M3.51042 6.40977C3.71955 6.20065 4.04679 6.18164 4.2774 6.35274L4.34346 6.40977L9.42474 11.4908L14.506 6.40977C14.7152 6.20065 15.0424 6.18163 15.273 6.35274L15.3391 6.40977C15.5482 6.6189 15.5672 6.94615 15.3961 7.17675L15.3391 7.24281L9.84127 12.7406C9.63214 12.9497 9.30489 12.9688 9.07429 12.7977L9.00822 12.7406L3.51042 7.24282C3.28038 7.01278 3.28038 6.63981 3.51042 6.40977Z" fill="#43454D"/>
</svg></span></h4>',
        ) );

        register_sidebar( array(
            'name'          => 'فروشگاه دسکتاپ',
            'id'            => 'shop',
            'before_widget' => '<div class="wp-widget overflow-hidden flex flex-col w-full mb-8 border-b border-solid border-[#DEE2E7]">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="flex mb-5 items-center justify-between w-full"><span class="text-[var(--title)] text-[15px] font-bold">',
            'after_title'   => '</span><span class="flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
  <path d="M3.51042 6.40977C3.71955 6.20065 4.04679 6.18164 4.2774 6.35274L4.34346 6.40977L9.42474 11.4908L14.506 6.40977C14.7152 6.20065 15.0424 6.18163 15.273 6.35274L15.3391 6.40977C15.5482 6.6189 15.5672 6.94615 15.3961 7.17675L15.3391 7.24281L9.84127 12.7406C9.63214 12.9497 9.30489 12.9688 9.07429 12.7977L9.00822 12.7406L3.51042 7.24282C3.28038 7.01278 3.28038 6.63981 3.51042 6.40977Z" fill="#43454D"/>
</svg></span></h4>',
        ) );

        register_sidebar( array(
            'name'          => 'فروشگاه موبایل',
            'id'            => 'shop-mobile',
            'before_widget' => '<div class="wp-widget overflow-hidden flex flex-col w-full mb-8 border-b border-solid border-[#DEE2E7]">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="flex mb-5 items-center justify-between w-full"><span class="text-[var(--title)] text-[15px] font-bold">',
            'after_title'   => '</span><span class="flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
  <path d="M3.51042 6.40977C3.71955 6.20065 4.04679 6.18164 4.2774 6.35274L4.34346 6.40977L9.42474 11.4908L14.506 6.40977C14.7152 6.20065 15.0424 6.18163 15.273 6.35274L15.3391 6.40977C15.5482 6.6189 15.5672 6.94615 15.3961 7.17675L15.3391 7.24281L9.84127 12.7406C9.63214 12.9497 9.30489 12.9688 9.07429 12.7977L9.00822 12.7406L3.51042 7.24282C3.28038 7.01278 3.28038 6.63981 3.51042 6.40977Z" fill="#43454D"/>
</svg></span></h4>',
        ) );
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