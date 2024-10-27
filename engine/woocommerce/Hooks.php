<?php

namespace engine\woocommerce;

use engine\enums\Constants;
use engine\security\Escape;
use engine\security\Nonce;
use engine\storage\Storage;
use engine\utils\CodeStar;
use engine\utils\Notice;
use engine\utils\Request;
use engine\utils\User;
use engine\utils\Woocommerce;
use stdClass;
use WC_Order;
use WC_Query;
use WP_Error;
use WP_Query;
use function Sodium\add;

defined('ABSPATH') || exit;

class Hooks
{
    public function __construct()
    {
//        add_action('woocommerce_product_query',[$this,'shopQuery'],10,2);
        add_action('woocommerce_account_dashboard',[$this,'userInformation'],15);
        add_action('woocommerce_account_dashboard',[$this,'dashboardPanels'],20);

        add_action('woocommerce_edit_account_form_fields',[$this,'editUserInfoFields'],20);

        remove_action('woocommerce_register_form','wc_registration_privacy_policy_text',20);
        add_action('woocommerce_register_form',[$this,'privacyPolicy'],20);

        add_filter('woocommerce_account_menu_items',[$this,'navigationItems']);
        add_filter('loop_shop_per_page',[$this,'shopPostsPerPage'],20);
        add_filter('woocommerce_save_account_details_required_fields',[$this,'userInfoRequiredFields'],20);
        add_action('woocommerce_save_account_details_errors',[$this,'saveUserInfo'],99999999999,2);

        add_filter('woocommerce_my_account_my_orders_actions',[$this,'addFactorOrderAction'],10,2);
        add_filter('woocommerce_my_account_my_orders_query',[$this,'ordersQueryArgs']);
        add_filter('woocommerce_localisation_address_formats',[$this,'addIrAddressFormat']);

        remove_action('woocommerce_order_item_meta_start','dokan_attach_vendor_name');

        add_action('woocommerce_order_details_after_customer_details',[$this,'userPurchaseNotes'],9);
        add_action('woocommerce_order_details_after_customer_details',[$this,'orderBill']);

        remove_filter('woocommerce_get_item_data','dokan_product_seller_info');

        add_action('woocommerce_before_cart',[$this,'cartPageOpenWrapper']);
        add_action('woocommerce_after_cart',[$this,'cartPageCloseWrapper']);
    }

    public function shopQuery(WP_Query $wpQuery, WC_Query $wcQuery): void
    {
        $wcQuery->query_vars['posts_per_page'] = CodeStar::getOption('loop-shop-per-page',16);
    }

    /**
     * Adds country format for iran
     *
     * @param array $formats
     * @return array
     */
    public function addIrAddressFormat(array $formats): array
    {
        $formats['IR'] = "{country}\n{state}\n{city}\n{address_1}\n{address_2}\n{company}\n{postcode}\n{name}";

        return $formats;
    }

    public function ordersQueryArgs(array $args): array
    {
        $args['status'] = 'completed';

        return $args;
    }

    /**
     * Adds a 'Factor' action to orders that are completed and paid.
     *
     * @param array $actions Current actions.
     * @param WC_Order $order WooCommerce order object.
     * @return array Modified actions.
     */
    public function addFactorOrderAction(array $actions,WC_Order $order): array
    {
        // Check if the order is completed and paid
        if ($order->is_paid())
        {
            $actions['invoice'] = [
                'url'    => wc_get_endpoint_url('invoice',$order->get_id()),
                'name'   => Escape::htmlWithTranslation('فاکتور'),
                'svg'    => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M10.6663 4H5.33301C3.44739 4 2.50458 4 1.91879 4.58579C1.33301 5.17157 1.33301 6.11438 1.33301 8C1.33301 9.8856 1.33301 10.8284 1.91879 11.4142C2.24849 11.7439 2.69129 11.8881 3.35164 11.9511C3.33299 11.4692 3.333 10.9015 3.33301 10.3333C3.14891 10.3333 2.99967 10.1841 2.99967 10C2.99967 9.81593 3.14891 9.66667 3.33301 9.66667H12.6663C12.8504 9.66667 12.9997 9.81593 12.9997 10C12.9997 10.1841 12.8504 10.3335 12.6663 10.3335C12.6663 10.9017 12.6663 11.4693 12.6477 11.9511C13.3081 11.8881 13.7509 11.7439 14.0805 11.4142C14.6663 10.8284 14.6663 9.8856 14.6663 8C14.6663 6.11438 14.6663 5.17157 14.0805 4.58579C13.4947 4 12.5519 4 10.6663 4ZM5.99967 7.16667C6.27581 7.16667 6.49967 6.9428 6.49967 6.66667C6.49967 6.39053 6.27581 6.16667 5.99967 6.16667H3.99967C3.72353 6.16667 3.49967 6.39053 3.49967 6.66667C3.49967 6.9428 3.72353 7.16667 3.99967 7.16667H5.99967ZM11.333 7.33333C11.7012 7.33333 11.9997 7.03487 11.9997 6.66667C11.9997 6.29848 11.7012 6 11.333 6C10.9648 6 10.6663 6.29848 10.6663 6.66667C10.6663 7.03487 10.9648 7.33333 11.333 7.33333Z" fill="#0E1935"></path><path d="M11.4142 1.91916C10.8284 1.33337 9.88564 1.33337 8.00004 1.33337C6.1144 1.33337 5.17159 1.33337 4.5858 1.91916C4.25756 2.24741 4.11325 2.68775 4.0498 3.34327C4.42263 3.33335 4.83473 3.33336 5.28646 3.33337H10.7139C11.1655 3.33336 11.5775 3.33335 11.9502 3.34326C11.8868 2.68775 11.7425 2.24741 11.4142 1.91916Z" fill="#0E1935"></path><path d="M12 10.3334C12 12.219 12 13.4951 11.4142 14.0809C10.8284 14.6667 9.8856 14.6667 8 14.6667C6.11439 14.6667 5.17157 14.6667 4.58579 14.0809C4 13.4951 4 12.219 4 10.3334H12Z" fill="#0E1935"></path></svg>',
            ];
        }
        
        if (isset($actions['pay']))
            $actions['pay']['svg'] = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="16" height="16" viewBox="0 0 32 32" xml:space="preserve" fill="#0E1935" stroke="#0E1935" stroke-width="0.00032" transform="rotate(0)"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.192"></g><g id="SVGRepo_iconCarrier"> <style type="text/css"> .puchipuchi_een{fill:#0E1935;} </style> <path class="puchipuchi_een" d="M1,24c0,1.1,0.9,2,2,2h26c1.1,0,2-0.9,2-2V12H1V24z M4,19h12c0.552,0,1,0.448,1,1s-0.448,1-1,1H4 c-0.552,0-1-0.448-1-1S3.448,19,4,19z M4,22h5c0.552,0,1,0.448,1,1s-0.448,1-1,1H4c-0.552,0-1-0.448-1-1S3.448,22,4,22z M31,8v1H1V8 c0-1.1,0.9-2,2-2h26C30.1,6,31,6.9,31,8z"></path> </g></svg>';

        if (isset($actions['view']))
            $actions['view']['svg'] = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="12" viewBox="0 0 16 12" fill="none"><path d="M8 0C9.65463 0 11.2234 0.538182 12.5893 1.53455C13.9551 2.52364 15.118 3.97091 15.9532 5.78182C16.0156 5.92 16.0156 6.08 15.9532 6.21091C14.2829 9.83273 11.3093 12 8 12H7.9922C4.69073 12 1.71707 9.83273 0.0468293 6.21091C-0.0156098 6.08 -0.0156098 5.92 0.0468293 5.78182C1.71707 2.16 4.69073 0 7.9922 0H8ZM8 3.09091C6.27512 3.09091 4.87805 4.39273 4.87805 6C4.87805 7.6 6.27512 8.90182 8 8.90182C9.71707 8.90182 11.1141 7.6 11.1141 6C11.1141 4.39273 9.71707 3.09091 8 3.09091ZM8.00094 4.18022C9.07021 4.18022 9.94435 4.99476 9.94435 5.9984C9.94435 6.99476 9.07021 7.80931 8.00094 7.80931C6.92386 7.80931 6.04972 6.99476 6.04972 5.9984C6.04972 5.87476 6.06533 5.7584 6.08874 5.64204H6.12777C6.99411 5.64204 7.69655 5.00204 7.72777 4.20204C7.81362 4.18749 7.90728 4.18022 8.00094 4.18022Z" fill="#0E1935"></path></svg>';

        if (isset($actions['cancel']))
            $actions['cancel']['svg'] = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <circle cx="12" cy="12" r="9" stroke="#0E1935" stroke-width="2"></circle> <path d="M18 18L6 6" stroke="#0E1935" stroke-width="2"></path> </g></svg>';

        return $actions;
    }

