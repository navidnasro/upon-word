<?php

namespace engine\utils;

use WP_Term;

defined('ABSPATH') || exit;

class TermUtils
{
    /**
     * @param WP_Term $term
     * @return void
     */
    public static function getTermCountPlus(WP_Term $term) : void
    {
        $count = $term->count;

        if( strlen( $count ) > 1 )
        {
            //Last digit
            $number = substr($count,0,1);
            for( $i=1 ; $i<=strlen($count)-1 ; $i++ )
                $number.='0';

            echo $number.'+'.' محصول';
        }
        else
            echo $count.' محصول';
    }

    /**
     * @param WP_Term|int $term
     * @param bool $getText
     * @param string $separator
     * @return array|string
     */
    public static function getTermAncestors(WP_Term|int $term, bool $getText = false, string $separator = '/'): array|string
    {
        if (is_numeric($term))
            $term = get_term($term);

        $parents = get_ancestors($term->term_id,'product_cat','taxonomy'); //array of IDs of ancestors from lowest to highest

        //if output is desired as text
        if($getText)
        {
            $parents = array_reverse($parents); //from highest to lowest
            $text = '';

            foreach($parents as $parent)
            {
                $parentTerm = get_term($parent);
                $text .= $parentTerm->name;
                $text .= end($parents) == $parent ? '' : '/';
            }

            return $text;
        }

        return array_reverse($parents); //from highest to lowest
    }

    /**
     * @param WP_Term|int $term
     * @param string $metaKey
     * @return false|string
     */
    public static function getThumbnailUrl(WP_Term|int $term,string $metaKey = 'thumbnail_id'): bool|string
    {
        if (!is_numeric($term))
            $term = $term->term_id;

        $thumbnail_id = get_term_meta($term,$metaKey,true);
        return wp_get_attachment_url($thumbnail_id);
    }

    /**
     * @param WP_Term|int $term
     * @return array
     */
    public static function getDirectChildren(WP_Term|int $term): array
    {
        global $wpdb;

        $childern = [];

        if(is_numeric($term))
            $term = get_term($term);

        $childern = $wpdb->get_col(
            'SELECT term_id 
                    FROM `wp_term_taxonomy` 
                    WHERE parent="'.$term->term_id.'"',
        );

        return $childern;
    }
}