/**
 * Does the sharing functionality
 * 1.saving page link to clipboard
 *
 * @instructions
 * button must have
 * 1."share" class
 */

$(document).ready(function (){

    $('.share').each(function (){

        $(this).on('click',function (){

            navigator.clipboard.writeText(window.location.href);

            $('body').append('<div class="toast fixed z-[99999999] space-x-2.5 space-x-reverse bottom-10 right-10 flex items-center justify-start border border-solid border-black/[.1] shadow-[0_0.25rem_0.75rem_rgba(0,0,0,.1)] rounded-[0.25rem] overflow-hidden px-[15px] py-1.5 md:w-[350px] bg-white/[.85]">' +
                '<span class="w-5 h-5 bg-green-500 rounded-[0.25rem]"></span>' +
                '<span class="text-xs font-medium text-right">لینک کپی شد</span>' +
                '</div>');

            setTimeout(function (){

                $('body > .toast').remove();

            },30000);
        });

    });

});