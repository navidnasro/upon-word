<?php

namespace engine\admin\fields\users;

defined('ABSPATH') || exit;

use engine\enums\Constants;
use engine\security\Escape;
use engine\storage\Storage;
use engine\utils\User;
use WP_User;

class ExtraAccountInfo
{
    public function __construct()
    {
        add_filter('manage_users_columns', [$this, 'addExtraAccountInfoColumn']);
        add_action('manage_users_custom_column', [$this, 'showExtraAccountInfoColumnContent'], 10, 3);
        add_action('show_user_profile', [$this, 'addExtraAccountInfoField']);
        add_action('edit_user_profile', [$this, 'addExtraAccountInfoField']);
        add_action('personal_options_update', [$this, 'saveExtraAccountInfoField']);
        add_action('edit_user_profile_update', [$this, 'saveExtraAccountInfoField']);
    }

    /**
     * Add a new column to the Users table
     *
     * @param array $columns
     * @return array
     */
    public function addExtraAccountInfoColumn(array $columns): array
    {
        $columns['extra_account_info'] = Escape::htmlWithTranslation('اطلاعات پروفایل کاربر');
        return $columns;
    }

    /**
     * Display the data in the new column
     *
     * @param string $value
     * @param string $columnName
     * @param int $userId
     * @return string
     */
    public function showExtraAccountInfoColumnContent(string $value, string $columnName, int $userId): string
    {
        if ($columnName === 'extra_account_info')
        {
            // Get the data from user meta
            $extraAccountInfo = User::getMeta($userId, 'extra_account_info');

            if ($extraAccountInfo)
            {
                // Create a unique ID for the modal
                $modalId = 'extra-account-info-' . $userId;

                $stateName = Storage::getJsonDataWhere(Constants::Storage.'/json/states.json',$extraAccountInfo['location']['state']);
                $cities = Storage::getJsonDataWhere(Constants::Storage.'/json/cities.json',$extraAccountInfo['location']['state']);

                $gender = $extraAccountInfo['gender'] == 'male' ? Escape::htmlWithTranslation('مرد') : Escape::htmlWithTranslation('زن');

                $formattedInfo = '';
                $formattedInfo .= Escape::htmlWithTranslation('کدملی : ') . esc_html($extraAccountInfo['national_id']) . '<br>';
                $formattedInfo .= Escape::htmlWithTranslation('جنسیت : ') . esc_html($gender) . '<br>';
                $formattedInfo .= Escape::htmlWithTranslation('تلفن همراه :  : ') . esc_html($extraAccountInfo['account_phone_number']) . '<br>';
                $formattedInfo .= Escape::htmlWithTranslation('تاریخ تولد : ') . esc_html($extraAccountInfo['birthdate']['year'] . '-' . $extraAccountInfo['birthdate']['month'] . '-' . $extraAccountInfo['birthdate']['day']) . '<br>';
                $formattedInfo .= Escape::htmlWithTranslation('محل سکونت : ') . $stateName . ', ' . $cities[$extraAccountInfo['location']['city']];

                // Return a button and the hidden content
                return '
                <button type="button" class="button button-primary" onclick="toggleExtraAccountInfo(\'' . esc_attr($modalId) . '\')">'.Escape::htmlWithTranslation('مشاهده همه').'</button>
                <div id="' . esc_attr($modalId) . '" style="display:none; margin-top: 10px; border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9;">
                    ' . $formattedInfo . '
                </div>
                <script>
                    function toggleExtraAccountInfo(id) {
                        var element = document.getElementById(id);
                        if (element.style.display === "none") {
                            element.style.display = "block";
                        } else {
                            element.style.display = "none";
                        }
                    }
                </script>
            ';
            }
        }

        return $value;
    }