    public function saveUserInfo(WP_Error $errors, stdClass $user): void
    {
        $request = Request::post();

        $data = $request->validate(
            [
                'account_first_name'   => [
                    'rules'      => 'charNum|text|min:2|max:15',
                    'dictionary' => Escape::htmlWithTranslation('نام'),
                ],
                'account_last_name'    =>[
                    'rules'      => 'charNum|text|min:2|max:30',
                    'dictionary' => Escape::htmlWithTranslation('نام خانوادگی'),
                ],
                'account_display_name' => [
                    'rules'      => 'charNum|text|min:3|max:25',
                    'dictionary' => Escape::htmlWithTranslation('نام نمایشی'),
                ],
                'account_email'        => [
                    'rules'      => 'email',
                    'dictionary' => Escape::htmlWithTranslation('ایمیل'),
                ],
                'national_id'          => [
                    'rules'      => 'charNum|number|min:10|max:10',
                    'dictionary' => Escape::htmlWithTranslation('کد ملی'),
                ],
                'gender'               => [
                    'rules'      => 'gender',
                    'dictionary' => Escape::htmlWithTranslation('جنسیت'),
                ],
                'phone_number'         => [
                    'rules'      => 'charNum|number|min:11|max:11',
                    'dictionary' => Escape::htmlWithTranslation('شماره تلفن'),
                ],
                'day'                  => [
                    'rules' => 'intVal|number|min:1|max:31',
                    'dictionary' => Escape::htmlWithTranslation('روز تاریخ تولد'),
                ],
                'month'                => [
                    'rules'      => 'intVal|number|min:1|max:12',
                    'dictionary' => Escape::htmlWithTranslation('ماه تاریخ تولد'),
                ],
                'year'                 => [
                    'rules'      => 'intVal|number|min:1300|max:'.date('Y') - 621,
                    'dictionary' => Escape::htmlWithTranslation('سال تاریخ تولد'),
                ],
                'state'                => [
                    'rules'      => 'state',
                    'dictionary' => Escape::htmlWithTranslation('استان محل سکونت'),
                ],
                'city'                 => [
                    'rules'      => 'city',
                    'dictionary' => Escape::htmlWithTranslation('شهر محل سکونت')
                ]
            ],
            $errors
        );

        if ($data)
        {
            // reformatting date values
            $data['birthdate'] = [
                'day'   => $data['day'],
                'month' => $data['month'],
                'year'  => $data['year']
            ];
            // reformatting location values
            $data['location'] = [
                'state'   => $data['state'],
                'city' => $data['city']
            ];

            // removing data that should not be saved
            unset(
                $data['day'],
                $data['month'],
                $data['year'],
                $data['state'],
                $data['city'],
                $data['account_first_name'],
                $data['account_last_name'],
                $data['account_display_name'],
                $data['account_email'],
            );

            $data = serialize($data);
            User::updateOrAddMeta($user->ID,'extra_account_info',$data);
        }

        else
        {
            /**
             * self notice and redirect handling
             * since woocommerce is sometimes unable to handle it
             * of course , at this point woocommerce function does nothing else
             * so redirecting is safe here
             */

            if ($errors->get_error_messages())
                foreach ($errors->get_error_messages() as $error)
                    wc_add_notice($error,'error');

            wp_safe_redirect(wc_get_endpoint_url(
                'edit-account',
                '',
                wc_get_page_permalink('myaccount')
            ));

            exit;
        }
    }

    public function userInfoRequiredFields(): array
    {
        $default = [
            'account_first_name'   => Escape::htmlWithTranslation('نام'),
            'account_last_name'    => Escape::htmlWithTranslation('نام خانوادگی'),
            'account_display_name' => Escape::htmlWithTranslation('نام نمایشی'),
            'account_email'        => Escape::htmlWithTranslation('ایمیل'),
            'birthdate'            => Escape::htmlWithTranslation('تاریخ تولد'),
            'national_id'  => Escape::htmlWithTranslation('کد ملی'),
            'gender'               => Escape::htmlWithTranslation('جنسیت'),
            'phone_number' => Escape::htmlWithTranslation('شماره تلفن'),
            'location'             => Escape::htmlWithTranslation('محل سکونت')
        ];

        $requiredFields = CodeStar::getOption('required-user-info');
        $fields = [];

        foreach ($requiredFields as $field)
        {
            if ($field == 'birthdate')
            {
                $fields['day'] = Escape::htmlWithTranslation('روز تولد');
                $fields['month'] = Escape::htmlWithTranslation('ماه تولد');
                $fields['year'] = Escape::htmlWithTranslation('سال تولد');
            }

            else if ($field == 'location')
            {
                $fields['state'] = Escape::htmlWithTranslation('استان محل سکونت');
                $fields['city'] = Escape::htmlWithTranslation('شهر محل سکونت');
            }

            else
            {
                $fields[$field] = $default[$field];
            }
        }

        return $fields;
    }

