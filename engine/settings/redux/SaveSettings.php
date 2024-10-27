<?php

namespace engine\settings\redux;

use engine\enums\Constants;
use engine\settings\ThemeColors;
use Redux_Panel;
use ReduxFramework;

defined('ABSPATH') || exit;

class SaveSettings
{
    public function __construct()
    {
        add_action('redux/options/'.Constants::SettingsObjectID.'/reset',[$this,'globalReset']);
        add_action('redux/options/'.Constants::SettingsObjectID.'/saved',[$this,'globalSave'],10,2);
        add_action('redux/options/'.Constants::SettingsObjectID.'/section/reset',[$this,'sectionReset']);
        add_action('redux/options/'.Constants::SettingsObjectID.'/settings/change',[$this,'optionChange'],10,2);
    }

    /**
     * @param array $reduxObject
     * @param array $changedOptions , changed options with their previous values
     * @return void
     */
    public function globalSave(array $options,array $changedOptions): void
    {
        return;
    }

    public function globalReset(Redux_Panel $redux): void
    {
        return;
    }

    public function sectionReset(Redux_Panel $redux): void
    {
        return;
    }
}

new SaveSettings();