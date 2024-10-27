<?php

namespace engine\walkers;

use engine\utils\Elementor;
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
     * @param $data_object
     * @param $depth
     * @param $args
     * @param $current_objectid
     * @return void
     */
    public function start_el(&$output, $data_object, $depth = 0, $args = null, $current_objectid = 0) : void
    {
        $classes = implode(' ',$data_object->classes);
        $output .= '<li class="'.$classes.'">';

        if($depth == 0)
        {
            $megaMenu = get_post_meta($data_object->ID, 'megamenu', true );
            $icon = get_post_meta($data_object->ID, 'icon', true );

            if(!empty($icon))
            {
                $output .= '<a class="flex items-center space-x-1.5 space-x-reverse" href="'.$data_object->url.'">';

                if(str_contains($icon,'<svg'))
                    $output .= $icon;
//                    $output .= '<img class="menu-icon mb-[5px]" src="'.wp_get_attachment_image_url($icon,'thumbnail',true).'">';
                else
                    $output .= '<span class="menu-icon"><i class="'.$icon.'"></i></span>';

                $output .= '<span>'.$data_object->title.'</span>';
                $output .= '</a>';
            }

            else
            {
                $output .= '<a href="'.$data_object->url.'">';
                $output .= $data_object->title;
                $output .= '</a>';
            }

            if(!empty($megaMenu))
            {
                $output .= '<ul class="megamenu megamenu-'.$megaMenu.'">';
                $output .= Elementor::getTemplate($megaMenu);
                $output .= '</ul>';
            }
        }

        else
        {
            $output .= '<a href="'.$data_object->url.'">';
            $output .= $data_object->title;
            $output .= '</a>';
        }

    }

    public function end_el(&$output, $data_object, $depth = 0, $args = null) : void
    {
        $output .= '</li>';
    }

    public function end_lvl(&$output, $depth = 0, $args = null) : void
    {
        $output .= '</ul>';
    }
}