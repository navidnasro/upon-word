<?php

namespace engine\utils;

use Elementor\Plugin;

defined('ABSPATH') || exit;

class ElementorUtils
{
    public static function getTemplates($limit=-1): array
    {
        $templates = get_posts(
            [
                'post_type' => ['elementor_library','page'],
                'posts_per_page' => $limit,
            ]
        );

        $choices = array(
            '0' => 'پیش فرض',
        );

        if (!empty($templates) && !is_wp_error($templates)) 
            foreach ($templates as $template)
                $choices[$template->ID] = $template->post_title;

        return $choices;
    }

    public static function getPages(): array
    {
        $templates = get_posts(
            [
                'post_type' => 'page',
                'posts_per_page' => -1,
            ]
        );

        $choices = array(
            '0' => 'پیش فرض',
        );

        if (!empty($templates) && !is_wp_error($templates)) 
            foreach ($templates as $template)
                $choices[$template->ID] = $template->post_title;

        return $choices;
    }

    /**
     * Determines if it is elementor editor page
     *
     * @return bool
     */
    public static function isEditor(): bool
    {
        return (isset($_GET['action']) && $_GET['action'] == 'elementor') ||
               (isset($_GET['post_type']) && $_GET['post_type'] == 'elementor_library') ||
               (isset($_GET['elementor_library']) && isset($_GET['elementor-preview'])) ||
                Plugin::$instance->editor->is_edit_mode();
    }

    /**
     * Determines if it is elementor preview page
     *
     * @return bool
     */
    public static function isPreview(): bool
    {
		return (isset($_GET['elementor_library']) ||
               (isset($_GET['preview_id'])) ||
               (isset($_GET['preview']) && $_GET['preview'] == true) ||
                Plugin::$instance->preview->is_preview_mode());
	}

    public static function printTemplate($id)
    {
        $content = Plugin::$instance->frontend->get_builder_content_for_display($id);

        if (empty($content)) 
            return 'empty';

        echo $content;
    }

    public static function getTemplate($id): string
    {
        $content = Plugin::$instance->frontend->get_builder_content_for_display($id);

        if (empty($content))
            return 'empty';

        return $content;
    }
}