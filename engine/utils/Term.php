<?php

namespace engine\utils;

use engine\database\enums\Table;
use engine\database\QueryBuilder;
use WP_Term;

defined('ABSPATH') || exit;

class Term
{
    /**
     * Returns number of items in a term(category)
     *
     * @param WP_Term $term
     * @param string $label
     * @param bool $withPlus
     * @return string
     */
    public static function getTermCountPlus(WP_Term $term,string $label = '',bool $withPlus = true): string
    {
        $count = $term->count;

        if(strlen($count) > 1)
        {
            /**
             * intval($count/10) => removing most right digit
             * *10 => nearest multiple of 10
             */
            $number = intval($count/10)*10;

            return $withPlus ? $number.'+ '.$label : $number.' '.$label;
        }

        else
            return $count.' '.$label;
    }

    /**
     * @param WP_Term|int $term
     * @param string $objectType The type of object for which we'll be retrieving ancestors. like product_cat
     * @param string $resourceType Type of resource $objectType is. like taxonomy
     * @param bool $getText
     * @param string $separator
     * @return array|string
     */
    public static function getTermAncestors(WP_Term|int $term,string $objectType,string $resourceType,bool $getText = false, string $separator = '/'): array|string
    {
        if (is_numeric($term))
            $term = get_term($term);

        $parents = get_ancestors($term->term_id,$objectType,$resourceType); //array of IDs of ancestors from lowest to highest

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
     * @param array|string $size
     * @param string $metaKey
     * @return false|string
     */
    public static function getThumbnailUrl(WP_Term|int $term,array|string $size = 'thumbnail',string $metaKey = 'thumbnail_id'): bool|string
    {
        if (!is_numeric($term))
            $term = $term->term_id;

        $thumbnail_id = get_term_meta($term,$metaKey,true);
        return wp_get_attachment_image_url($thumbnail_id,$size);
    }

    /**
     * @param WP_Term|int $term
     * @return array
     */
    public static function getDirectChildren(WP_Term|int $term): array
    {
        $builder = new QueryBuilder();

        if(is_numeric($term))
            $term = get_term($term);

//        $childern = $wpdb->get_col(
//            'SELECT term_id
//                    FROM `wp_term_taxonomy`
//                    WHERE parent="'.$term->term_id.'"',
//        );

        return $builder->select('term_id')
            ->from(Table::TERM_TAXONOMY)
            ->where('parent','=',$term->term_id)
            ->getColumn();
    }
}