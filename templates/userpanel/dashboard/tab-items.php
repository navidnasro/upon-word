<?php

/**
 * Template for dashboard tab items
 * Displayed in my-account page
 */

defined('ABSPATH') || exit;

?>

<ul class="user-account-tabs flex-col sm:flex-row space-y-3.5 sm:space-y-0 sm:flex-wrap tabs flex items-center justify-start mb-14 sm:space-x-2 sm:space-x-reverse">
    <li
        class="active tab-item w-full sm:w-max cursor-pointer flex space-x-2.5 space-x-reverse items-center flex-1 justify-center py-4 rounded-[15px] bg-white border border-solid border-[#EBEBEB]"
        data-panel="#userorders">
        <span class="flex items-center justify-center mb-[5px]">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path d="M16.25 22.5C17.2165 22.5 18 21.7165 18 20.75C18 19.7835 17.2165 19 16.25 19C15.2835 19 14.5 19.7835 14.5 20.75C14.5 21.7165 15.2835 22.5 16.25 22.5Z" fill="#43454D"/>
              <path d="M8.25 22.5C9.2165 22.5 10 21.7165 10 20.75C10 19.7835 9.2165 19 8.25 19C7.2835 19 6.5 19.7835 6.5 20.75C6.5 21.7165 7.2835 22.5 8.25 22.5Z" fill="#43454D"/>
              <path d="M4.84 3.94L4.64 6.39C4.6 6.86 4.97 7.25 5.44 7.25H20.75C21.17 7.25 21.52 6.93 21.55 6.51C21.68 4.74 20.33 3.3 18.56 3.3H6.27C6.17 2.86 5.97 2.44 5.66 2.09C5.16 1.56 4.46 1.25 3.74 1.25H2C1.59 1.25 1.25 1.59 1.25 2C1.25 2.41 1.59 2.75 2 2.75H3.74C4.05 2.75 4.34 2.88 4.55 3.1C4.76 3.33 4.86 3.63 4.84 3.94Z" fill="#43454D"/>
              <path d="M20.5101 8.75H5.17005C4.75005 8.75 4.41005 9.07 4.37005 9.48L4.01005 13.83C3.87005 15.54 5.21005 17 6.92005 17H18.0401C19.5401 17 20.8601 15.77 20.9701 14.27L21.3001 9.6C21.3401 9.14 20.9801 8.75 20.5101 8.75Z" fill="#43454D"/>
            </svg>
        </span>
        <span class="flex items-center justify-center text-[var(--title)] text-[15px] font-bold">
            سفارش های من
        </span>
    </li>
    <li
        class="flex items-center w-full sm:w-max tab-item cursor-pointer space-x-2.5 space-x-reverse flex-1 justify-center py-4 rounded-[15px] bg-white border border-solid border-[#EBEBEB]"
        data-panel="#useraddresses">
        <span class="flex items-center justify-center mb-[5px]">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path opacity="0.4" d="M12.0009 13.4299C13.724 13.4299 15.1209 12.0331 15.1209 10.3099C15.1209 8.58681 13.724 7.18994 12.0009 7.18994C10.2777 7.18994 8.88086 8.58681 8.88086 10.3099C8.88086 12.0331 10.2777 13.4299 12.0009 13.4299Z" stroke="#43454D" stroke-width="1.5"/>
              <path d="M3.61971 8.49C5.58971 -0.169998 18.4197 -0.159997 20.3797 8.5C21.5297 13.58 18.3697 17.88 15.5997 20.54C13.5897 22.48 10.4097 22.48 8.38971 20.54C5.62971 17.88 2.46971 13.57 3.61971 8.49Z" stroke="#43454D" stroke-width="1.5"/>
            </svg>
        </span>
        <span class="flex items-center justify-center text-[var(--title)] text-[15px] font-bold">
            آدرس های من
        </span>
    </li>
    <li
        class="flex items-center w-full sm:w-max tab-item cursor-pointer space-x-2.5 space-x-reverse flex-1 justify-center py-4 rounded-[15px] bg-white border border-solid border-[#EBEBEB]"
        data-panel="#useraccount">
        <span class="flex items-center justify-center mb-[5px]">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path opacity="0.4" d="M18.1394 21.6198C17.2594 21.8798 16.2194 21.9998 14.9994 21.9998H8.99937C7.77937 21.9998 6.73937 21.8798 5.85938 21.6198C6.07937 19.0198 8.74937 16.9697 11.9994 16.9697C15.2494 16.9697 17.9194 19.0198 18.1394 21.6198Z" stroke="#43454D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M22 9V15C22 18.78 20.86 20.85 18.14 21.62C17.26 21.88 16.22 22 15 22H9C7.78 22 6.74 21.88 5.86 21.62C3.14 20.85 2 18.78 2 15V9C2 4 4 2 9 2H15C20 2 22 4 22 9Z" stroke="#43454D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path opacity="0.4" d="M15.5799 10.58C15.5799 12.56 13.9799 14.17 11.9999 14.17C10.0199 14.17 8.41992 12.56 8.41992 10.58C8.41992 8.60002 10.0199 7 11.9999 7C13.9799 7 15.5799 8.60002 15.5799 10.58Z" stroke="#43454D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        <span class="flex items-center justify-center text-[var(--title)] text-[15px] font-bold">
            حساب کاربری من
        </span>
    </li>
    <li
        class="flex items-center w-full sm:w-max tab-item cursor-pointer space-x-2.5 space-x-reverse flex-1 justify-center py-4 rounded-[15px] bg-white border border-solid border-[#EBEBEB]"
        data-panel="#userlastseen">
        <span class="flex items-center justify-center mb-[5px]">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path opacity="0.4" d="M15.5799 11.9999C15.5799 13.9799 13.9799 15.5799 11.9999 15.5799C10.0199 15.5799 8.41992 13.9799 8.41992 11.9999C8.41992 10.0199 10.0199 8.41992 11.9999 8.41992C13.9799 8.41992 15.5799 10.0199 15.5799 11.9999Z" stroke="#43454D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M11.9998 20.2702C15.5298 20.2702 18.8198 18.1902 21.1098 14.5902C22.0098 13.1802 22.0098 10.8102 21.1098 9.40021C18.8198 5.80021 15.5298 3.72021 11.9998 3.72021C8.46984 3.72021 5.17984 5.80021 2.88984 9.40021C1.98984 10.8102 1.98984 13.1802 2.88984 14.5902C5.17984 18.1902 8.46984 20.2702 11.9998 20.2702Z" stroke="#43454D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        <span class="flex items-center justify-center text-[var(--title)] text-[15px] font-bold">
            آخرین بازدیدها
        </span>
    </li>
</ul>
