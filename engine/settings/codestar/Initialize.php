<?php

namespace engine\settings\codestar;

use CSF;
use engine\enums\Constants;
use engine\security\Escape;

defined('ABSPATH') || exit;

class Initialize
{
    public function __construct()
    {
        // Control core classes for avoid errors
        if(class_exists('CSF'))
        {
            // Create options
            CSF::createOptions(Constants::SettingsObjectID,
                [
                    // framework title
                    'framework_title'         => Escape::htmlWithTranslation('گاج مارکت').' <small>'.Escape::htmlWithTranslation('توسط نوید نصراله نژاد').'</small>',
                    'framework_class'         => '',

                    // menu settings
                    'menu_title'              => Escape::htmlWithTranslation('تنظیمات قالب'),
                    'menu_slug'               => Escape::htmlWithTranslation('تنظیمات-قالب'),
                    'menu_type'               => 'menu',
                    'menu_capability'         => 'manage_options',
                    'menu_icon'               => null,
                    'menu_position'           => 1,
                    'menu_hidden'             => false,
                    'menu_parent'             => '',

                    // menu extras
                    'show_bar_menu'           => true,
                    'show_sub_menu'           => true,
                    'show_in_network'         => true,
                    'show_in_customizer'      => false,

                    'show_search'             => true,
                    'show_reset_all'          => true,
                    'show_reset_section'      => true,
                    'show_footer'             => true,
                    'show_all_options'        => true,
                    'show_form_warning'       => true,
                    'sticky_header'           => true,
                    'save_defaults'           => true,
                    'ajax_save'               => true,

                    // admin bar menu settings
                    'admin_bar_menu_icon'     => '',
                    'admin_bar_menu_priority' => 80,

                    // footer
                    'footer_text'             => '',
                    'footer_after'            => '',
                    'footer_credit'           => '',

                    // database model
                    'database'                => '', // options, transient, theme_mod, network
                    'transient_time'          => 0,

                    // contextual help
                    'contextual_help'         => array(),
                    'contextual_help_sidebar' => '',

                    // typography options
                    'enqueue_webfont'         => true,
                    'async_webfont'           => false,

                    // others
                    'output_css'              => true,

                    // theme and wrapper classname
                    'nav'                     => 'normal',
                    'theme'                   => 'dark',
                    'class'                   => '',

                    // external default values
                    'defaults'                => array(),
                ]
            );

            // Adding SVG support in CodeStar options
            add_filter('upload_mimes',[$this,'allowSvgUpload']);
            add_filter('wp_check_filetype_and_ext',[$this,'fixSvgMimeType'],10);
            add_filter('wp_handle_upload_prefilter',[$this,'sanitizeSvg']);
        }
    }

    public function allowSvgUpload(array $mimes): array
    {
        $mimes['svg'] = 'image/svg+xml';
        return $mimes;
    }

    public function fixSvgMimeType(array $data): array
    {
        $ext = $data['ext'] ?? '';

        if ('svg' === $ext)
            $data['type'] = 'image/svg+xml';

        return $data;
    }

    public function sanitizeSvg(array $file): array
    {
        $file_ext = pathinfo($file['name'],PATHINFO_EXTENSION);

        if ('svg' === $file_ext)
            $file['type'] = 'image/svg+xml';

        return $file;
    }
}

new Initialize();