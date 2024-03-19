<?php

namespace engine\utils;

defined('ABSPATH') || exit;

class QueryUtils
{
    /**
     * @param string $postType
     * @param string $filter
     * @param string $postCount
     * @param string|null $taxonomy
     * @param string $field
     * @param int|array|null $terms
     * @return array
     */
    public static function filterQuery(string $postType,string $filter,string $postCount,string $taxonomy = null,string $field = 'term_id',int|array $terms = null): array
    {
        $query = [];

        if($filter == 'mostVisited')
        {
            $count_key = 'views';

            $query = [
                'post_type' => $postType,
                'meta_key' => $count_key,
                'no_found_rows' => 1,
                'post_status' => 'publish',
                'order' => 'DESC',
                'orderby' => 'meta_value_num',
                'posts_per_page' => $postCount,
            ];
        }
        elseif($filter == 'date')
        {
            $query = [
                'post_type' => $postType,
                'posts_per_page' => $postCount,
                'order' => 'DESC',
            ];
        }
        elseif($filter == 'Bestselling')
        {
            $query = [
                'post_type' => $postType,
                'meta_key' => 'total_sales',
                'orderby' => 'meta_value_num',
                'posts_per_page' => $postCount,
            ];
        }
        elseif($filter == 'popularity')
        {
            $query = [
                'post_type' => $postType,
                'meta_key' => '_wc_average_rating',
                'orderby' => 'meta_value_num',
                'posts_per_page' => $postCount,
            ];
        }
        elseif($filter == 'ascending')
        {
            $query = [
                'post_type' => $postType,
                'posts_per_page' => $postCount,
                'order' => 'ASC',
            ];
        }
        elseif($filter == 'Descending')
        {
            $query = [
                'post_type' => $postType,
                'posts_per_page' => $postCount,
                'order' => 'DESC',
            ];
        }
        elseif($filter == 'discounted')
        {
            $query = [
                'posts_per_page' => $postCount,
                'no_found_rows' => 1,
                'post_status' => 'publish',
                'post_type' => $postType,
                'order' => 'DESC',
                'meta_key' => '_sale_price',
                'meta_query' => [
                    [
                        'key' => '_sale_price',
                        'value' => '0',
                        'type' => 'numeric',
                        'compare' => '>',
                    ],
                ]
            ];
        }
        elseif($filter == 'NumberOFcomments')
        {
            $query = [
                'post_type' => $postType,
                'meta_key' => '_wc_review_count',
                'orderby' => 'meta_value_num',
                'posts_per_page' => $postCount,
            ];
        }
        else
        {
            $query = [
                'post_type' => $postType,
                'posts_per_page' => $postCount,
            ];
        }

        if((!empty($taxonomy) && !empty($terms)) && !empty($query))
            $query['tax_query'] = [
                [
                    'taxonomy' => $taxonomy,
                    'field' => $field,
                    'terms' => $terms,
                ]
            ];

        return $query;
    }
}