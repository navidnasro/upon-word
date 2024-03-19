<?php

defined('ABSPATH') || exit;

$user = wp_get_current_user();
?>

<div
    id="useraccount"
    class="tab-panel hidden flex w-full flex-col items-start space-y-7">
    <div class="flex w-full items-center justify-between">
        <div class="flex items-center justify-between space-x-3 space-x-reverse">
            <span class="flex items-center justify-center -mt-[3px]">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                  <path opacity="0.4" d="M18.1394 21.6198C17.2594 21.8798 16.2194 21.9998 14.9994 21.9998H8.99937C7.77937 21.9998 6.73937 21.8798 5.85938 21.6198C6.07937 19.0198 8.74937 16.9697 11.9994 16.9697C15.2494 16.9697 17.9194 19.0198 18.1394 21.6198Z" stroke="#43454D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M22 9V15C22 18.78 20.86 20.85 18.14 21.62C17.26 21.88 16.22 22 15 22H9C7.78 22 6.74 21.88 5.86 21.62C3.14 20.85 2 18.78 2 15V9C2 4 4 2 9 2H15C20 2 22 4 22 9Z" stroke="#43454D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                  <path opacity="0.4" d="M15.5799 10.58C15.5799 12.56 13.9799 14.17 11.9999 14.17C10.0199 14.17 8.41992 12.56 8.41992 10.58C8.41992 8.60002 10.0199 7 11.9999 7C13.9799 7 15.5799 8.60002 15.5799 10.58Z" stroke="#43454D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <span class="text-[#313131] text-[15px] font-bold">مشخصات کاربری</span>
        </div>
    </div>
    <div class="w-full rounded-[15px] bg-white border border-solid border-[#EBEBEB] px-9 py-[30px] flex items-start justify-between">
        <div class="flex flex-col items-start space-y-[34px]">
            <address class="flex flex-col items-start space-y-[18px] not-italic">
                <div class="flex items-center justify-start space-x-2.5 space-x-reverse">
                    <span class="flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="14" viewBox="0 0 15 14" fill="none">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="M10.9305 0C13.2385 0 14.9789 1.88317 14.9789 4.38042V9.14067C14.9789 10.4188 14.527 11.5735 13.7057 12.3927C12.9689 13.1267 12.0131 13.5155 10.9416 13.5155H4.1124C3.04294 13.5155 2.08785 13.1274 1.35038 12.3927C0.529053 11.5735 0.0771484 10.4188 0.0771484 9.14067V4.38042C0.0771484 1.88317 1.81753 0 4.12557 0H10.9305ZM10.9305 1.03966H4.12557C2.38242 1.03966 1.11681 2.44458 1.11681 4.38042V9.14067C1.11681 10.1408 1.46059 11.0342 2.08438 11.6559C2.62223 12.1931 3.32434 12.4759 4.11448 12.4759H10.9305C10.9319 12.4745 10.9374 12.4759 10.9416 12.4759C11.7324 12.4759 12.4338 12.1931 12.9717 11.6559C13.5962 11.0342 13.9392 10.1408 13.9392 9.14067V4.38042C13.9392 2.44458 12.6736 1.03966 10.9305 1.03966ZM12.0228 4.2479C12.2037 4.47039 12.1697 4.79753 11.9473 4.97913L8.8671 7.48262C8.47758 7.79175 8.01181 7.94631 7.54674 7.94631C7.08305 7.94631 6.62075 7.79313 6.234 7.48678L3.12542 4.98051C2.90155 4.80031 2.8669 4.47247 3.04641 4.24929C3.22731 4.0268 3.55446 3.99145 3.77764 4.17097L6.88344 6.67446C7.27366 6.98359 7.82329 6.98359 8.21628 6.67169L11.2909 4.17235C11.5141 3.99007 11.8412 4.02472 12.0228 4.2479Z" fill="#43454D"/>
                        </svg>
                    </span>
                    <span class="text-[var(--title)] text-[15px] font-bold">
                        ایمیل :
                        <?php echo $user->user_email ?>
                    </span>
                </div>
                <div class="flex items-center justify-start space-x-2.5 space-x-reverse">
                    <span class="flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="16" viewBox="0 0 13 16" fill="none">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="M6.54616 0C10.0932 0 12.979 2.90766 12.979 6.482C12.979 10.8759 7.94502 15.205 6.54616 15.205C5.1473 15.205 0.113281 10.8759 0.113281 6.482C0.113281 2.90766 2.99911 0 6.54616 0ZM6.54616 1.16961C3.64396 1.16961 1.2829 3.55329 1.2829 6.482C1.2829 10.2084 5.66817 13.8389 6.54616 14.0323C7.42415 13.8381 11.8094 10.2076 11.8094 6.482C11.8094 3.55329 9.44836 1.16961 6.54616 1.16961ZM6.54694 3.89872C7.94424 3.89872 9.08111 5.03558 9.08111 6.43366C9.08111 7.83096 7.94424 8.96704 6.54694 8.96704C5.14964 8.96704 4.01278 7.83096 4.01278 6.43366C4.01278 5.03558 5.14964 3.89872 6.54694 3.89872ZM6.54694 5.06833C5.79449 5.06833 5.18239 5.68043 5.18239 6.43366C5.18239 7.18611 5.79449 7.79743 6.54694 7.79743C7.29939 7.79743 7.91149 7.18611 7.91149 6.43366C7.91149 5.68043 7.29939 5.06833 6.54694 5.06833Z" fill="#43454D"/>
                        </svg>
                    </span>
                    <span class="text-[var(--title)] text-[15px] font-bold">
                        نام نمایشی :
                        <?php echo $user->display_name ?>
                    </span>
                </div>
                <div class="flex items-center justify-start space-x-2.5 space-x-reverse">
                    <span class="flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="14" viewBox="0 0 11 14" fill="none">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="M10.9788 11.2237C10.9788 13.5082 7.84595 13.7723 5.49008 13.7723L5.3215 13.7721C3.82054 13.7685 0 13.6737 0 11.2099C0 8.97201 3.00693 8.67294 5.34487 8.66166L5.65867 8.66146C7.15953 8.66511 10.9788 8.75987 10.9788 11.2237ZM5.49008 9.70097C2.53676 9.70097 1.03966 10.2083 1.03966 11.2099C1.03966 12.2204 2.53676 12.7326 5.49008 12.7326C8.44271 12.7326 9.93913 12.2253 9.93913 11.2237C9.93913 10.2132 8.44271 9.70097 5.49008 9.70097ZM5.49008 0C7.5195 0 9.16978 1.65098 9.16978 3.68039C9.16978 5.7098 7.5195 7.36008 5.49008 7.36008H5.4679C3.44265 7.35384 1.80207 5.70217 1.80898 3.67831C1.80898 1.65098 3.45998 0 5.49008 0ZM5.49008 0.989754C4.00615 0.989754 2.79874 2.19645 2.79874 3.68039C2.79391 5.15947 3.99228 6.36548 5.46998 6.37102L5.49008 6.8659V6.37102C6.97333 6.37102 8.18002 5.16363 8.18002 3.68039C8.18002 2.19645 6.97333 0.989754 5.49008 0.989754Z" fill="#43454D"/>
                        </svg>
                    </span>
                    <span class="text-[var(--title)] text-[15px] font-bold">
                        نام و نام خانوادگی :
                        <?php echo $user->first_name.' '.$user->last_name ?>
                    </span>
                </div>
            </address>
        </div>
        <div class="flex items-start justify-center">
            <a class="w-full flex items-center rounded-[10px] bg-[var(--theme-primary)] justify-center px-5 py-2 space-x-2 space-x-reverse"
               href="<?php echo esc_url(wc_get_endpoint_url('edit-account')); ?>">
            <span class="flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                  <path d="M14.75 11.75H9.25C8.84 11.75 8.5 11.41 8.5 11C8.5 10.59 8.84 10.25 9.25 10.25H14.75C15.16 10.25 15.5 10.59 15.5 11C15.5 11.41 15.16 11.75 14.75 11.75Z" fill="white"/>
                  <path d="M12 14.5C11.59 14.5 11.25 14.16 11.25 13.75V8.25C11.25 7.84 11.59 7.5 12 7.5C12.41 7.5 12.75 7.84 12.75 8.25V13.75C12.75 14.16 12.41 14.5 12 14.5Z" fill="white"/>
                  <path d="M11.9997 22.76C10.5197 22.76 9.02969 22.2 7.86969 21.09C4.91969 18.25 1.65969 13.72 2.88969 8.33C3.99969 3.44 8.26969 1.25 11.9997 1.25C11.9997 1.25 11.9997 1.25 12.0097 1.25C15.7397 1.25 20.0097 3.44 21.1197 8.34C22.3397 13.73 19.0797 18.25 16.1297 21.09C14.9697 22.2 13.4797 22.76 11.9997 22.76ZM11.9997 2.75C9.08969 2.75 5.34969 4.3 4.35969 8.66C3.27969 13.37 6.23969 17.43 8.91969 20C10.6497 21.67 13.3597 21.67 15.0897 20C17.7597 17.43 20.7197 13.37 19.6597 8.66C18.6597 4.3 14.9097 2.75 11.9997 2.75Z" fill="white"/>
                </svg>
            </span>
                <span class="text-white text-[15px] font-bold">
                    ویرایش
                </span>
            </a>
        </div>
    </div>
</div>