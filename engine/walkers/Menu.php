<?php

namespace engine\walkers;

use engine\enums\Constants;
use engine\enums\Defaults;
use engine\utils\Elementor;
use engine\utils\Term;
use Walker_Nav_Menu;

defined('ABSPATH') || exit;

class Menu extends Walker_Nav_Menu
{
    private $dataObject;

    /**
     * @param string $output Passing by Reference
     * @param int $depth
     * @param array $args
     * @return void
     */
    public function start_lvl(&$output, $depth = 0, $args = null) : void
    {
        if ($depth == 1)
        {
            $output .= '<ul class="menu level-'.$depth.' megamenu">';
            $output .= '<div class="flex items-start justify-start space-x-3.5 space-x-reverse flex-wrap w-[80%]">';
        }

        else
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
        // menu item classes
        $classes = implode(' ',$data_object->classes);
        $output .= '<li class="'.$classes.'">';

        // menu item label
        $label = get_post_meta($data_object->ID, 'label', true );
        // menu item label color
        $labelColor = get_post_meta($data_object->ID, 'label-color', true );
        $labelColor = !empty($labelColor) ? 'bg-['.$labelColor.']' : 'bg-rose';

        if($depth == 0)
        {
            $megaMenu = get_post_meta($data_object->ID, 'megamenu', true );
            $icon = get_post_meta($data_object->ID, 'icon', true );

            // if icon uploaded , print menu item with chosen icon
            if(!empty($icon))
            {
                $output .= '<a href="'.$data_object->url.'">';

                if(str_contains($icon,'<svg'))
                    $output .= $icon;

                else
                    $output .= '<span class="menu-icon"><i class="'.$icon.'"></i></span>';

                $output .= '<span>'.$data_object->title.'</span>';

                // if has children , add arrow down icon
                if (in_array('menu-item-has-children',$data_object->classes))
                    $output .= '<svg xmlns="http://www.w3.org/2000/svg" width="7" height="7" viewBox="0 0 7 7" fill="none"><path d="M2.84025 6.20133C2.80642 6.16837 2.66175 6.04392 2.54275 5.92799C1.79433 5.24834 0.569333 3.47532 0.195417 2.54733C0.135333 2.4064 0.00816667 2.05009 0 1.85972C0 1.6773 0.042 1.50341 0.127167 1.33747C0.246167 1.13062 0.433417 0.964686 0.6545 0.873762C0.807917 0.81523 1.267 0.724306 1.27517 0.724306C1.77742 0.633382 2.5935 0.583374 3.49533 0.583374C4.35458 0.583374 5.13742 0.633382 5.64725 0.707826C5.65542 0.71635 6.22592 0.807274 6.42133 0.906722C6.77833 1.08914 7 1.44545 7 1.82676V1.85972C6.99125 2.10805 6.76958 2.6303 6.76142 2.6303C6.38692 3.50828 5.222 5.24038 4.44792 5.93652C4.44792 5.93652 4.249 6.13257 4.12475 6.21781C3.94625 6.35079 3.72517 6.41671 3.50408 6.41671C3.25733 6.41671 3.0275 6.34226 2.84025 6.20133Z" fill="#8A929C"></path></svg>';

                // if label set , print it
                if (!empty($label))
                    $output .= '<span class="text-[12px] font-bold leading-5 px-[5px] rounded-[10px] text-white '.$labelColor.' pb-0.5 mr-[5px]">'.$label.'</span>';

                $output .= '</a>';
            }

            // else print without icon
            else
            {
                $output .= '<a href="'.$data_object->url.'">';
                $output .= $data_object->title;

                // if has children , add arrow down icon
                if (in_array('menu-item-has-children',$data_object->classes))
                    $output .= '<svg xmlns="http://www.w3.org/2000/svg" width="7" height="7" viewBox="0 0 7 7" fill="none"><path d="M2.84025 6.20133C2.80642 6.16837 2.66175 6.04392 2.54275 5.92799C1.79433 5.24834 0.569333 3.47532 0.195417 2.54733C0.135333 2.4064 0.00816667 2.05009 0 1.85972C0 1.6773 0.042 1.50341 0.127167 1.33747C0.246167 1.13062 0.433417 0.964686 0.6545 0.873762C0.807917 0.81523 1.267 0.724306 1.27517 0.724306C1.77742 0.633382 2.5935 0.583374 3.49533 0.583374C4.35458 0.583374 5.13742 0.633382 5.64725 0.707826C5.65542 0.71635 6.22592 0.807274 6.42133 0.906722C6.77833 1.08914 7 1.44545 7 1.82676V1.85972C6.99125 2.10805 6.76958 2.6303 6.76142 2.6303C6.38692 3.50828 5.222 5.24038 4.44792 5.93652C4.44792 5.93652 4.249 6.13257 4.12475 6.21781C3.94625 6.35079 3.72517 6.41671 3.50408 6.41671C3.25733 6.41671 3.0275 6.34226 2.84025 6.20133Z" fill="#8A929C"></path></svg>';

                // if label set , print it
                if (!empty($label))
                    $output .= '<span class="text-[12px] font-bold leading-5 px-[5px] rounded-[10px] text-white '.$labelColor.' pb-0.5 mr-[5px]">'.$label.'</span>';

                $output .= '</a>';
            }

            // if megamenu set , print it wrapped with ul
            if(!empty($megaMenu))
            {
                $output .= '<ul class="cust-megamenu megamenu-'.$megaMenu.'">';
                $output .= Elementor::getTemplate($megaMenu);
                $output .= '</ul>';
            }
        }

        elseif ($depth == 1)
        {
            // if menu item is a taxonomy object , set $data_object to retrieve taxonomy image
            if (in_array('menu-item-has-children',$data_object->classes) && $data_object->type == 'taxonomy')
                $this->dataObject = $data_object;

            $output .= '<a href="'.$data_object->url.'">';
            $output .= $data_object->title;

            // if has children , add arrow down icon
            if (in_array('menu-item-has-children',$data_object->classes))
                $output .= '<svg xmlns="http://www.w3.org/2000/svg" width="7" height="7" viewBox="0 0 7 7" fill="none"><path d="M2.84025 6.20133C2.80642 6.16837 2.66175 6.04392 2.54275 5.92799C1.79433 5.24834 0.569333 3.47532 0.195417 2.54733C0.135333 2.4064 0.00816667 2.05009 0 1.85972C0 1.6773 0.042 1.50341 0.127167 1.33747C0.246167 1.13062 0.433417 0.964686 0.6545 0.873762C0.807917 0.81523 1.267 0.724306 1.27517 0.724306C1.77742 0.633382 2.5935 0.583374 3.49533 0.583374C4.35458 0.583374 5.13742 0.633382 5.64725 0.707826C5.65542 0.71635 6.22592 0.807274 6.42133 0.906722C6.77833 1.08914 7 1.44545 7 1.82676V1.85972C6.99125 2.10805 6.76958 2.6303 6.76142 2.6303C6.38692 3.50828 5.222 5.24038 4.44792 5.93652C4.44792 5.93652 4.249 6.13257 4.12475 6.21781C3.94625 6.35079 3.72517 6.41671 3.50408 6.41671C3.25733 6.41671 3.0275 6.34226 2.84025 6.20133Z" fill="#8A929C"></path></svg>';

            // if label set , print it
            if (!empty($label))
                $output .= '<span class="text-[12px] font-bold leading-5 px-[5px] rounded-[10px] text-white '.$labelColor.' pb-0.5 mr-[5px]">'.$label.'</span>';

            $output .= '</a>';
        }

        else
        {
            $output .= '<a href="'.$data_object->url.'">';
            $output .= $data_object->title;

            // if has children , add arrow down icon
            if (in_array('menu-item-has-children',$data_object->classes))
                $output .= '<svg xmlns="http://www.w3.org/2000/svg" width="7" height="7" viewBox="0 0 7 7" fill="none"><path d="M2.84025 6.20133C2.80642 6.16837 2.66175 6.04392 2.54275 5.92799C1.79433 5.24834 0.569333 3.47532 0.195417 2.54733C0.135333 2.4064 0.00816667 2.05009 0 1.85972C0 1.6773 0.042 1.50341 0.127167 1.33747C0.246167 1.13062 0.433417 0.964686 0.6545 0.873762C0.807917 0.81523 1.267 0.724306 1.27517 0.724306C1.77742 0.633382 2.5935 0.583374 3.49533 0.583374C4.35458 0.583374 5.13742 0.633382 5.64725 0.707826C5.65542 0.71635 6.22592 0.807274 6.42133 0.906722C6.77833 1.08914 7 1.44545 7 1.82676V1.85972C6.99125 2.10805 6.76958 2.6303 6.76142 2.6303C6.38692 3.50828 5.222 5.24038 4.44792 5.93652C4.44792 5.93652 4.249 6.13257 4.12475 6.21781C3.94625 6.35079 3.72517 6.41671 3.50408 6.41671C3.25733 6.41671 3.0275 6.34226 2.84025 6.20133Z" fill="#8A929C"></path></svg>';

            // if label set , print it
            if (!empty($label))
                $output .= '<span class="text-[12px] font-bold leading-5 px-[5px] rounded-[10px] text-white '.$labelColor.' pb-0.5 mr-[5px]">'.$label.'</span>';

            $output .= '</a>';
        }

    }

    public function end_el(&$output, $data_object, $depth = 0, $args = null) : void
    {
        $output .= '</li>';
    }

    public function end_lvl(&$output, $depth = 0, $args = null) : void
    {
        if ($depth == 1)
        {
            $output .= '</div>';

            // if true means , we must print taxonomy image in theme mega menu
            if (!is_null($this->dataObject))
            {
                $term = get_term($this->dataObject->object_id);
                $categoryImage = Term::getThumbnailUrl($term,[300,'auto']);
                $categoryImage = $categoryImage ? $categoryImage : Defaults::TermNoImage;
                $output .= '<div class="flex flex-col items-center justify-center w-[164px] h-full"><img style="max-width: 300px;" class="w-full rounded-3xl" src="'.$categoryImage.'"></div>';

                $this->dataObject = null;
            }

            else
            {
                $output .= '<div class="flex flex-col items-center justify-center w-[164px] h-full"><img style="max-width: 300px;" class="w-full rounded-3xl" src="'.Defaults::TermNoImage.'"></div>';
            }

            $output .= '</ul>';
        }

        else
            $output .= '</ul>';
    }
}