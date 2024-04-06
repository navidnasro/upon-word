<?php

namespace engine\enums;

defined('ABSPATH') || exit;

enum Constants
{
    const IMG = ROOT.'/assets/img'; // path to images folder
    const CSS = ROOT.'/assets/css'; // path to css folder
    const JS  = ROOT.'/assets/js'; // path to js folder
    const Templates = THEME_ROOT.'/templates'; // path to templates folder
    const TextDomain = 'engine'; // text domain name
    const Settings = 'redux'; // settings framework that engine should use
    const SettingsObjectID = 'themeSettings'; // object name that is used by the settings framework
}