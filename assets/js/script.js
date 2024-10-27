$(document).ready(function ()
{
    $(document).ajaxSend(function()
    {
        $("#loader-overlay").toggleClass('hidden flex');
    });

    $(document).ajaxComplete(function ()
    {
        setTimeout(function()
        {
            $("#loader-overlay").toggleClass('hidden flex');
        },500);
    });
});