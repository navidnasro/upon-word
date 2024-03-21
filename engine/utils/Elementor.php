<?php

namespace engine\utils;

use Elementor\Plugin;

defined('ABSPATH') || exit;

class Elementor
{
    /**
     * Returns all elementor templates and pages
     *
     * @param int $limit
     * @return string[]
     */
    public static function getTemplates(int $limit = -1): array
    {
        $templates = get_posts(
            [
                'post_type' => ['elementor_library','page'],
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
     * Determines if it is elementor editor page
     *
     * @return bool
     */
    public static function isEditor(): bool
    {
        return ((isset($_GET['action']) && $_GET['action'] == 'elementor') ||
               (isset($_GET['post_type']) && $_GET['post_type'] == 'elementor_library') ||
               (isset($_GET['elementor_library']) && isset($_GET['elementor-preview'])) ||
                Plugin::$instance->editor->is_edit_mode());
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

    /**
     * Prints the content of an elementor template
     *
     * @param int $id
     * @param bool $withCss Optional. Whether to retrieve the content with CSS or not. Default is false
     * @return string|void
     */
    public static function printTemplate(int $id,bool $withCss = false)
    {
        $content = Plugin::$instance->frontend->get_builder_content_for_display($id,$withCss);

        if (empty($content)) 
            return 'empty';

        echo $content;
    }

    /**
     * Returns the content of an elementor template
     *
     * @param int $id
     * @param bool $withCss Optional. Whether to retrieve the content with CSS or not. Default is false
     * @return string
     */
    public static function getTemplate(int $id,bool $withCss = false): string
    {
        $content = Plugin::$instance->frontend->get_builder_content_for_display($id,$withCss);

        if (empty($content))
            return 'empty';

        return $content;
    }
}