<?php

namespace engine\settings\redux;

use engine\settings\ThemeColors;
use Redux_Panel;
use ReduxFramework;

defined('ABSPATH') || exit;

class SaveSettings
{
    public function __construct()
    {
        add_action('redux/options/ribar_options/reset',[$this,'globalReset']);
        add_action('redux/options/ribar_options/saved',[$this,'globalSave'],10,2);
        add_action('redux/options/ribar_options/section/reset',[$this,'sectionReset']);
//        add_action('redux/options/ribar_options/settings/change',[$this,'optionChange'],10,2);
    }

    /**
     * @param array $reduxObject
     * @param array $changedOptions , changed options with their previous values
     * @return void
     */
    public function globalSave(array $options,array $changedOptions): void
    {
        //if option is changed and the value is not default
        if(isset($changedOptions['mainpage-elementor']) && $options['mainpage-elementor'] != 0)
            update_option('default_mainpage',$options['mainpage-elementor']);

        //if option is changed and the value is not default
        if(isset($changedOptions['header-elementor']) && $options['header-elementor'] != 0)
            update_option('default_header',$options['header-elementor']);

        //if option is changed and the value is not default
        if(isset($changedOptions['footer-elementor']) && $options['footer-elementor'] != 0)
            update_option('default_footer',$options['footer-elementor']);

        //if option is changed
        if(isset($changedOptions['logo_option_img']))
            update_option('site_logo',$options['logo_option_img']['id']);

        //if option is changed
        if(isset($changedOptions['favicon_option_img']))
            update_option('site_icon',$options['favicon_option_img']['id']);

        if (ThemeColors::hasColorChanged($changedOptions))
            ThemeColors::modify($options);
    }

    public function globalReset(Redux_Panel $redux): void
    {
//        $options = $redux->parent->options;
        $optionsDefaults = $redux->parent->options_defaults;

        update_option('site_logo',$optionsDefaults['logo_option_img']['id']);
        update_option('site_icon',$optionsDefaults['favicon_option_img']['id']);

        ThemeColors::modify($optionsDefaults);
    }

    public function sectionReset(Redux_Panel $redux): void
    {
        $options = $redux->parent->options;
//        $optionsDefaults = $redux->parent->options_defaults;

        update_option('site_logo',$options['logo_option_img']['id']);
        update_option('site_icon',$options['favicon_option_img']['id']);

        ThemeColors::modify($options);
    }
}

new SaveSettings();