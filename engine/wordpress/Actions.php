<?php

namespace engine\wordpress;

use engine\enums\Constants;
use engine\enums\Defaults;
use engine\utils\CodeStar;
use engine\utils\Theme;
use engine\VarDump;
use WC_Query;
use WP_Query;

defined('ABSPATH') || exit;

class Actions
{
    public function __construct()
    {
        add_action('wp_footer',[$this,'ajaxPreloader']);
        add_action('admin_notices',[$this,'adminNotices']);
    }

    public function adminNotices(): void
    {
        if (!Theme::pluginExists('elementor'))
        {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p>شما افزونه المنتور رو روی سایت خود نصب ندارید!<br>برای کارکرد بهتر قالب نیاز هست تا افزونه المنتور رو نصب کنید.</p>
            </div>
            <?php
        }

        if (!Theme::pluginExists('woocommerce'))
        {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p>شما افزونه ووکامرس رو روی سایت خود نصب ندارید!<br>برای کارکرد بهتر قالب نیاز هست تا افزونه ووکامرس رو نصب کنید.</p>
            </div>
            <?php
        }

    }

    public function ajaxPreloader(): void
    {
        $logo = CodeStar::getOption('logo');
        $style = CodeStar::getOption('preloader-style');

        if ($style == 'style-1')
        {
            ?>
            <div id="loader-overlay" class="fixed hidden space-y-3.5 top-0 z-[10000] w-full h-full flex-col items-center justify-center bg-black/[.6]">
                <div class="flex items-center justify-center">
                    <img src="<?php echo $logo ? $logo['url'] : Defaults::Logo ?>">
                </div>
                <div class="loader"></div>
            </div>
            <?php
        }

        else if ($style == 'style-2')
        {
            ?>
            <div id="loader-overlay" class="default-preloader fixed hidden top-0 right-0 bottom-0 left-0 bg-black/[.35] z-[10000] w-full h-full flex-col items-center justify-center">
                <div class="preloader-content-wrapper space-y-2.5">
                    <div class="flex items-center justify-center">
                        <img src="<?php echo $logo ? $logo['url'] : Defaults::Logo ?>">
                    </div>
                    <div class="preloader-dots">
                        <div class="dot dot1"></div>
                        <div class="dot dot2"></div>
                        <div class="dot dot3"></div>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>

        <?php
    }
}

new Actions();