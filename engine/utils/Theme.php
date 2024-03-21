<?php

namespace engine\utils;

use WP_Term;

defined('ABSPATH') || exit;

class Theme
{
    /**
     * Returns all pages
     *
     * @param int $limit
     * @return string[]
     */
    public static function getPages(int $limit = -1): array
    {
        $templates = get_posts(
            [
                'post_type' => 'page',
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
     * Returns array of navigation menus
     *
     * @return array
     */
	public static function getMenus(): array
    {
		$menus = wp_get_nav_menus();
    
		$menuItems = array();
		
		foreach ($menus as $menu)
			$menuItems[$menu->term_id]=$menu->name;
		
		return $menuItems;
	}

    /**
     * Returns menu object of specified location
     *
     * @param string $location
     * @return array|null
     */
	public static function getMenu(string $location): ?array
    {
		$locations = get_nav_menu_locations();
        $menu = wp_get_nav_menu_object($locations[$location]);

        return $menu ? [$menu->term_id => $menu->name] : null;
	}

    /**
     * Returns items of passed menu
     *
     * @param WP_Term|int $menu
     * @return array|null
     */
    public static function getMenuElemenets(WP_Term|int $menu): ?array
    {
        $items = wp_get_nav_menu_items($menu);

        return $items ?: null;
    }

    /**
     * Returns all registered sidebars
     *
     * @return array
     */
    public static function getRegisteredSidebars(): array
    {
        global $wp_registered_sidebars;
        $sidebars = array();

        foreach($wp_registered_sidebars as $sidebar)
            $sidebars[$sidebar['id']] = $sidebar['name'];

        return $sidebars;
    }

    /**
     * submits or updates views for a post
     *
     * @return void
     */
    public static function postViewCount(): void
    {
        $postID = get_the_ID();
        $countKey = 'views';
        $count = get_post_meta($postID, $countKey,true);

        if(empty( $count ))
        {
            delete_post_meta($postID, $countKey);
            update_post_meta($postID, $countKey,'1');
        }
        else
        {
            $count ++;
            update_post_meta($postID, $countKey,(string) $count);
        }
    }
}