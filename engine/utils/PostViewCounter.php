<?php

namespace engine\utils;

defined('ABSPATH') || exit;

class PostViewCounter
{
    public static function count(): void
    {
        $post_id = get_the_ID();
        $count_key = 'views';
        $count = get_post_meta($post_id, $count_key,true);

        if(empty( $count ))
        {
            delete_post_meta($post_id, $count_key);
            update_post_meta($post_id, $count_key,'1');
        }
        else
        {
            $count ++;
            update_post_meta($post_id, $count_key,(string) $count);
        }
    }
}