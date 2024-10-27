<?php

namespace engine\walkers;

use engine\enums\Defaults;
use engine\utils\Elementor;
use engine\utils\Term;
use engine\utils\Walker;
use Walker_Nav_Menu;
use WP_Post;

defined('ABSPATH') || exit;

class MobileMenu extends Walker_Nav_Menu
{
    private Walker $dataObject; // represents each li item object
    private array $menuItems; //holds data objects of items in slider

    /**
     * @param string $output Passing by Reference
     * @param int $depth
     * @param array $args
     * @return void
     */
    public function start_lvl(&$output, $depth = 0, $args = null) : void
    {
        if ($depth == 0)
        {
            ob_start();
            ?>
            <div class="w-full h-full fixed top-0 left-full z-20 bg-white overflow-y-scroll mobile-menu-box level-<?php echo $depth ?>">

                <!-- Menu Headings -->

                <div id="menu-heading"
                     class="relative w-full bg-green p-5">
                    <div id="menu-level-heading"
                         class="w-full bg-green p-5 flex items-center justify-start text-white fill-white absolute top-0 left-full z-[9]">
                        <span id="prev-level-btn"
                              class="ml-2.5 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" viewBox="0 0 14 12" fill="none">
                                <path opacity="0.8" d="M13.7762 5.28765L8.72033 0.223885C8.57601 0.079339 8.38365 -1.46584e-06 8.17855 -1.42998e-06C7.97322 -1.39408e-06 7.78098 0.0794531 7.63665 0.223885L7.17762 0.68374C7.03341 0.828058 6.95396 1.02082 6.95396 1.22636C6.95396 1.43178 7.03341 1.63104 7.17762 1.77536L10.1272 4.73592L0.756336 4.73592C0.333835 4.73592 8.85976e-07 5.06719 9.59981e-07 5.49045L1.07365e-06 6.14057C1.14766e-06 6.56383 0.333836 6.9285 0.756336 6.9285L10.1606 6.9285L7.17773 9.90559C7.03352 10.0501 6.95407 10.2377 6.95407 10.4432C6.95407 10.6485 7.03352 10.8388 7.17773 10.9832L7.63677 11.4416C7.78109 11.5861 7.97333 11.6649 8.17867 11.6649C8.38377 11.6649 8.57613 11.5851 8.72045 11.4405L13.7763 6.37688C13.921 6.23187 14.0006 6.03831 14 5.83255C14.0005 5.62611 13.921 5.43243 13.7762 5.28765Z" fill="white"></path>
                            </svg>
                        </span>
                        بازگشت به
                        <em id="prev-level-title"
                            class="mr-1 text-base not-italic font-bold">
                            دسته بندی محصولات
                        </em>
                    </div>
                    <span id="menu-close-btn"
                          class="absolute -bottom-1 left-2.5 flex justify-center pt-[3px] w-[104px] text-2xl text-darkblue z-10 cursor-pointer">
                        ×
                    </span>
                </div>

                <!-- Menu Headlines -->

                <div class="flex mobile-submenu-headline pr-[45px] flex-col space-y-1.5 w-full py-[30px] relative text-base text-darkblue">
                    <div class="flex items-center text-lg font-bold text-[var(--darkblue)]">
                        <?php echo $this->dataObject->getTitle() ?>
                    </div>
                    <a id="more-product-text"
                       class="text-[13px] block mt-[5px] text-darkcyan pr-[18px] duration-[0s] font-bold"
                       href="<?php echo $this->dataObject->getUrl() ?>">
                        مشاهده همه از این دسته
                    </a>
                </div>
                <ul style="height: calc(100vh - 143px);"
                    class="pb-[30px] flex flex-col items-start justify-start overflow-y-scroll w-full px-4 relative z-[9]">
            <?php

            $output .= ob_get_clean();
        }

        else if ($depth == 1)
        {
            ob_start();
            ?>
            <div class="w-full h-full fixed top-0 left-full z-20 bg-white overflow-y-scroll mobile-menu-box level-<?php echo $depth ?>">

                <!-- Menu Headings -->

                <div id="menu-heading"
                     class="relative w-full bg-green p-5">
                    <div id="menu-level-heading"
                         class="w-full bg-green p-5 flex items-center justify-start text-white fill-white absolute top-0 left-full z-[9]">
                            <span id="prev-level-btn"
                                  class="ml-2.5 cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" viewBox="0 0 14 12" fill="none">
                                    <path opacity="0.8" d="M13.7762 5.28765L8.72033 0.223885C8.57601 0.079339 8.38365 -1.46584e-06 8.17855 -1.42998e-06C7.97322 -1.39408e-06 7.78098 0.0794531 7.63665 0.223885L7.17762 0.68374C7.03341 0.828058 6.95396 1.02082 6.95396 1.22636C6.95396 1.43178 7.03341 1.63104 7.17762 1.77536L10.1272 4.73592L0.756336 4.73592C0.333835 4.73592 8.85976e-07 5.06719 9.59981e-07 5.49045L1.07365e-06 6.14057C1.14766e-06 6.56383 0.333836 6.9285 0.756336 6.9285L10.1606 6.9285L7.17773 9.90559C7.03352 10.0501 6.95407 10.2377 6.95407 10.4432C6.95407 10.6485 7.03352 10.8388 7.17773 10.9832L7.63677 11.4416C7.78109 11.5861 7.97333 11.6649 8.17867 11.6649C8.38377 11.6649 8.57613 11.5851 8.72045 11.4405L13.7763 6.37688C13.921 6.23187 14.0006 6.03831 14 5.83255C14.0005 5.62611 13.921 5.43243 13.7762 5.28765Z" fill="white"></path>
                                </svg>
                            </span>
                        بازگشت به
                        <em id="prev-level-title"
                            class="mr-1 text-base not-italic font-bold">
                            <?php echo $this->dataObject->getTitle() ?>
                        </em>
                    </div>
                    <span id="menu-close-btn"
                          class="absolute -bottom-1 left-2.5 flex justify-center pt-[3px] w-[104px] text-2xl text-darkblue z-10 cursor-pointer">
                            ×
                        </span>
                </div>

                <!-- Menu Headlines -->

                <div class="mobile-submenu-swiper p-[30px] mx-auto relative overflow-hidden z-[1]">
                    <div class="swiper-wrapper">

                <?php

            $output .= ob_get_clean();
        }
    }