    public function editUserInfoFields(): void
    {
        $userData = User::getMeta(-1,'extra_account_info');
        ?>
        <div class="edit-account-info-field space-x-3.5 space-x-reverse woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide user-info-birthday">
            <div class="flex items-center justify-center relative flex-1">
                <select class="user-info-day w-full" id="day" name="day">
                    <option></option>
                    <?php
                    for ($i = 1;$i < 32;$i++)
                    {
                        ?>
                        <option <?php echo !empty($userData) && $userData['birthdate']['day'] == $i ? 'selected' : '' ?>
                                value="<?php echo $i ?>">
                            <?php echo $i ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>
            </div>
            <div class="flex items-center justify-center relative flex-1">
                <select class="user-info-month" id="month" name="month">
                    <option></option>
                    <option <?php echo !empty($userData) && $userData['birthdate']['month'] == 1 ? 'selected' : '' ?> value="1">
                        فروردین
                    </option>
                    <option <?php echo !empty($userData) && $userData['birthdate']['month'] == 2 ? 'selected' : '' ?> value="2">
                        اردیبهشت
                    </option>
                    <option <?php echo !empty($userData) && $userData['birthdate']['month'] == 3 ? 'selected' : '' ?> value="3">
                        خرداد
                    </option>
                    <option <?php echo !empty($userData) && $userData['birthdate']['month'] == 4 ? 'selected' : '' ?> value="4">
                        تیر
                    </option>
                    <option <?php echo !empty($userData) && $userData['birthdate']['month'] == 5 ? 'selected' : '' ?> value="5">
                        مرداد
                    </option>
                    <option <?php echo !empty($userData) && $userData['birthdate']['month'] == 6 ? 'selected' : '' ?> value="6">
                        شهریور
                    </option>
                    <option <?php echo !empty($userData) && $userData['birthdate']['month'] == 7 ? 'selected' : '' ?> value="7">
                        مهر
                    </option>
                    <option <?php echo !empty($userData) && $userData['birthdate']['month'] == 8 ? 'selected' : '' ?> value="8">
                        آبان
                    </option>
                    <option <?php echo !empty($userData) && $userData['birthdate']['month'] == 9 ? 'selected' : '' ?> value="9">
                        آذر
                    </option>
                    <option <?php echo !empty($userData) && $userData['birthdate']['month'] == 10 ? 'selected' : '' ?> value="10">
                        دی
                    </option>
                    <option <?php echo !empty($userData) && $userData['birthdate']['month'] == 11 ? 'selected' : '' ?> value="11">
                        بهمن
                    </option>
                    <option <?php echo !empty($userData) && $userData['birthdate']['month'] == 12 ? 'selected' : '' ?> value="12">
                        اسفند
                    </option>
                </select>
            </div>
            <div class="flex items-center justify-center relative flex-1">
                <select class="user-info-year" id="year" name="year">
                    <option></option>
                    <?php
                    for ($i = ((int) date('Y')) - 621;$i >= 1300;$i--)
                    {
                        ?>
                        <option <?php echo !empty($userData) && $userData['birthdate']['year'] == $i ? 'selected' : '' ?>
                                value="<?php echo $i ?>">
                            <?php echo $i ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>
            </div>
        </div>

        <p class="edit-account-info-field <?php echo !empty($userData) && $userData['national_id'] ? 'has-value' : '' ?> woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="account_national_id">
                <?php echo Escape::htmlWithTranslation('کدملی') ?>
                <span class="required">*</span>
            </label>
            <input type="text"
                   class="woocommerce-Input woocommerce-Input--text input-text"
                   name="national_id"
                   id="account_national_id"
                   value="<?php echo !empty($userData) ? $userData['national_id'] : '' ?>" />
        </p>

        <div class="edit-account-info-field flex items-center justify-between woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <span class="flex items-center justify-center">
                <?php echo Escape::htmlWithTranslation('جنسیت') ?>
                <span class="required">*</span>
            </span>
            <div class="flex items-center gender-selection">
                <label for="account_gender_male">
                    <input type="radio"
                           class="woocommerce-Input"
                           name="gender"
                           id="account_gender_male"
                           value="male"
                           <?php echo !empty($userData) && $userData['gender'] == 'male' ? 'checked' : '' ?>
                    />
                    <span></span>
                    <?php echo Escape::htmlWithTranslation('مرد') ?>
                </label>
                <label for="account_gender_female">
                    <input type="radio"
                           class="woocommerce-Input"
                           name="gender"
                           id="account_gender_female"
                           value="female"
                           <?php echo !empty($userData) && $userData['gender'] == 'female' ? 'checked' : '' ?>
                    />
                    <span></span>
                    <?php echo Escape::htmlWithTranslation('زن') ?>
                </label>
            </div>
        </div>

        <p class="edit-account-info-field <?php echo !empty($userData) && $userData['phone_number'] ? 'has-value' : '' ?> woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="account_phone_number">
                <?php echo Escape::htmlWithTranslation('شماره همراه') ?>
                <span class="required">*</span>
            </label>
            <input type="text"
                   class="woocommerce-Input woocommerce-Input--text input-text"
                   name="phone_number"
                   id="account_phone_number"
                   value="<?php echo !empty($userData) ? $userData['phone_number'] : '' ?>"
            />
        </p>

        <div class="edit-account-info-field <?php echo !empty($userData) && $userData['location']['state'] ? 'has-value' : '' ?> selector flex items-center justify-center relative">
            <select class="user-info-state" id="state" name="state">
                <option></option>
                <?php
                $states = Storage::getJsonContent(Constants::Storage.'/json/states.json');

                if ($states)
                {
                    foreach ($states as $stateCode => $stateName)
                    {
                        ?>
                        <option <?php echo !empty($userData) && $userData['location']['state'] == $stateCode ? 'selected' : '' ?>
                                value="<?php echo $stateCode ?>">
                            <?php echo Escape::htmlWithTranslation($stateName) ?>
                        </option>
                        <?php
                    }
                }
                ?>
            </select>
            <label for="state">
                <?php echo Escape::htmlWithTranslation('استان') ?>
            </label>
        </div>

        <div class="edit-account-info-field <?php echo !empty($userData) && $userData['location']['city'] ? 'has-value' : '' ?> selector flex items-center justify-center relative">
            <select class="user-info-city" id="city" name="city">
                <option></option>
                <?php
                if (!empty($userData) && !empty($userData['location']['state']) && !empty($userData['location']['city']))
                {
                    $cities = Storage::getJsonDataWhere(Constants::Storage.'/json/cities.json',$userData['location']['state']);

                    foreach ($cities as $cityCode => $cityName)
                    {
                        ?>
                        <option <?php echo $userData['location']['city'] == $cityCode ? 'selected' : '' ?>
                                value="<?php echo $cityCode ?>">
                            <?php echo $cityName ?>
                        </option>
                        <?php
                    }
                }
                ?>
            </select>
            <label for="city">
                <?php echo Escape::htmlWithTranslation('شهر') ?>
            </label>
        </div>
        <?php
    }

    public function privacyPolicy(): void
    {
        $privacy_page_id = wc_privacy_policy_page_id();
        $terms_page_id   = wc_terms_and_conditions_page_id();
        ?>
        <div class="flex items-center space-x-2.5 space-x-reverse">
            <?php
            if ($privacy_page_id)
            {
                ?>
                <input type="checkbox" name="privacy-policy" id="privacy-policy">
                <label for="privacy-policy"></label>
                <span class="text-[15px] font-medium text-[var(--gray)]">
                    <a class="text-[var(--cyan)] text-[15px] font-bold"
                       href="<?php echo get_permalink($privacy_page_id) ?>">حریم خصوصی</a>
                    <?php
                    echo Escape::htmlWithTranslation('کاربران سایت').' ';
                    echo Escape::htmlWithTranslation(CodeStar::getOption('site-name'));
                    echo Escape::htmlWithTranslation('را مطالعه نموده و با کلیه موارد آن موافقم .');
                    ?>
                </span>
                <?php
            }

            else if ($terms_page_id)
            {
                ?>
                <input type="checkbox" name="terms" id="terms">
                <label for="terms"></label>
                <span class="text-[15px] font-medium text-[var(--gray)]">
                    <a class="text-[var(--cyan)] text-[15px] font-bold"
                       href="<?php echo get_permalink($terms_page_id) ?>">شرایط و قوانین</a>
                    <?php
                    echo Escape::htmlWithTranslation('استفاده از سایت');
                    echo Escape::htmlWithTranslation(CodeStar::getOption('site-name'));
                    echo Escape::htmlWithTranslation('را مطالعه نموده و با کلیه موارد آن موافقم.');
                    ?>
                </span>
                <?php
            }
            ?>
        </div>
        <?php
    }

    /**
     * Modifies user panel navigation sidebar items
     *
     * @param array $items
     * @return array
     */
    public static function navigationItems(array $items): array
    {
        unset($items['customer-logout']); // repositioning

        $items['dashboard'] = '<span class="flex items-center ml-[19px] justify-center"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.78325 2.1665H6.59991C7.77491 2.1665 8.71658 3.12484 8.71658 4.30067V7.1415C8.71658 8.32484 7.77491 9.27484 6.59991 9.27484H3.78325C2.61658 9.27484 1.66658 8.32484 1.66658 7.1415V4.30067C1.66658 3.12484 2.61658 2.1665 3.78325 2.1665ZM3.78325 11.7246H6.59991C7.77491 11.7246 8.71658 12.6754 8.71658 13.8588V16.6996C8.71658 17.8746 7.77491 18.8329 6.59991 18.8329H3.78325C2.61658 18.8329 1.66658 17.8746 1.66658 16.6996V13.8588C1.66658 12.6754 2.61658 11.7246 3.78325 11.7246ZM16.2167 2.1665H13.4C12.225 2.1665 11.2833 3.12484 11.2833 4.30067V7.1415C11.2833 8.32484 12.225 9.27484 13.4 9.27484H16.2167C17.3833 9.27484 18.3333 8.32484 18.3333 7.1415V4.30067C18.3333 3.12484 17.3833 2.1665 16.2167 2.1665ZM13.4 11.7246H16.2167C17.3833 11.7246 18.3333 12.6754 18.3333 13.8588V16.6996C18.3333 17.8746 17.3833 18.8329 16.2167 18.8329H13.4C12.225 18.8329 11.2833 17.8746 11.2833 16.6996V13.8588C11.2833 12.6754 12.225 11.7246 13.4 11.7246Z" fill="#BCC1C8"></path></svg></span>'.Escape::htmlWithTranslation('پیش خوان');
        $items['orders'] = '<span class="flex items-center ml-[19px] justify-center"><svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none"><path d="M5.155 16.0625C5.94288 16.0625 6.58838 16.7217 6.58838 17.5361C6.58838 18.3408 5.94288 19 5.155 19C4.35762 19 3.71212 18.3408 3.71212 17.5361C3.71212 16.7217 4.35762 16.0625 5.155 16.0625ZM15.8342 16.0625C16.6221 16.0625 17.2676 16.7217 17.2676 17.5361C17.2676 18.3408 16.6221 19 15.8342 19C15.0368 19 14.3913 18.3408 14.3913 17.5361C14.3913 16.7217 15.0368 16.0625 15.8342 16.0625ZM0.739315 0.000113515L0.836053 0.00830139L3.10004 0.356346C3.42279 0.415484 3.6601 0.68597 3.68858 1.01559L3.86894 3.18724C3.89742 3.49844 4.14423 3.73112 4.44799 3.73112H17.2677C17.8468 3.73112 18.2265 3.93471 18.6062 4.38067C18.9859 4.82663 19.0524 5.46649 18.9669 6.04721L18.0651 12.407C17.8943 13.6295 16.8691 14.5302 15.6635 14.5302H5.30707C4.04455 14.5302 3.00037 13.5423 2.89595 12.2626L2.02263 1.69423L0.589246 1.44217C0.209542 1.3743 -0.0562511 0.996205 0.0101971 0.608412C0.0766453 0.211893 0.446857 -0.0508371 0.836053 0.00830139L0.739315 0.000113515ZM14.1447 7.31724H11.5152C11.1165 7.31724 10.8033 7.63717 10.8033 8.04435C10.8033 8.44184 11.1165 8.77146 11.5152 8.77146H14.1447C14.5434 8.77146 14.8566 8.44184 14.8566 8.04435C14.8566 7.63717 14.5434 7.31724 14.1447 7.31724Z" fill="#BCC1C8"></path></svg></span>'.Escape::htmlWithTranslation('سفارش ‌ها');
        $items['downloads'] = '<span class="flex items-center ml-[19px] justify-center"><svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none"><path d="M15.26 22.25H8.73998C3.82998 22.25 1.72998 20.15 1.72998 15.24V15.11C1.72998 10.67 3.47998 8.53 7.39998 8.16C7.79998 8.13 8.17998 8.43 8.21998 8.84C8.25998 9.25 7.95998 9.62 7.53998 9.66C4.39998 9.95 3.22998 11.43 3.22998 15.12V15.25C3.22998 19.32 4.66998 20.76 8.73998 20.76H15.26C19.33 20.76 20.77 19.32 20.77 15.25V15.12C20.77 11.41 19.58 9.93 16.38 9.66C15.97 9.62 15.66 9.26 15.7 8.85C15.74 8.44 16.09 8.13 16.51 8.17C20.49 8.51 22.27 10.66 22.27 15.13V15.26C22.27 20.15 20.17 22.25 15.26 22.25Z" fill="#BCC1C8"/><path d="M12 15.63C11.59 15.63 11.25 15.29 11.25 14.88V2C11.25 1.59 11.59 1.25 12 1.25C12.41 1.25 12.75 1.59 12.75 2V14.88C12.75 15.3 12.41 15.63 12 15.63Z" fill="#BCC1C8"/><path d="M12.0001 16.75C11.8101 16.75 11.6201 16.68 11.4701 16.53L8.12009 13.18C7.83009 12.89 7.83009 12.41 8.12009 12.12C8.41009 11.83 8.89009 11.83 9.18009 12.12L12.0001 14.94L14.8201 12.12C15.1101 11.83 15.5901 11.83 15.8801 12.12C16.1701 12.41 16.1701 12.89 15.8801 13.18L12.5301 16.53C12.3801 16.68 12.1901 16.75 12.0001 16.75Z" fill="#BCC1C8"/></svg></span>'.Escape::htmlWithTranslation('دانلودها');
        $items['edit-address'] = '<span class="flex ml-[19px] items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="22" viewBox="0 0 15 22" fill="none"><path d="M13.9581 3.73557C12.6342 1.45088 10.2827 0.0550679 7.66782 0.00172553C7.55619 -0.000575178 7.44384 -0.000575178 7.33217 0.00172553C4.71728 0.0550679 2.36579 1.45088 1.04186 3.73557C-0.311388 6.07087 -0.348412 8.87603 0.942798 11.2394L6.35215 21.1404C6.35458 21.1448 6.35701 21.1492 6.35952 21.1536C6.59752 21.5672 7.02388 21.8142 7.50008 21.8142C7.97624 21.8142 8.4026 21.5672 8.64055 21.1536C8.64306 21.1492 8.64549 21.1448 8.64792 21.1404L14.0573 11.2394C15.3484 8.87603 15.3114 6.07087 13.9581 3.73557ZM7.49999 9.88456C5.8085 9.88456 4.43238 8.50843 4.43238 6.81694C4.43238 5.12545 5.8085 3.74933 7.49999 3.74933C9.19149 3.74933 10.5676 5.12545 10.5676 6.81694C10.5676 8.50843 9.19153 9.88456 7.49999 9.88456Z" fill="#BCC1C8"></path></svg></span>'.Escape::htmlWithTranslation('آدرس');
        $items['edit-account'] = '<span class="flex items-center ml-[19px] justify-center"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="19" viewBox="0 0 15 19" fill="none"><path d="M7.5 12.4756C11.5675 12.4756 15 13.1365 15 15.6865C15 18.2375 11.545 18.875 7.5 18.875C3.43348 18.875 0 18.2141 0 15.664C0 13.1131 3.45505 12.4756 7.5 12.4756ZM7.5 0.125C10.2554 0.125 12.4631 2.3319 12.4631 5.08536C12.4631 7.83883 10.2554 10.0467 7.5 10.0467C4.74553 10.0467 2.53689 7.83883 2.53689 5.08536C2.53689 2.3319 4.74553 0.125 7.5 0.125Z" fill="#BCC1C8"></path></svg></span>'.Escape::htmlWithTranslation('جزئیات حساب');
        $items['change-password'] = '<span class="flex items-center ml-[19px] justify-center"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"><g fill="#AAA" fill-rule="evenodd"><path fill-rule="nonzero" d="M12.238 4.717h-.022V3.712c0-1.748-1.43-3.234-3.175-3.204A3.13 3.13 0 0 0 5.969 3.63v.096c0 .144.118.262.263.262h.839a.263.263 0 0 0 .262-.262v-.03c0-.938.706-1.763 1.645-1.822a1.759 1.759 0 0 1 1.87 1.752v1.087H8.261v.003H5.869a.825.825 0 0 0-.802.821v4.546c0 .455.37.824.825.824h6.342c.455 0 .824-.37.824-.824V5.542a.82.82 0 0 0-.82-.825zM9.554 8.018c-.062.044-.08.092-.08.166.003.333.003.666 0 1.002a.35.35 0 0 1-.19.336.39.39 0 0 1-.583-.336v-.004-1.001c0-.067-.015-.115-.074-.16a.76.76 0 0 1-.252-.938c.148-.325.507-.517.843-.447.377.074.64.38.643.754a.704.704 0 0 1-.307.628z"></path><path d="M1.5 12.2h15a1.5 1.5 0 0 1 1.5 1.5v2a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 0 15.7v-2a1.5 1.5 0 0 1 1.5-1.5zm2.457 1.732a.258.258 0 0 0-.351-.093l-.643.374v-.743a.256.256 0 0 0-.51 0v.743l-.644-.374a.258.258 0 0 0-.35.093c-.07.122-.03.28.092.351l.647.377-.647.377a.258.258 0 0 0-.093.351c.07.122.23.163.351.093l.644-.374v.743a.256.256 0 0 0 .51 0v-.743l.643.374c.122.07.28.03.35-.093a.258.258 0 0 0-.092-.351l-.646-.377.646-.377a.256.256 0 0 0 .093-.351zm1.857 1.452c.07.122.23.163.351.093l.644-.373v.742a.256.256 0 0 0 .51 0v-.742l.643.373c.122.07.28.03.35-.093a.258.258 0 0 0-.091-.35l-.647-.378.647-.377a.258.258 0 0 0 .092-.35.258.258 0 0 0-.351-.093l-.643.373v-.743a.256.256 0 0 0-.51 0v.743l-.644-.37a.258.258 0 0 0-.35.093c-.07.122-.03.28.092.351l.647.377-.647.373a.258.258 0 0 0-.093.351zm4.361 0c.07.122.23.163.351.093l.644-.373v.742a.256.256 0 0 0 .51 0v-.742l.643.373c.122.07.28.03.35-.093a.258.258 0 0 0-.091-.35l-.647-.378.647-.377a.258.258 0 0 0 .092-.35.258.258 0 0 0-.351-.093l-.643.373v-.743a.256.256 0 0 0-.51 0v.743l-.644-.373a.258.258 0 0 0-.35.092c-.07.122-.03.281.092.351l.647.377-.647.377a.258.258 0 0 0-.093.351zm6.182.116h-2.214c-.078 0-.143.053-.143.116v.268c0 .063.065.116.143.116h2.214c.078 0 .143-.053.143-.116v-.268c0-.063-.065-.116-.143-.116z"></path></g></svg></span>'.Escape::htmlWithTranslation('تغییر رمزعبور');
        $items['bookmarks'] = '<span class="flex items-center justify-center ml-[19px]"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="19" viewBox="0 0 14 19" fill="none"><path d="M9.68625 0.749512C12.0575 0.749512 13.9738 1.68576 14 4.06576V17.3483C14 17.497 13.965 17.6458 13.895 17.777C13.7812 17.987 13.5887 18.1445 13.3525 18.2145C13.125 18.2845 12.8713 18.2495 12.6613 18.127L6.99125 15.292L1.3125 18.127C1.18212 18.1961 1.0325 18.2408 0.88375 18.2408C0.39375 18.2408 0 17.8383 0 17.3483V4.06576C0 1.68576 1.925 0.749512 4.2875 0.749512H9.68625Z" fill="#BCC1C8"></path></svg></span>'.Escape::htmlWithTranslation('محصولات مورد علاقه');
        $items['recent-visits'] = '<span class="flex items-center justify-center ml-[19px]"><svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none"><path class="path-stroke" d="M15.0007 12C15.0007 13.6569 13.6576 15 12.0007 15C10.3439 15 9.00073 13.6569 9.00073 12C9.00073 10.3431 10.3439 9 12.0007 9C13.6576 9 15.0007 10.3431 15.0007 12Z" stroke="#BCC1C8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path class="path-stroke" d="M12.0012 5C7.52354 5 3.73326 7.94288 2.45898 12C3.73324 16.0571 7.52354 19 12.0012 19C16.4788 19 20.2691 16.0571 21.5434 12C20.2691 7.94291 16.4788 5 12.0012 5Z" stroke="#BCC1C8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>'.Escape::htmlWithTranslation('بازدید های اخیر');
        $items['comments'] = '<span class="flex items-center justify-center ml-[19px]"><svg xmlns="http://www.w3.org/2000/svg" height="19" width="19" version="1.1" id="_x32_" viewBox="0 0 512 512" xml:space="preserve"><g><path class="st0" d="M92.574,294.24V124.336H43.277C19.449,124.336,0,144.213,0,168.467v206.44   c0,24.254,19.449,44.133,43.277,44.133h62v45.469c0,3.041,1.824,5.777,4.559,6.932c2.736,1.154,5.957,0.486,8.023-1.641   l49.844-50.76h106.494c23.828,0,43.279-19.879,43.279-44.133v-0.061H172.262C128.314,374.846,92.574,338.676,92.574,294.24z"/><path class="st0" d="M462.717,40H172.26c-27.105,0-49.283,22.59-49.283,50.197v204.037c0,27.61,22.178,50.199,49.283,50.199   h164.668l75.348,76.033c2.399,2.442,6.004,3.172,9.135,1.852c3.133-1.322,5.176-4.434,5.176-7.887v-69.998h36.131   c27.106,0,49.283-22.59,49.283-50.199V90.197C512,62.59,489.822,40,462.717,40z M369.156,280.115H195.92v-24.316h173.236V280.115z    M439.058,204.129H195.92v-24.314h243.138V204.129z M439.058,128.143H195.92v-24.315h243.138V128.143z"/></g></svg></span>'.Escape::htmlWithTranslation('نظرات و پرسش ها');
        $items['customer-logout'] = '<span class="flex items-center justify-center ml-[19px]"><svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none"><path d="M7.97016 0.5C9.95621 0.5 11.576 2.092 11.576 4.052V7.884H6.69226C6.34226 7.884 6.06551 8.156 6.06551 8.5C6.06551 8.836 6.34226 9.116 6.69226 9.116H11.576V12.94C11.576 14.9 9.95621 16.5 7.95388 16.5H3.98993C1.99574 16.5 0.375977 14.908 0.375977 12.948V4.06C0.375977 2.092 2.00388 0.5 3.99807 0.5H7.97016ZM13.6081 5.74016C13.8481 5.49216 14.2401 5.49216 14.4801 5.73216L16.8161 8.06016C16.9361 8.18016 17.0001 8.33216 17.0001 8.50016C17.0001 8.66016 16.9361 8.82016 16.8161 8.93216L14.4801 11.2602C14.3601 11.3802 14.2001 11.4442 14.0481 11.4442C13.8881 11.4442 13.7281 11.3802 13.6081 11.2602C13.3681 11.0202 13.3681 10.6282 13.6081 10.3882L14.8881 9.11616H11.5761V7.88416H14.8881L13.6081 6.61216C13.3681 6.37216 13.3681 5.98016 13.6081 5.74016Z" fill="#CAB9B9"></path></svg></span>'.Escape::htmlWithTranslation('خروج');

        // Check if payment gateways support add new payment methods.
        if (isset($items['payment-methods']))
        {
            $items['payment-methods'] = '<span class="flex ml-[19px] items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="19" viewBox="0 0 18 19" fill="none"><path d="M11.25 0.730769C11.25 0.327205 11.5858 0 12 0C12.4142 0 12.75 0.327205 12.75 0.730769V3.65385C12.75 4.05741 12.4142 4.38462 12 4.38462C11.5858 4.38462 11.25 4.05741 11.25 3.65385V0.730769Z" fill="#BCC1C8"></path><path d="M10.5 1.48669C10.0286 1.46939 9.52917 1.46154 9 1.46154C8.47083 1.46154 7.97136 1.46939 7.5 1.48669V3.65385C7.5 4.46106 6.82841 5.11538 6 5.11538C5.17159 5.11538 4.5 4.46106 4.5 3.65385V1.80908C1.86923 2.36965 0.680603 3.70648 0.231033 6.50449C0.162323 6.93214 0.511826 7.30769 0.956039 7.30769H17.044C17.4882 7.30769 17.8377 6.93214 17.769 6.50449C17.3194 3.70648 16.1308 2.36965 13.5 1.80908V3.65385C13.5 4.46106 12.8284 5.11538 12 5.11538C11.1716 5.11538 10.5 4.46106 10.5 3.65385V1.48669Z" fill="#BCC1C8"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M9 19C1.58849 19 0 17.4522 0 10.2308C0 9.97083 0.00205994 9.7182 0.00636292 9.4727C0.0132751 9.07993 0.344925 8.76923 0.748123 8.76923H17.2519C17.6551 8.76923 17.9867 9.07993 17.9936 9.4727L17.9957 9.60669L17.9973 9.72605C17.9987 9.85316 17.9995 9.98207 17.9999 10.1128L18 10.2308C18 11.3313 17.9631 12.3001 17.8743 13.1517C17.82 13.1502 17.7566 13.1482 17.6853 13.1458C17.2479 13.1316 16.5169 13.1078 15.8089 13.1589C15.3297 13.1934 14.8134 13.2633 14.3325 13.4029C13.8573 13.5409 13.3636 13.7622 12.9832 14.134C12.2622 14.8387 12.0595 15.8879 11.9958 16.7404C11.9504 17.3469 11.9718 18.0027 11.9879 18.4957C11.9925 18.6383 11.9967 18.7672 11.9987 18.8775C11.1245 18.964 10.1299 19 9 19ZM3.75 11.6923C3.75 11.2887 4.08577 10.9615 4.5 10.9615H6.75C7.16423 10.9615 7.5 11.2887 7.5 11.6923C7.5 12.0959 7.16423 12.4231 6.75 12.4231H4.5C4.08577 12.4231 3.75 12.0959 3.75 11.6923ZM4.5 13.8846C4.08577 13.8846 3.75 14.2118 3.75 14.6154C3.75 15.0189 4.08577 15.3462 4.5 15.3462H6.75C7.16423 15.3462 7.5 15.0189 7.5 14.6154C7.5 14.2118 7.16423 13.8846 6.75 13.8846H4.5Z" fill="#BCC1C8"></path><path d="M13.4947 18.6535C15.9161 18.1389 17.1172 16.9681 17.6449 14.608C17.6088 14.6069 17.5718 14.6057 17.5338 14.6045C17.0969 14.5902 16.5364 14.5719 15.9197 14.6164C15.5007 14.6466 15.101 14.7048 14.7609 14.8036C14.4151 14.904 14.183 15.0315 14.0455 15.1658C13.7308 15.4734 13.5513 16.0511 13.4918 16.8466C13.4521 17.3774 13.469 17.8743 13.4846 18.3354C13.4883 18.4434 13.4919 18.5495 13.4947 18.6535Z" fill="#BCC1C8"></path><path d="M5.25 0.730769C5.25 0.327205 5.58577 0 6 0C6.41423 0 6.75 0.327205 6.75 0.730769V3.65385C6.75 4.05741 6.41423 4.38462 6 4.38462C5.58577 4.38462 5.25 4.05741 5.25 3.65385V0.730769Z" fill="#BCC1C8"></path></svg></span>'.Escape::htmlWithTranslation('روش های پرداخت');
        }

        return $items;
    }

    public function userInformation(): void
    {
        $user = wp_get_current_user();

        $nationalId = get_user_meta($user->ID,'national_id',true);
        $phoneNumber = get_user_meta($user->ID,'phone_number',true);
        ?>
        <div class="dashboard-user-info py-5 px-[15px] border-[2px] border-transparent border-solid bg-white relative rounded-[25px]">
            <div class="flex w-full items-center justify-between pb-5 mb-5 border-b border-solid border-[#dfe1e8] flex-wrap">
                <span class="text-darkblue text-[18px] font-bold">
                    <?php echo \engine\security\Escape::htmlWithTranslation('اطلاعات حساب کاربری') ?>
                </span>
                <a href="<?php echo wc_get_endpoint_url('edit-account') ?>"
                   class="flex cursor-pointer items-center space-x-[3px] space-x-reverse">
                    <span class="flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="15" viewBox="0 0 13 15" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.6813 0.983166C10.7192 1.01199 11.9851 1.97294 11.9851 1.97294C12.4451 2.24053 12.8045 2.71879 12.9407 3.285C13.0762 3.84531 12.9778 4.42336 12.6623 4.91196C12.6602 4.91522 12.6581 4.91844 12.6508 4.92802L12.6451 4.93542C12.5939 5.00136 12.3712 5.27638 11.2475 6.65171C11.2371 6.66974 11.2253 6.68662 11.2124 6.70302C11.1934 6.7272 11.1728 6.74943 11.1508 6.76967C11.0741 6.86394 10.9932 6.963 10.9081 7.06714L10.7356 7.27816C10.3797 7.71352 9.95764 8.22978 9.45688 8.8421L9.19989 9.15635C8.23311 10.3384 6.99497 11.8519 5.40887 13.7904C5.06156 14.2132 4.54097 14.4593 3.98179 14.466L1.22828 14.5H1.22071C0.958149 14.5 0.729636 14.3241 0.668345 14.0735L0.0486343 11.5078C-0.0792426 10.9763 0.0478777 10.4278 0.396702 10.002L7.52451 1.29289C7.52754 1.28993 7.52981 1.28623 7.53284 1.28328C8.31448 0.370378 9.72793 0.235845 10.6813 0.983166ZM6.72981 4.0385L1.28276 10.6946C1.15337 10.8528 1.1057 11.0569 1.15337 11.2527L1.66866 13.3853L3.96817 13.3572C4.18685 13.355 4.38888 13.2596 4.52281 13.097C5.2125 12.254 6.07932 11.1945 6.97052 10.1051L7.28583 9.7196L7.60167 9.33348C8.43747 8.31168 9.26318 7.30208 9.95406 6.45703L6.72981 4.0385ZM8.40679 1.99068L7.4388 3.17217L10.6628 5.59001C11.2833 4.83069 11.6916 4.33059 11.7293 4.28291C11.8534 4.08629 11.9018 3.80835 11.8368 3.54076C11.7702 3.26653 11.5954 3.03368 11.3434 2.8851C11.2897 2.84888 10.0147 1.88202 9.97536 1.85171C9.49563 1.47621 8.79572 1.54125 8.40679 1.99068Z" fill="#179299"></path>
                        </svg>
                    </span>
                    <span class="text-[15px] font-bold text-cyan">
                        <?php echo \engine\security\Escape::htmlWithTranslation('اطلاعات حساب کاربری') ?>
                    </span>
                </a>
            </div>
            <div class="flex items-stretch justify-between flex-wrap">
                <div class="user-info relative flex flex-col items-center justify-center">
                    <span class="text-[#8a929c] text-[15px] font-medium">
                        <?php echo \engine\security\Escape::htmlWithTranslation('نام و نام خانوادگی:') ?>
                    </span>
                    <span class="font-bold text-darkblue text-[15px]">
                        <?php echo $user->first_name && $user->last_name ? $user->first_name.' '.$user->last_name : '-' ?>
                    </span>
                </div>
                <div class="user-info flex relative flex-col items-center justify-center">
                    <span class="text-[#8a929c] text-[15px] font-medium">
                        <?php echo \engine\security\Escape::htmlWithTranslation('کد ملی:') ?>
                    </span>
                    <span class="font-bold text-darkblue text-[15px]">
                        <?php echo $nationalId ? $nationalId : '-' ?>
                    </span>
                </div>
                <div class="user-info flex flex-col relative items-center justify-center">
                    <span class="text-[#8a929c] text-[15px] font-medium">
                        <?php echo \engine\security\Escape::htmlWithTranslation('ایمیل:') ?>
                    </span>
                    <span class="font-bold text-darkblue text-[15px]">
                        <?php echo $user->user_email ? $user->user_email : '-' ?>
                    </span>
                </div>
                <div class="user-info flex flex-col items-center relative justify-center">
                    <span class="text-[#8a929c] text-[15px] font-medium">
                        <?php echo \engine\security\Escape::htmlWithTranslation('شماره همراه:') ?>
                    </span>
                    <span class="font-bold text-darkblue text-[15px]">
                        <?php echo $phoneNumber ? $phoneNumber : '-' ?>
                    </span>
                </div>
            </div>
        </div>
        <?php
    }

    public function dashboardPanels(): void
    {
        ?>
        <div class="dashboard-user-orders mb-[30px] py-5 px-[15px] bg-white relative rounded-[25px] flex flex-col w-full">
            <div class="dashboard-user-orders-sorting overflow-y-hidden overflow-x-auto flex items-center justify-start w-full p-2.5 rounded-[15px] h-[60px]">
                <span class="sorting-option active" data-panel=".dashboard-user-orders">
                    <?php echo Escape::htmlWithTranslation('آخرین سفارش ها') ?>
                </span>
                <span class="sorting-option" data-panel=".dashboard-user-favorites-panel">
                    <?php echo Escape::htmlWithTranslation('آخرین محصولات ذخیره شده') ?>
                </span>
            </div>
            <?php
            $this->order();
            $this->bookmarks();
            ?>
        </div>
        <?php
    }

    private function order(): void
    {
        $args = [
            'limit' => 6,
            'customer_id' => get_current_user_id(),
            'post_type' => 'shop_order',
        ];

        $userOrders = wc_get_orders($args);
        ?>
        <div class="dashboard-panel dashboard-user-orders w-full overflow-auto">
            <div class="dashboard-user-orders-table w-fit flex flex-col">
                <div class="dashboard-user-orders-table-heading flex items-center justify-start">
                    <div class="w-[100px] text-[14px]">
                        <?php echo Escape::htmlWithTranslation('شماره سفارش') ?>
                    </div>
                    <div class="w-[215px] text-[14px]">
                        <?php echo Escape::htmlWithTranslation('محصول') ?>
                    </div>
                    <div class="w-[170px] text-[14px]">
                        <?php echo Escape::htmlWithTranslation('تاریخ') ?>
                    </div>
                    <div class="w-[160px] text-[14px]">
                        <?php echo Escape::htmlWithTranslation('قیمت کل') ?>
                    </div>
                    <div class="w-[118px] text-[14px]">
                        <?php echo Escape::htmlWithTranslation('وضعیت') ?>
                    </div>
                </div>
                <?php
                if (!empty($userOrders))
                {
                    ?>
                    <div class="flex flex-col w-full">
                        <?php
                        foreach ($userOrders as $order)
                        {
                            $status = $order->get_status();
                            ?>
                            <div class="dashboard-user-orders-table-content flex border-t justify-start border-solid border-[#eef1f4] items-center py-[15px] px-5 font-bold text-darkblue">
                                <div class="w-[100px] text-[14px]">
                                    <?php echo $order->get_order_number() ?>
                                </div>
                                <div class="w-[215px] text-[14px] flex space-x-[5px] space-x-reverse items-center justify-start px-[5px]">
                                    <?php
                                    foreach ($order->get_items() as $item)
                                    {
                                        $data = $item->get_data();

                                        $variationID = $data['variation_id'];
                                        $quantity = $item->get_quantity();
                                        $product = wc_get_product($data['product_id']);

                                        if ($variationID != 0)
                                        {
                                            $img = get_the_post_thumbnail_url($variationID,[50,50]);

                                            if (!$img)
                                                $img = get_the_post_thumbnail_url($product->get_id(),[50,50]);
                                        }

                                        else
                                            $img = get_the_post_thumbnail_url($product->get_id(),[50,50]);
                                        ?>
                                        <a href="<?php echo $product->get_permalink() ?>"
                                           class="flex tooltip-hover items-center justify-center relative w-[50px] h-[50px] p-[3px] rounded-[15px] bg-white shadow-[2px_-2px_30px_rgb(237,240,245)]">
                                            <img src="<?php echo $img ?>">
                                            <span class="tooltip">
                                                <?php echo $quantity.' '.Escape::htmlWithTranslation('عدد') ?>
                                            </span>
                                        </a>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <div class="w-[170px] ltr text-[15px] font-bold">
                                    <?php echo date('Y/m/d-H:i:s',strtotime($order->get_date_created())) ?>
                                </div>
                                <div class="w-[160px] text-[15px] font-bold">
                                    <?php echo number_format((float)$order->get_data()['total'],0,',',',').' '.get_woocommerce_currency_symbol() ?>
                                </div>
                                <div class="w-[118px] relative <?php echo $status ?> h-[36px] overflow-hidden rounded-[10px] py-0.5 px-1 flex items-center justify-center">
                                    <span class="text-[14px] font-medium">
                                        <?php echo wc_get_order_status_name( $order->get_status() ) ?>
                                    </span>
                                </div>
                                <div class="w-[70px] flex items-center justify-end">
                                    <a href="<?php echo $order->get_view_order_url() ?>"
                                       class="relative order-url flex items-center justify-center tooltip-hover w-9 h-9 inline-block rounded-[10px] bg-white shadow-[0_2px_8px_rgba(13,60,95,.1)]">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="12" viewBox="0 0 16 12" fill="none"><path d="M8 0C9.65463 0 11.2234 0.538182 12.5893 1.53455C13.9551 2.52364 15.118 3.97091 15.9532 5.78182C16.0156 5.92 16.0156 6.08 15.9532 6.21091C14.2829 9.83273 11.3093 12 8 12H7.9922C4.69073 12 1.71707 9.83273 0.0468293 6.21091C-0.0156098 6.08 -0.0156098 5.92 0.0468293 5.78182C1.71707 2.16 4.69073 0 7.9922 0H8ZM8 3.09091C6.27512 3.09091 4.87805 4.39273 4.87805 6C4.87805 7.6 6.27512 8.90182 8 8.90182C9.71707 8.90182 11.1141 7.6 11.1141 6C11.1141 4.39273 9.71707 3.09091 8 3.09091ZM8.00094 4.18022C9.07021 4.18022 9.94435 4.99476 9.94435 5.9984C9.94435 6.99476 9.07021 7.80931 8.00094 7.80931C6.92386 7.80931 6.04972 6.99476 6.04972 5.9984C6.04972 5.87476 6.06533 5.7584 6.08874 5.64204H6.12777C6.99411 5.64204 7.69655 5.00204 7.72777 4.20204C7.81362 4.18749 7.90728 4.18022 8.00094 4.18022Z" fill="#0E1935"></path></svg>
                                        <span class="tooltip"><?php echo Escape::htmlWithTranslation('مشاهده جزئیات') ?></span>
                                    </a>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                }

                else
                {
                    ?>
                    <div class="py-[50px] flex items-center justify-center w-full rounded-[25px] bg-[url(data:image/svg+xml,%3csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100%25' height='100%25' fill='none' rx='25' ry='25' stroke='%23DDE2E9' stroke-width='6' stroke-dasharray='21%2c 21' stroke-dashoffset='38' stroke-linecap='square'/%3e%3c/svg%3e)]">
                        <span class="flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="150" height="150" viewBox="0 0 150 150" fill="none"><path d="M14.8323 44.1052L93.1651 29.6021L106.815 103.327L28.4823 117.83L14.8323 44.1052Z" fill="#B3B9BF"></path><path d="M29.8665 22.2656L14.843 44.1164L44.8008 38.5688L93.1969 29.6086L108.22 7.75781L29.8665 22.2656Z" fill="#C0C2C9"></path><path d="M108.22 7.75781L93.1965 29.6086L106.851 103.355L121.875 81.5039L108.22 7.75781Z" fill="#A6ABB3"></path><path d="M44.7856 38.5598L63.2169 35.1473L68.7622 65.0981L50.331 68.5106L44.7856 38.5598Z" fill="#9BA1A8"></path><path d="M63.2391 35.1539L44.8008 38.5687L59.8242 16.7179L78.2625 13.3054L63.2391 35.1539Z" fill="#9BA1A8"></path><path d="M21.0938 21.0938H16.4062C16.4062 12.0492 23.7656 4.6875 32.8125 4.6875H35.1562V9.375H32.8125C26.3508 9.375 21.0938 14.632 21.0938 21.0938Z" fill="#9BA1A8"></path><path d="M111.875 135.312H109.531V130.625H111.875C118.337 130.625 123.594 125.368 123.594 118.906H128.281C128.281 127.951 120.922 135.312 111.875 135.312Z" fill="#9BA1A8"></path><path d="M104.844 128.281H102.5V123.594H104.844C111.305 123.594 116.562 118.337 116.562 111.875H121.25C121.25 120.92 113.891 128.281 104.844 128.281Z" fill="#9BA1A8"></path></svg>
                        </span>
                        <span class="mt-5 font-bold text-[18px] w-full">
                            <?php echo Escape::htmlWithTranslation('سفارشی موجود نیست!') ?>
                        </span>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php
    }

    private function bookmarks(): void
    {
        $products = User::getFavorites();
        ?>
        <div class="dashboard-panel dashboard-user-favorites-panel hidden flex flex-col w-full space-y-5 pt-[30px]">
            <?php
            if (!empty($products))
            {
                foreach ($products as $product)
                {
                    $product = wc_get_product($product);
                    ?>
                    <div class="flex items-center justify-between user-favorite-product overflow-auto">
                        <div class="flex items-start space-x-5 space-x-reverse">
                            <a href="<?php echo $product->get_permalink() ?>"
                                class="relative flex items-center justify-center w-[120px] h-[120px]">
                                <img src="<?php echo get_the_post_thumbnail_url($product->get_id()) ?>">
                            </a>
                            <a href="<?php $product->get_permalink() ?>"
                                class="text-[17px] text-darkblue font-bold">
                                <?php echo $product->get_title() ?>
                            </a>
                        </div>
                        <div class="flex items-end space-x-2.5 space-x-reverse">
                            <?php
                            if ($product->is_on_sale())
                            {
                                ?>
                                <div class="flex items-center h-[47px] space-x-2.5 space-x-reverse">
                                    <span class="pl-2.5 border-l border-solid border-[#d2d7df] space-x-1.5 space-x-reverse leading-[18px] flex items-center">
                                        <span class="text-[18px] text-[var(--gray)] font-bold">
                                            <?php echo number_format(Woocommerce::getRegularPrice($product) - Woocommerce::getSalePrice($product),3,',',',') ?>
                                        </span>
                                        <span class="text-[var(--gray)] text-[14px] w-max">
                                            <?php echo Escape::htmlWithTranslation('سود از خرید') ?>
                                        </span>
                                    </span>
                                    <div class="flex items-center space-x-1.5 space-x-reverse">
                                        <span class="text-[18px] font-bold">
                                            <?php echo Woocommerce::getSalePrice($product) ?>
                                        </span>
                                            <span class="text-[11px]">
                                            <?php echo get_woocommerce_currency_symbol() ?>
                                        </span>
                                    </div>
                                </div>
                                <?php
                            }

                            else
                            {
                                ?>
                                <div class="flex items-center space-x-1.5 space-x-reverse">
                                    <span class="text-[18px] font-bold">
                                        <?php echo Woocommerce::getRegularPrice($product) ?>
                                    </span>
                                    <span class="text-[11px]">
                                        <?php echo get_woocommerce_currency_symbol() ?>
                                    </span>
                                </div>
                                <?php
                            }
                            ?>
                            <a href="<?php echo $product->get_permalink() ?>"
                                class="w-[170px] h-[47px] py-2.5 bg-white space-x-2.5 space-x-reverse border flex items-center justify-center border-solid border-[var(--green)] cursor-pointer rounded-[10px]">
                                <span class="flex items-center justify-center mb-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none"><path d="M6 0C7.24098 0 8.41756 0.403636 9.44195 1.15091C10.4663 1.89273 11.3385 2.97818 11.9649 4.33636C12.0117 4.44 12.0117 4.56 11.9649 4.65818C10.7122 7.37455 8.48195 9 6 9H5.99415C3.51805 9 1.2878 7.37455 0.035122 4.65818C-0.0117073 4.56 -0.0117073 4.44 0.035122 4.33636C1.2878 1.62 3.51805 0 5.99415 0H6ZM6 2.31818C4.70634 2.31818 3.65854 3.29455 3.65854 4.5C3.65854 5.7 4.70634 6.67636 6 6.67636C7.28781 6.67636 8.33561 5.7 8.33561 4.5C8.33561 3.29455 7.28781 2.31818 6 2.31818ZM6.0007 3.13516C6.80265 3.13516 7.45826 3.74607 7.45826 4.4988C7.45826 5.24607 6.80265 5.85698 6.0007 5.85698C5.1929 5.85698 4.53729 5.24607 4.53729 4.4988C4.53729 4.40607 4.549 4.3188 4.56656 4.23153H4.59582C5.24558 4.23153 5.77241 3.75153 5.79582 3.15153C5.86021 3.14062 5.93046 3.13516 6.0007 3.13516Z" fill="#6FA336"></path></svg>
                                </span>
                                <span class="text-[15px] font-medium text-green">
                                    <?php echo Escape::htmlWithTranslation('مشاهده محصول') ?>
                                </span>
                            </a>
                            <div class="remove-from-user-favorites w-[35px] h-[47px] mr-2.5 flex items-center justify-center shadow-[0_2px_8px_rgba(13,60,95,.1)] rounded-[10px] cursor-pointer"
                                 data-id="<?php echo $product->get_id() ?>"
                                 data-parent=".user-favorite-product">
                                <input type="hidden" name="remove-user-favorite" value="<?php echo Nonce::generate('remove-favorite-nonce') ?>">
                                <svg width="17" height="17" viewBox="0 0 17 17" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M14.315 5.87584C14.6507 5.90304 14.901 6.19185 14.8741 6.52225C14.8693 6.57665 14.4288 11.9455 14.1753 14.1976C14.0176 15.5952 13.0798 16.4456 11.6642 16.4712C10.5809 16.4896 9.53581 16.5 8.5143 16.5C7.41315 16.5 6.33964 16.488 5.27749 16.4664C3.91873 16.44 2.97849 15.5728 2.8249 14.2032C2.56891 11.9311 2.13089 6.57585 2.12683 6.52225C2.0992 6.19185 2.3495 5.90224 2.68512 5.87584C3.01587 5.86704 3.31493 6.09584 3.34175 6.42545C3.34434 6.46017 3.52341 8.64711 3.71855 10.8109L3.75774 11.2427C3.85602 12.3182 3.95565 13.3517 4.03657 14.0712C4.12352 14.8496 4.55017 15.2512 5.30269 15.2664C7.33433 15.3088 9.40741 15.3112 11.6422 15.2712C12.4419 15.256 12.8742 14.8624 12.9636 14.0656C13.2155 11.8303 13.6544 6.47985 13.6592 6.42545C13.686 6.09584 13.9827 5.86544 14.315 5.87584ZM10.2199 0.5C10.9659 0.5 11.6217 0.995207 11.8143 1.70482L12.0208 2.71363C12.0875 3.04434 12.3823 3.28585 12.7236 3.29115L15.3905 3.29124C15.7269 3.29124 16 3.56005 16 3.89125C16 4.22246 15.7269 4.49126 15.3905 4.49126L12.7474 4.49114C12.7433 4.49122 12.7392 4.49126 12.7351 4.49126L12.7152 4.49046L4.28444 4.49116C4.27789 4.49123 4.27133 4.49126 4.26476 4.49126L4.25225 4.49046L1.60949 4.49126C1.27305 4.49126 1 4.22246 1 3.89125C1 3.56005 1.27305 3.29124 1.60949 3.29124L4.27582 3.29044L4.35791 3.28533C4.6637 3.24627 4.91784 3.01764 4.9799 2.71363L5.17738 1.74082C5.3781 0.995207 6.03392 0.5 6.77993 0.5H10.2199ZM10.2199 1.70002H6.77993C6.5849 1.70002 6.41342 1.82882 6.36385 2.01362L6.1745 2.94964C6.15044 3.0682 6.1154 3.18245 6.07055 3.29146H10.9295C10.8846 3.18245 10.8495 3.0682 10.8253 2.94964L10.6279 1.97682C10.5864 1.82882 10.4149 1.70002 10.2199 1.70002Z" fill="#0E1935" stroke="#0E1935" stroke-width="0.4"></path></svg>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }

            else
                echo Notice::nothingToShow();
            ?>
        </div>
        <?php
    }

    public function orderBill(WC_Order $order): void
    {
        $discountedPrice = 0;
        $productPrices = 0;

        $orderItems = $order->get_items(apply_filters('woocommerce_purchase_order_item_types','line_item'));

        foreach ($orderItems as $itemId => $item)
        {
            $product = $item->get_product();
            $discountedPrice += Woocommerce::getDiscountedPrice($product);
            $productPrices   += Woocommerce::getRegularPrice($product);
        }
        ?>
        <div class="order-bill overflow-hidden relative flex items-center justify-start min-h-[210px] mb-5 py-[50px] bg-white rounded-[25px] shadow-[0_4px_30px_rgb(237_240_245)]">
            <div class="order-bill-icon flex flex-col items-center justify-center space-y-3.5">
                <span class="font-bold text-[15px] text-darkblue">
                    <?php echo Escape::htmlWithTranslation('جزئیات قیمت') ?>
                </span>
                <span class="flex items-center justify-center w-[73px] h-[73px]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="72" height="72" viewBox="0 0 72 72">
                        <circle opacity="0.5" cx="36" cy="36" r="36" fill="#C2E1A0"></circle>
                        <path d="M36 61C49.8071 61 61 49.8071 61 36C61 22.1929 49.8071 11 36 11C22.1929 11 11 22.1929 11 36C11 49.8071 22.1929 61 36 61Z" fill="white"></path>
                        <g>
                        <path d="M24.9905 42.1791C25.0251 43.8867 25.0684 45.5943 25.1203 47.3018C25.1423 48.008 25.7395 48.6265 26.4518 48.6747C27.9579 48.7756 29.464 48.8528 30.9702 48.9066C30.937 46.7306 30.9103 44.5546 30.8899 42.3785C28.9235 42.336 26.957 42.2695 24.9905 42.1791Z" fill="url(#paint1_linear)"></path>
                        <path d="M29.5791 42.3466C29.5995 44.0821 29.6249 45.8176 29.6555 47.5531C29.6686 48.2715 30.258 48.881 30.9703 48.9066C35.919 49.0832 40.8676 49.006 45.8162 48.6747C46.5287 48.6263 47.1259 48.0083 47.1477 47.3018C47.1997 45.5942 47.243 43.8866 47.2776 42.179C41.3781 42.4504 35.4786 42.5062 29.5791 42.3466Z" fill="url(#paint1_linear)"></path>
                        <path d="M26.2801 22.9191C25.5549 22.9456 24.9562 23.5431 24.9451 24.2495C24.8543 30.2261 24.8694 36.2026 24.9906 42.1792C25.0053 42.8858 25.604 43.4899 26.3259 43.5237C27.8517 43.5935 29.3774 43.647 30.9031 43.6842C30.8298 36.721 30.8216 29.7577 30.8787 22.7944C29.3457 22.8232 27.8129 22.8648 26.2801 22.9191Z" fill="url(#paint2_linear)"></path>
                        <path d="M34.8203 22.7476C33.5065 22.7539 32.1926 22.7693 30.8787 22.7942C30.1534 22.8082 29.559 23.4054 29.5524 24.1237C29.4951 30.6318 29.5095 37.1399 29.5954 43.6482C35.0443 43.8144 40.4933 43.7728 45.9423 43.5235C46.6642 43.4897 47.263 42.8857 47.2777 42.179C47.3253 39.831 47.3565 37.4831 47.3714 35.1352C43.2107 31.1394 38.9724 26.8835 34.8203 22.7476Z" fill="url(#paint3_linear)"></path>
                        <path d="M45.9879 22.9191C42.2654 22.7872 38.5428 22.7301 34.8201 22.7477C34.8024 29.6241 40.4401 35.2562 47.3712 35.1354C47.3942 31.5067 47.3781 27.8781 47.323 24.2495C47.3119 23.5429 46.7132 22.9455 45.9879 22.9191Z" fill="url(#paint4_linear)"></path>
                        <path d="M42.7323 26.2931C42.7404 27.4901 41.7571 28.4521 40.5403 28.4461C39.3231 28.4395 38.3357 27.4581 38.3335 26.2504C38.3316 25.0431 39.311 24.0699 40.5219 24.0868C41.7328 24.1044 42.7239 25.0965 42.7323 26.2931Z" fill="url(#paint5_linear)"></path>
                        <path d="M33.9347 26.2505C33.9325 27.4582 32.945 28.4395 31.7279 28.4461C30.5109 28.4521 29.5278 27.49 29.5359 26.2932C29.5443 25.0965 30.5353 24.1041 31.7463 24.0869C32.9574 24.0698 33.9366 25.043 33.9347 26.2505Z" fill="url(#paint6_linear)"></path>
                        <path d="M39.6482 24.9492C39.65 25.3858 39.6516 25.8222 39.6531 26.2588C39.6547 26.7411 40.0499 27.1345 40.5363 27.1383C41.0226 27.1422 41.415 26.7563 41.4127 26.2758C41.4104 25.8407 41.408 25.4054 41.4053 24.9702C40.8195 24.9619 40.2339 24.9549 39.6482 24.9492Z" fill="url(#paint7_linear)"></path>
                        <path d="M30.8629 24.9702C30.8601 25.4054 30.8577 25.8406 30.8555 26.2758C30.8531 26.7565 31.2456 27.1422 31.7319 27.1383C32.2181 27.1343 32.6136 26.7409 32.615 26.2588C32.6165 25.8222 32.6181 25.3858 32.6199 24.9492C32.0343 24.9549 31.4486 24.9619 30.8629 24.9702Z" fill="url(#paint8_linear)"></path>
                        <path d="M36.134 36.8308C38.9772 36.8286 41.3277 34.3927 41.3651 31.5161C41.3818 29.9204 41.3953 28.3246 41.4053 26.7289C41.4084 26.2482 41.0167 25.8638 40.5309 25.8686C40.0454 25.8736 39.6499 26.2677 39.6482 26.7499C39.6416 28.3504 39.6326 29.951 39.6214 31.5516C39.6049 33.4748 38.038 35.0801 36.134 35.0805C34.23 35.08 32.6632 33.4749 32.6467 31.5516C32.6355 29.951 32.6265 28.3504 32.6199 26.7499C32.618 26.2675 32.2229 25.8735 31.7372 25.8686C31.2514 25.8637 30.8598 26.2483 30.8628 26.7289C30.8728 28.3246 30.8862 29.9204 30.903 31.5161C30.9404 34.3927 33.2907 36.8285 36.134 36.8308Z" fill="white"></path>
                        </g>
                        <defs>
                        <linearGradient id="paint0_linear" x1="6.35279" y1="66.9985" x2="64.1999" y2="8.68823" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#E0E9DD"></stop>
                        <stop offset="0.5" stop-color="#98E231"></stop>
                        <stop offset="1" stop-color="#E6FE7F"></stop>
                        </linearGradient>
                        <linearGradient id="paint1_linear" x1="26.2827" y1="57.0741" x2="69.1584" y2="13.8549" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#95E12E"></stop>
                        <stop offset="1" stop-color="#E6FE7F"></stop>
                        </linearGradient>
                        <linearGradient id="paint2_linear" x1="20.0377" y1="41.5286" x2="41.9786" y2="19.412" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#95E12E"></stop>
                        <stop offset="1" stop-color="#E6FE7F"></stop>
                        </linearGradient>
                        <linearGradient id="paint3_linear" x1="13.8727" y1="57.4921" x2="54.2455" y2="16.7961" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#95E12E"></stop>
                        <stop offset="1" stop-color="#E6FE7F"></stop>
                        </linearGradient>
                        <linearGradient id="paint4_linear" x1="8.92737" y1="60.8542" x2="46.2338" y2="23.249" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#95E12E"></stop>
                        <stop offset="1" stop-color="#E6FE7F"></stop>
                        </linearGradient>
                        <linearGradient id="paint5_linear" x1="38.4537" y1="28.3292" x2="46.7658" y2="19.9505" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#95E12E"></stop>
                        <stop offset="1" stop-color="#E6FE7F"></stop>
                        </linearGradient>
                        <linearGradient id="paint6_linear" x1="30.1756" y1="27.8237" x2="40.2048" y2="17.7142" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#95E12E"></stop>
                        <stop offset="1" stop-color="#E6FE7F"></stop>
                        </linearGradient>
                        <linearGradient id="paint7_linear" x1="25.0588" y1="41.1412" x2="41.8259" y2="24.2397" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#95E12E"></stop>
                        <stop offset="1" stop-color="#E6FE7F"></stop>
                        </linearGradient>
                        <linearGradient id="paint8_linear" x1="16.1044" y1="41.3055" x2="33.1132" y2="24.1605" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#95E12E"></stop>
                        <stop offset="1" stop-color="#E6FE7F"></stop>
                        </linearGradient>
                        <clipPath id="clip0">
                        <rect width="22" height="26" fill="white" transform="translate(25 23)"></rect>
                        </clipPath>
                        </defs>
                    </svg>
                </span>
            </div>
            <div class="flex grow flex-col px-[60px] space-y-5">
                <div class="flex items-center justify-between space-x-2.5 space-x-reverse">
                    <div class="flex items-center justify-between w-1/2">
                        <span class="text-darkblue text-[15px]">
                            <?php echo Escape::htmlWithTranslation('جمع مبلغ کالاها:') ?>
                        </span>
                        <span class="flex items-center space-x-0.5 space-x-reverse">
                            <span class="text-[22px] text-darkblue font-bold">
                                <?php echo $productPrices ?>
                            </span>
                            <span class="mt-[5px] text-[11px] text-darkblue font-regular">
                                <?php echo get_woocommerce_currency_symbol() ?>
                            </span>
                        </span>
                    </div>
                    <div class="flex items-center justify-between w-1/2">
                        <span class="text-darkblue text-[15px]">
                            <?php echo Escape::htmlWithTranslation('سود شما از خرید:') ?>
                        </span>
                        <span class="flex items-center space-x-0.5 space-x-reverse">
                            <span class="text-[22px] text-darkblue font-bold">
                                <?php echo $discountedPrice ?>
                            </span>
                            <span class="mt-[5px] text-[11px] text-darkblue font-regular">
                                <?php echo get_woocommerce_currency_symbol() ?>
                            </span>
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-between space-x-2.5 space-x-reverse">
                    <div class="flex items-center justify-between w-1/2">
                        <span class="text-green text-[15px]">
                            <?php echo Escape::htmlWithTranslation(' مبلغ پرداخت شده :') ?>
                        </span>
                        <span class="flex items-center space-x-0.5 space-x-reverse">
                            <span class="text-[22px] text-green font-bold">
                                <?php echo number_format((float)$order->get_data()['total'],0,',',',') ?>
                            </span>
                            <span class="mt-[5px] text-[11px] text-green font-regular">
                                <?php echo get_woocommerce_currency_symbol() ?>
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function userPurchaseNotes(WC_Order $order): void
    {
        if ($order->get_customer_note())
        {
            ?>
            <div class="flex justify-between mx-[30px] items-center relative order-customer-note my-1.5 py-[15px] px-5 rounded-[15px] bg-[#fff3db]">
                <div class="flex items-center justify-start w-full">
                    <span class="flex w-[24px] h-[25px] ml-[7px] items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 24" fill="none">
                            <path d="M8 2V5" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16 2V5" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M21 8.5V13.63C20.11 12.92 18.98 12.5 17.75 12.5C16.52 12.5 15.37 12.93 14.47 13.66C13.26 14.61 12.5 16.1 12.5 17.75C12.5 18.73 12.78 19.67 13.26 20.45C13.63 21.06 14.11 21.59 14.68 22H8C4.5 22 3 20 3 17V8.5C3 5.5 4.5 3.5 8 3.5H16C19.5 3.5 21 5.5 21 8.5Z" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7 11H13" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7 16H9.62" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M23 17.75C23 18.73 22.72 19.67 22.24 20.45C21.96 20.93 21.61 21.35 21.2 21.69C20.28 22.51 19.08 23 17.75 23C16.6 23 15.54 22.63 14.68 22C14.11 21.59 13.63 21.06 13.26 20.45C12.78 19.67 12.5 18.73 12.5 17.75C12.5 16.1 13.26 14.61 14.47 13.66C15.37 12.93 16.52 12.5 17.75 12.5C18.98 12.5 20.11 12.92 21 13.63C22.22 14.59 23 16.08 23 17.75Z" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17.75 20.25C17.75 18.87 18.87 17.75 20.25 17.75C18.87 17.75 17.75 16.63 17.75 15.25C17.75 16.63 16.63 17.75 15.25 17.75C16.63 17.75 17.75 18.87 17.75 20.25Z" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <div class="flex items-start justify-start flex-col">
                        <span class="text-[15px] text-darkblue leading-[27px]">
                            <?php echo wp_kses_post(nl2br(wptexturize($order->get_customer_note()))); ?>
                        </span>
                        <small class="text-[11px]">
                            <?php echo Escape::htmlWithTranslation('(یادداشت مشتری)') ?>
                        </small>
                    </div>
                </div>
            </div>
            <?php
        }
    }

    /**
     * Changes the number of posts per page for shop
     *
     * @return mixed
     */
    public function shopPostsPerPage(): int
    {
        return CodeStar::getOption('loop-shop-per-page',16);
    }

    public function cartPageOpenWrapper(): void
    {
        echo '<div class="flex items-start justify-between min-[1024px]:space-x-5 min-[1024px]:space-x-reverse container px-5 mx-auto">';
    }

    public function cartPageCloseWrapper(): void
    {
        echo '</div>';
    }
}

new Hooks();