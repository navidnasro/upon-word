<?php

namespace engine\utils;

defined('ABSPATH') || exit;

class TagsGenerator
{
    /**
     * @param Integer $count
     * @param Integer $post_id
     * @return String $tags
     */
    public static function Generate($separator,$count,$post_id)
    {
        $post_tags = get_the_tags($post_id);
        $tags = '<ul>';

        $count = $count > count($post_tags) ? count($post_tags) : $count;
        
        for($i=0 ; $i<$count ; $i++)
        {
            $tags .= '<li>'.
                        '<a href="'.get_tag_link($post_tags[$i]->term_id).'">'.
                            $post_tags[$i]->name.$separator.
                        '</a>'.
                    '</li>';
        }

        $tags .= '</ul>';

        return $tags;
    }
}