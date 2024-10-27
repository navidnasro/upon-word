<?php

namespace engine\settings\codestar;

use CSF_Options;
use engine\enums\Constants;
use engine\utils\CodeStar;
use engine\VarDump;

defined('ABSPATH') || exit;

class SaveSettings
{
    public function __construct()
    {
        add_action('csf_'.Constants::SettingsObjectID.'_save_before',[$this,'save'],10,2);
    }

    /**
     * @param array $options options with updated value
     * @param CSF_Options $instance includes options with previous values
     *
     * @return void
     */
    public function save(array $options,CSF_Options $instance): void
    {
        $prevOptions = $instance->options;

        if (CodeStar::isOptionChanged('logo',$prevOptions,$options))
        {
            update_option('site_logo',$options['logo']['id']);
            update_option('site_icon',$options['favicon']['id']);
        }

        if (CodeStar::isOptionChanged('enableRegistration',$prevOptions,$options))
        {
            $value = $options['enableRegistration'] ? 'yes' : 'no';
            update_option('woocommerce_enable_myaccount_registration',$value);
        }

        if (CodeStar::isOptionChanged('generateUsername',$prevOptions,$options))
        {
            $value = $options['generateUsername'] ? 'yes' : 'no';
            update_option('woocommerce_registration_generate_username',$value);
        }

        if (CodeStar::isOptionChanged('generatePassword',$prevOptions,$options))
        {
            $value = $options['generatePassword'] ? 'yes' : 'no';
            update_option('woocommerce_registration_generate_password',$value);
        }

        if (CodeStar::isOptionChanged('vendorAddressOnRegistration',$prevOptions,$options))
        {
            $value = $options['vendorAddressOnRegistration'] ? 'on' : 'off';
            $dokanOptions = get_option('dokan_general');
            $dokanOptions['enabled_address_on_reg'] = $value;

            update_option('dokan_general',$dokanOptions);
        }

        if (CodeStar::isOptionChanged('enableVendorTermsCondition',$prevOptions,$options))
        {
            $value = $options['enableVendorTermsCondition'] ? 'on' : 'off';
            $dokanOptions = get_option('dokan_general');
            $dokanOptions['seller_enable_terms_and_conditions'] = $value;

            update_option('dokan_general',$dokanOptions);
        }
    }
}

new SaveSettings();