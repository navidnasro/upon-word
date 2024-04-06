<?php

namespace engine\utils;

use engine\security\Escape;
use WP_Query;

defined('ABSPATH') || exit;

class Query
{
    /**
     * Returns number of published posts in a specified post type
     *
     * @param string $postType
     * @return int
     */
    public static function getPostTypeCount(string $postType): int
    {
        $query = new WP_Query(
            [
                'post_type' => $postType,
                'post_status' => 'publish',
                'posts_per_page' => -1,
            ]
        );

        return $query->found_posts;
    }

    /**
     * Generates query parameters with appropriate filters
     *
     * @param string $postType
     * @param string $filter
     * @param int $postCount
     * @param string|null $taxonomy
     * @param string $field
     * @param int|array|null $terms
     * @return array
     */
    public static function filterQuery(string $postType,string $filter,int $postCount = -1,string $taxonomy = null,string $field = 'term_id',int|array $terms = null): array
    {
        $query = [
            'post_type' => $postType,
            'posts_per_page' => $postCount,
        ];

        if ($filter == 'mostVisited')
        {
            $count_key = 'views';

            $query = array_merge($query,
                [
                    'meta_key' => $count_key,
                    'no_found_rows' => 1,
                    'post_status' => 'publish',
                    'order' => 'DESC',
                    'orderby' => 'meta_value_num',
                ]
            );
        }

        elseif ($filter == 'date')
        {
            $query = array_merge($query,
                [
                    'orderby' => 'date',
                    'order' => 'DESC',
                ]
            );
        }

        elseif ($filter == 'Bestselling')
        {
            $query = array_merge($query,
                [
                    'meta_key' => 'total_sales',
                    'orderby' => 'meta_value_num',
                    'order' => 'DESC',
                ]
            );
        }

        elseif ($filter == 'popularity')
        {
            $query = array_merge($query,
                [
                    'meta_key' => '_wc_average_rating',
                    'orderby' => 'meta_value_num',
                    'order' => 'DESC',
                ]
            );
        }

        elseif ($filter == 'ascending')
        {
            $query = array_merge($query,
                [
                    'order' => 'ASC',
                ]
            );
        }

        elseif ($filter == 'Descending')
        {
            $query = array_merge($query,
                [
                    'order' => 'DESC',
                ]
            );
        }

        elseif ($filter == 'discounted')
        {
            $query = array_merge($query,
                [
                    'no_found_rows' => 1,
                    'post_status' => 'publish',
                    'meta_query' => [
                        [
                            'key' => 'sale_percentage',
                            'value' => '0',
                            'type' => 'numeric',
                            'compare' => '>',
                        ],
                    ],
                    'orderby'        => 'meta_value_num',
                    'order'          => 'DESC',
                ]
            );
        }

        elseif ($filter == 'NumberOFcomments')
        {
            $query = array_merge($query,
                [
                    'meta_key' => '_wc_review_count',
                    'orderby' => 'meta_value_num',
                ]
            );
        }

        elseif ($filter == 'price-desc')
        {
            $query = array_merge($query,
                [
                    'orderby' => 'meta_value_num',
                    'meta_key' => '_price',
                    'order' => 'DESC',
                ]
            );
        }

        elseif ($filter == 'price-asc')
        {
            $query = array_merge($query,
                [
                    'orderby' => 'meta_value_num',
                    'meta_key' => '_price',
                    'order' => 'ASC',
                ]
            );
        }

        if ((!empty($taxonomy) && !empty($terms)))
            $query['tax_query'] = [
                [
                    'taxonomy' => $taxonomy,
                    'field' => $field,
                    'terms' => $terms,
                ]
            ];

        return $query;
    }

    /**
     * Returns a list of available filters
     *
     * @param bool $keysOnly
     * @return array|string[]
     */
    public static function getFilterKeys(bool $keysOnly = false): array
    {
        return $keysOnly ?
            [
                'mostVisited',
                'date',
                'NumberOFcomments',
                'discounted',
                'Descending',
                'ascending',
                'Bestselling',
                'popularity',
                'price-desc',
                'price-asc',
//                'rating',
            ] :
            [
                'mostVisited' => Escape::htmlWithTranslation('پربازدید ترین'),
                'date' => Escape::htmlWithTranslation('جدیدترین'),
                'NumberOFcomments' => Escape::htmlWithTranslation('پرمخاطب ترین'),
                'discounted' => Escape::htmlWithTranslation('بیشترین تخفیف'),
                'Descending' => Escape::htmlWithTranslation('نزولی'),
                'ascending' => Escape::htmlWithTranslation('صعودی'),
                'Bestselling' => Escape::htmlWithTranslation('پرفروش ترین'),
                'popularity' => Escape::htmlWithTranslation('محبوب ترین'),
                'price-desc' => Escape::htmlWithTranslation('گرانترین'),
                'price-asc' => Escape::htmlWithTranslation('ارزان ترین'),
//                'rating' => Escape::htmlWithTranslation('امتیاز'),
            ];
    }
}