    /**
     * Add custom fields to the user profile page
     *
     * @param WP_User $user
     */
    public function addExtraAccountInfoField(WP_User $user): void
    {
        $extraAccountInfo = User::getMeta($user->ID, 'extra_account_info');

        $states = Storage::getJsonContent(Constants::Storage . '/json/states.json');

        if (!empty($extraAccountInfo['location']['state']))
        {
            $cities = Storage::getJsonDataWhere(Constants::Storage.'/json/cities.json',$extraAccountInfo['location']['state']);
        }

        else
        {
            $cities = [];
        }
        ?>
        <h3>
            <?php echo Escape::htmlWithTranslation('اطلاعات پروفایل کاربر') ?>
        </h3>
        <table class="form-table">
            <tr>
                <th><label for="account_national_id">
                        <?php echo Escape::htmlWithTranslation('کد ملی') ?>
                    </label></th>
                <td>
                    <input type="text" name="account_national_id" id="account_national_id" value="<?php echo esc_attr($extraAccountInfo['national_id']); ?>" class="regular-text" /><br />
                </td>
            </tr>
            <tr>
                <th><label for="gender">
                        <?php echo Escape::htmlWithTranslation('جنسیت') ?>
                    </label></th>
                <td>
                    <label><input type="radio" name="gender" value="male" <?php checked($extraAccountInfo['gender'], 'male'); ?> /> <?php echo Escape::htmlWithTranslation('مرد') ?></label>
                    <label><input type="radio" name="gender" value="female" <?php checked($extraAccountInfo['gender'], 'female'); ?> /> <?php echo Escape::htmlWithTranslation('زن') ?></label>
                </td>
            </tr>
            <tr>
                <th><label for="account_phone_number">
                        <?php echo Escape::htmlWithTranslation('تلفن همراه') ?>
                    </label></th>
                <td>
                    <input type="text" name="account_phone_number" id="account_phone_number" value="<?php echo esc_attr($extraAccountInfo['account_phone_number']); ?>" class="regular-text" /><br />
                </td>
            </tr>
            <tr>
                <th><label>
                        <?php echo Escape::htmlWithTranslation('تاریخ تولد') ?>
                    </label></th>
                <td>
                    <input type="text" name="birthdate_day" id="birthdate_day" placeholder="Day" value="<?php echo esc_attr($extraAccountInfo['birthdate']['day']); ?>" class="small-text" />
                    <input type="text" name="birthdate_month" id="birthdate_month" placeholder="Month" value="<?php echo esc_attr($extraAccountInfo['birthdate']['month']); ?>" class="small-text" />
                    <input type="text" name="birthdate_year" id="birthdate_year" placeholder="Year" value="<?php echo esc_attr($extraAccountInfo['birthdate']['year']); ?>" class="small-text" /><br />
                </td>
            </tr>
            <tr>
                <th><label for="location_state">
                        <?php echo Escape::htmlWithTranslation('استان') ?>
                    </label></th>
                <td>
                    <select name="location_state" id="location_state" class="regular-text">
                        <?php foreach ($states as $stateCode => $stateName): ?>
                            <option value="<?php echo esc_attr($stateCode); ?>" <?php selected($extraAccountInfo['location']['state'], $stateCode); ?>>
                                <?php echo esc_html($stateName); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="location_city">
                        <?php echo Escape::htmlWithTranslation('شهر') ?>
                    </label></th>
                <td>
                    <select name="location_city" id="location_city" class="regular-text">
                        <?php foreach ($cities as $cityCode => $cityName): ?>
                            <option value="<?php echo esc_attr($cityCode); ?>" <?php selected($extraAccountInfo['location']['city'], $cityCode); ?>>
                                <?php echo esc_html($cityName); ?>
                            </option>
                        <?php endforeach; ?>
                    </select><br />
                </td>
            </tr>
        </table>

        <script>
            jQuery(document).ready(function($)
            {
                var cities = [];

                $('#location_state').on('change',function ()
                {
                    var state = $(this).val();
                    var selector = $('#location_city');

                    if (cities.hasOwnProperty(state))
                    {
                        selector.empty();
                        selector.append(cities[state]);

                        return;
                    }

                    $.ajax(
                        {
                            type: "post",
                            url: url.ajax_url,
                            data: {
                                action: 'getStateCities',
                                state: state,
                            },
                            success: function (response)
                            {
                                if(response.success)
                                {
                                    selector.empty();
                                    selector.append(response.data);

                                    cities[state] = response.data;
                                }
                            },
                        }
                    );
                });
            });
        </script>
        <?php
    }

    /**
     * Save custom fields data from the user profile page
     *
     * @param int $userId
     * @return void
     */
    public function saveExtraAccountInfoField(int $userId): void
    {
        if (!current_user_can('edit_user', $userId))
            return;

        $data = [
            'national_id' => sanitize_text_field($_POST['account_national_id']),
            'gender' => sanitize_text_field($_POST['gender']),
            'account_phone_number' => sanitize_text_field($_POST['account_phone_number']),
            'birthdate' => [
                'day' => sanitize_text_field($_POST['birthdate_day']),
                'month' => sanitize_text_field($_POST['birthdate_month']),
                'year' => sanitize_text_field($_POST['birthdate_year']),
            ],
            'location' => [
                'state' => sanitize_text_field($_POST['location_state']),
                'city' => sanitize_text_field($_POST['location_city']),
            ],
        ];

        User::updateOrAddMeta($userId, 'extra_account_info', $data);
    }
}

// Initialize the class
new ExtraAccountInfo();