    /**
     * @param $output
     * @param $data_object
     * @param $depth
     * @param $args
     * @param $current_objectid
     * @return void
     */
    public function start_el(&$output, $data_object, $depth = 0, $args = null, $current_objectid = 0) : void
    {
        $this->dataObject = Walker::getDataObject($data_object);

        if ($depth != 1)
        {
            // menu item classes
            $classes = $this->dataObject->getClasses();
            $output .= '<li class="px-[15px] mobile-menu-item '.$classes.'">';
            $output .= '<a class="px-[35px] border-top border-solid block relative border-[#eef1f4] pt-5 pb-[15px]"';

            if (!$this->dataObject->hasChildren())
                $output .= 'href="'.$this->dataObject->getUrl().'">';

            else
                $output .= '>';

            $output .= '<span class="text-[15px] text-[var(--darkblue)]">'.$this->dataObject->getTitle().'</span>';
            $output .= '</a>';
        }

        else
        {
            $this->menuItems[] = $this->dataObject;

            ob_start();

            ?>
            <div class="swiper-slide" id="item-<?php echo $this->dataObject->getMenuItemID() ?>">
                <a class="relative flex flex-col justify-center text-center w-full h-[123px] pt-5 border-[2px] border-solid border-transparent bg-white rounded-[15px] shadow-[0_4px_30px_rgba(0,0,0,.1)]">
                    <?php
                    if ($this->dataObject->getType() == 'taxonomy')
                        $imgUrl = Term::getThumbnailUrl($this->dataObject->getObjectID(),[60,60]);

                    elseif ($this->dataObject->getType() == 'post_type')
                        $imgUrl = get_the_post_thumbnail_url($this->dataObject->getObjectID(),[60,60]);

                    else
                        $imgUrl = Defaults::TermNoImage;
                     ?>
                    <img class="w-max-[60px] h-max-[60px] mx-auto"
                         src="<?php echo $imgUrl ? $imgUrl : Defaults::TermNoImage ?>">
                    <span class="megamenu-swiper-title flex items-center justify-center relative h-10 overflow-hidden mb-0.5 leading-[18px] font-medium text-[15px] text-darkblue">
                        <?php echo $this->dataObject->getTitle() ?>
                    </span>
            <?php

            $output .= ob_get_clean();
        }
    }

    public function end_el(&$output, $data_object, $depth = 0, $args = null) : void
    {
        if ($depth != 1)
            $output .= '</li>';

        else
            $output .= '</a></div>';
    }

    public function end_lvl(&$output, $depth = 0, $args = null) : void
    {
        if ($depth == 0)
            $output .= '</ul></div>';

        elseif ($depth == 1)
        {
            $output .= '</div></div>';

            foreach ($this->menuItems as $menuItem)
                $output .= '<a href="'.$menuItem->getUrl().'" id="item-'.$menuItem->getMenuItemID().'" class="mobile-mega-menu-item-link hidden relative py-5 px-[30px] text-darkcyan font-bold">مشاهده کامل محصولات این دسته</a>';
        }
    }
}