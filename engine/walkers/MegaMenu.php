<?php

namespace engine\walkers;

use engine\utils\ElementorUtils;
use Walker_Nav_Menu;

defined('ABSPATH') || exit;

class MegaMenu extends Walker_Nav_Menu
{
    /**
     * @param string $output Passing by Reference
     * @param int $depth
     * @param array $args
     * @return void
     */
    public function start_lvl(&$output, $depth = 0, $args = null) : void
    {
        $output .= '<ul class="menu level-'.$depth.'">';
    }

    /**
     * @param $output
     * @param $dataObject
     * @param $depth
     * @param $args
     * @param $currentObjectID
     * @return void
     */
    public function start_el(&$output, $dataObject, $depth = 0, $args = null, $currentObjectID = 0) : void
    {
        $classes = implode(' ',$dataObject->classes);
        $output .= '<li class="'.$classes.'">';

        if($depth == 0)
        {
            $megaMenu = get_post_meta($dataObject->ID, 'megamenu', true );
            $icon = get_post_meta($dataObject->ID, 'icon', true );

            if(!empty($icon))
            {
                $output .= '<a class="flex items-center space-x-1.5 space-x-reverse" href="'.$dataObject->url.'">';

                if(str_contains($icon,'<svg'))
                    $output .= $icon;
//                    $output .= '<img class="menu-icon mb-[5px]" src="'.wp_get_attachment_image_url($icon,'thumbnail',true).'">';
                else
                    $output .= '<span class="menu-icon"><i class="'.$icon.'"></i></span>';

                $output .= '<span>'.$dataObject->title.'</span>';
                $output .= '</a>';
            }

            else
            {
                $output .= '<a href="'.$dataObject->url.'">';
                $output .= $dataObject->title;
                $output .= '</a>';
            }

            if(!empty($megaMenu))
            {
                $output .= '<ul class="megamenu megamenu-'.$megaMenu.'">';
                $output .= ElementorUtils::getTemplate($megaMenu);
                $output .= '</ul>';
            }
        }

        else
        {
            $output .= '<a href="'.$dataObject->url.'">';
            $output .= $dataObject->title;
            $output .= '</a>';
        }

    }

    public function end_el(&$output, $dataObject, $depth = 0, $args = null) : void
    {
        $output .= '</li>';
    }

    public function end_lvl(&$output, $depth = 0, $args = null) : void
    {
        $output .= '</ul>';
    }
}