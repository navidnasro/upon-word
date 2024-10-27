/**
 * Does the sharing functionality
 * 1.saving page link to clipboard
 *
 * @instructions
 * button must have
 * 1."copy-to-clipboard" class
 */

$(document).ready(function ()
{
    $('.copy-to-clipboard').each(function ()
    {
        $(this).on('click',function ()
        {
            var dataToCopy = $(this).attr('data-to-copy');

            navigator.clipboard.writeText(dataToCopy);

            fireMessage('در کلیپبورد ذخیره شد','success');
        });
    });
});