<?php

namespace engine\utils;

defined('ABSPATH') || exit;

class ThemeUtils
{
	/**
	 *
	 * @param
	 * @return String
	 */
	public static function getDefaultImageUrl(): string
    {
		return IMG.'/default.png';
	}

	public static function getDefaultLogoUrl(): string
    {
		return IMG.'/logo/logo-ribar.png';
	}

	public static function getMenus(): array
    {
		$menus = wp_get_nav_menus();
    
		$menuItems = array();
		
		foreach ($menus as $menu)
			$menuItems[$menu->term_id]=$menu->name;
		
		return $menuItems;
	}

	public static function getMenu($location): ?array
    {
		$locations = get_nav_menu_locations();
        $menu = wp_get_nav_menu_object($locations[$location]);

        return $menu ? [$menu->term_id => $menu->name] : null;
	}

    public static function getMenuElemenets($menu): ?array
    {
        $items = wp_get_nav_menu_items($menu);

        return $items ?: null;
    }

    public static function getRegisteredSidebars(): array
    {
        global $wp_registered_sidebars;
        $sidebars = array();

        foreach($wp_registered_sidebars as $sidebar)
            $sidebars[$sidebar['id']] = $sidebar['name'];

        return $sidebars;
    }
}