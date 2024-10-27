function fireMessage(message = '',type = 'warning')
{
    if (type === 'warning')
        type = 'yellow';

    else if (type === 'success')
        type = 'green';

    else if (type === 'error')
        type = 'red';

    $('body').append('<div class="toast fixed z-[99999999] space-x-2.5 space-x-reverse bottom-10 right-10 flex items-center justify-start border border-solid border-black/[.1] shadow-[0_0.25rem_0.75rem_rgba(0,0,0,.1)] rounded-[0.25rem] overflow-hidden px-[15px] py-1.5 md:w-[350px] bg-white/[.85]">' +
        '<span class="w-5 h-5 bg-'+type+'-500 rounded-[0.25rem]"></span>' +
        '<span class="text-xs font-medium text-right">'+message+'</span>' +
        '<span class="close-message-toast text-xs py-0.5 px-1.5 cursor-pointer bg-red-500 text-white rounded-[3px] font-medium text-right" style="margin-right: auto">×</span>'+
        '</div>');

    setTimeout(function (){

        $('body > .toast').remove();

    },30000);

    $('.close-message-toast').on('click',function (){

        $(this).parent().remove();

    });
}