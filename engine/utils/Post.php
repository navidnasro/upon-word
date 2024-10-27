<?php

namespace engine\utils;

use WP_Post;
use WP_Query;

defined('ABSPATH') || exit;

class Post
{
    /**
     * Returns created posts
     *
     * @param int $limit
     * @return string[]
     */
    public static function getPosts(int $limit = -1): array
    {
        $templates = get_posts(
            [
                'post_type' => 'post',
                'posts_per_page' => $limit,
            ]
        );

        $choices = array(
            '0' => 'انتخاب کنید',
        );

        if (!empty($templates) && !is_wp_error($templates))
            foreach ($templates as $template)
                $choices[$template->ID] = $template->post_title;

        return $choices;
    }

    /**
     * Retrieves categories
     *
     * @param bool $hide
     * @param bool $addDefault
     * @param bool $rootCats
     * @return array
     */
    public static function getCategories(bool $hide = true, bool $addDefault = true, bool $rootCats = false): array
    {
        $args = [
            'taxonomy' => 'category',
            'hide_empty' => $hide,
        ];

        if($rootCats)
            $args['parent'] = 0;

        $terms = get_terms($args);

        $options = [];

        if($addDefault)
            $options[0] = 'انتخاب کنید';

        if(!empty($terms) && !is_wp_error($terms))
            foreach($terms as $term)
                $options[$term->term_id] = $term->name;

        return $options;
    }

    /**
     * Retrieves posts with a specific post format
     *
     * @param string $postFormat
     * @return array
     */
    public static function getPostWithFormats(string $postFormat): array
    {
        $IDs = array();

        $posts = new WP_Query(
            [
                'post_type' => 'post',
                'tax_query' => [
                    [
                        'taxonomy' => 'post_format',
                        'field' => 'slug',
                        'terms' => [
                            'post-format-'.$postFormat
                        ],
                    ]
                ],
            ]
        );

        foreach($posts->posts as $post)
            $IDs[$post->ID] = $post->post_title;

        return $IDs;
    }

    /**
     * Retrieves a post object
     *
     * @return array|WP_Post|null
     */
    public static function getCurrentPost(): array|WP_Post|null
    {
        if (Elementor::isPreview() || Elementor::isEditor())
            return get_post(CodeStar::getOption('sample-product'));

        else
            return get_post(get_the_ID());
    }
}