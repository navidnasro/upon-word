<?php

namespace engine;

use engine\security\Escape;
use engine\security\Nonce;
use engine\utils\Request;
use engine\utils\User;

defined('ABSPATH') || exit;

class Redirects
{
    public function __construct()
    {
        add_action('template_redirect',[$this,'savePasswordForm']);
    }

    public function savePasswordForm(): void
    {
        $request = Request::post();

        if (is_null($request))
            return;

        if (!($request->has('save_password') &&
            $request->has('save-password-nonce') &&
            Nonce::verify($request->getParam('save-password-nonce'),'save_password')))
        {
            wc_add_notice(
                Escape::htmlWithTranslation('عملیات ناموفق!دوباره تلاش کنید'),
                'error'
            );

            return;
        }

        $userId = User::getCurrentUser(false);
        $current_password  = $request->has('password_current') ? $request->getParam('password_current') : '';
        $new_password      = $request->has('password_1') ? $request->getParam('password_1') : '';
        $confirm_password  = $request->has('password_2') ? $request->getParam('password_2') : '';

        if ( empty( $current_password ) || empty( $new_password ) || empty( $confirm_password ) )
        {
            wc_add_notice(
                Escape::htmlWithTranslation('لطفا تمام فیلد ها را وارد کنید!'),
                'error');
            return;
        }

        if ( $new_password !== $confirm_password )
        {
            wc_add_notice(
                Escape::htmlWithTranslation('رمزعبور جدید با تکرار ان یکی نیست!'),
                'error');
            return;
        }

        $user = User::getCurrentUser();

        if (!wp_check_password($current_password, $user->user_pass, $userId))
        {
            wc_add_notice(
                Escape::htmlWithTranslation('رمزعبور فعلی نادرست است!'),
                'error'
            );

            return;
        }

        // Everything is valid, update the password
        wp_set_password( $new_password, $userId );

        wc_add_notice(
            Escape::htmlWithTranslation('رمزعبور با موفقیت تغییر یافت!')
        );

        wp_safe_redirect(wc_get_account_endpoint_url('change-password'));
        exit;
    }
}

new Redirects();